<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ScheduledOrderBatch;
use App\Models\ScheduledOrderItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ExecuteScheduledOrderBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $batchId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $batchId)
    {
        $this->batchId = $batchId;
        // Route this job to a dedicated queue by default so it isn't blocked by unrelated jobs
        $this->onQueue('scheduled');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Load and lock the batch
        DB::transaction(function () {
            /** @var ScheduledOrderBatch|null $batch */
            $batch = ScheduledOrderBatch::where('id', $this->batchId)->lockForUpdate()->first();
            if (!$batch) { return; }
            if (in_array($batch->status, ['completed','canceled'])) { return; }

            // Transition to processing if scheduled/failed/partial
            if (!in_array($batch->status, ['processing'])) {
                $batch->update(['status' => 'processing', 'started_at' => now()]);
            }
        });

        // Work outside the first transaction to avoid long locks
        $batch = ScheduledOrderBatch::with(['buyer','items' => function($q){
            $q->whereIn('status', ['pending','failed']);
        }, 'items.product', 'items.seller'])->find($this->batchId);
        if (!$batch) { return; }

        $buyer = $batch->buyer; /** @var User $buyer */
        $autoPaid = (bool)$batch->auto_paid;

        $groups = $batch->items->groupBy('seller_id');
        $createdCount = 0; $failedCount = 0;

        foreach ($groups as $sellerId => $items) {
            $result = $this->createOrderForSellerGroup($batch, $buyer, (int)$sellerId, $items->all(), $autoPaid);
            $createdCount += $result['created'] ?? 0;
            $failedCount += $result['failed'] ?? 0;
        }

        // Finalize batch status
        $newStatus = 'completed';
        if ($failedCount > 0 && $createdCount > 0) { $newStatus = 'partial'; }
        if ($failedCount > 0 && $createdCount === 0) { $newStatus = 'failed'; }

        $batch->update([
            'status' => $newStatus,
            'finished_at' => now(),
        ]);
    }

    /**
     * Create order for a seller group.
     * @param ScheduledOrderBatch $batch
     * @param User $buyer
     * @param int $sellerId
     * @param ScheduledOrderItem[] $items
     * @param bool $autoPaid
     * @return array{created:int,failed:int}
     */
    protected function createOrderForSellerGroup(ScheduledOrderBatch $batch, User $buyer, int $sellerId, array $items, bool $autoPaid): array
    {
        $created = 0; $failed = 0;
        $seller = User::find($sellerId);
        if (!$seller || !$seller->isSeller()) {
            foreach ($items as $it) { $it->update(['status' => 'failed', 'error_message' => 'Seller tidak valid']); $failed++; }
            return compact('created','failed');
        }

        try {
            DB::transaction(function () use ($batch, $buyer, $seller, $items, $autoPaid, &$created, &$failed) {
                $subtotal = 0; $discountTotal = 0; $sellerMarginTotal = 0; $grandTotal = 0; $etalaseMarginTotal = 0;

                $order = Order::create([
                    'user_id' => $buyer->id,
                    'purchase_type' => $batch->purchase_type,
                    'external_customer_name' => $batch->external_customer_name,
                    'external_customer_phone' => $batch->external_customer_phone,
                    'address' => $batch->address,
                    'subtotal' => 0,
                    'discount_total' => 0,
                    'grand_total' => 0,
                    'seller_margin_total' => 0,
                    'payment_method' => 'manual_transfer',
                    'payment_status' => 'unpaid',
                    'status' => 'pending',
                    'user_notes' => $batch->user_notes,
                    'from_etalase' => true,
                    'seller_id' => $seller->id,
                    'etalase_margin' => 0,
                ]);

                foreach ($items as $it) {
                    /** @var ScheduledOrderItem $it */
                    $product = Product::find($it->product_id);
                    if (!$product || !$product->isActive()) {
                        $it->update(['status' => 'failed', 'error_message' => 'Produk tidak tersedia']);
                        $failed++;
                        continue;
                    }

                    $qty = (int)$it->quantity;
                    $basePrice = $product->harga_biasa;
                    $sellPrice = $product->harga_jual;

                    // price guard (optional)
                    if ($it->price_cap && $sellPrice > $it->price_cap) {
                        $it->update(['status' => 'failed', 'error_message' => 'Harga melebihi price_cap']);
                        $failed++;
                        continue;
                    }

                    $unitPrice = $sellPrice; // etalase uses sell price
                    $discount = 0;
                    $sellerMargin = 0;

                    if ($buyer->isSeller() && $batch->purchase_type === 'external') {
                        $sellerMargin = $product->getSellerMargin($buyer);
                    }

                    // etalase owner margin
                    $marginPercent = $seller->getLevelMarginPercent();
                    if ($marginPercent) {
                        $etalaseMargin = round($sellPrice * ($marginPercent / 100));
                    } else {
                        $etalaseMargin = max(0, $sellPrice - $basePrice);
                    }
                    $etalaseMarginTotal += $etalaseMargin * $qty;

                    $lineTotal = ($unitPrice - $discount) * $qty;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'base_price' => $basePrice,
                        'sell_price' => $sellPrice,
                        'discount' => $discount,
                        'seller_margin' => $sellerMargin,
                        'line_total' => $lineTotal,
                    ]);

                    $subtotal += $unitPrice * $qty;
                    $discountTotal += $discount * $qty;
                    $sellerMarginTotal += $sellerMargin * $qty;
                    $grandTotal += $lineTotal;

                    // mark item created tentatively; updated after order finalize
                    $it->update(['status' => 'created', 'created_order_id' => $order->id]);
                    $created++;
                }

                // finalize order totals
                $order->update([
                    'subtotal' => $subtotal,
                    'discount_total' => $discountTotal,
                    'seller_margin_total' => $sellerMarginTotal,
                    'grand_total' => $grandTotal,
                    'etalase_margin' => $etalaseMarginTotal,
                ]);

                if ($autoPaid) {
                    // Pre-check stock availability before marking as paid
                    foreach ($order->items as $oi) {
                        $p = Product::find($oi->product_id);
                        if (!$p || $p->stock < $oi->quantity) {
                            throw new \RuntimeException("Stok produk {$oi->product->name} tidak mencukupi (tersedia: " . ($p?->stock ?? 0) . ", diminta: {$oi->quantity}).");
                        }
                    }

                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'packaging',
                        'payment_confirmed_at' => now(),
                        'payment_confirmed_by' => $batch->created_by,
                    ]);

                    // Progress levels
                    $buyer->addTransactionAmount($grandTotal);
                    $seller->addTransactionAmount($grandTotal);

                    // Pay margin to etalase owner
                    if ($order->etalase_margin > 0) {
                        $seller->increment('balance', $order->etalase_margin);
                    }
                    // Reduce stock (after pre-check)
                    foreach ($order->items as $oi) {
                        $p = Product::find($oi->product_id);
                        if ($p) { $p->decrement('stock', $oi->quantity); }
                    }

                    // Notifications
                    $sellerName = $seller->sellerInfo->store_name ?? $seller->full_name;
                    Notification::create([
                        'for_user_id' => $buyer->id,
                        'category' => 'payment',
                        'title' => 'Pembayaran Etalase Disetujui (Terjadwal)',
                        'description' => "Order #{$order->id} dari etalase {$sellerName} telah dibayar otomatis sesuai jadwal. Order masuk tahap dikemas.",
                    ]);

                    if ($order->etalase_margin > 0) {
                        $levelBadge = $seller->getLevelBadge();
                        $marginPercent = $seller->getLevelMarginPercent();
                        $desc = "Margin sebesar Rp" . number_format($order->etalase_margin, 0, ',', '.') . " dari penjualan etalase telah ditambahkan ke saldo Anda. Order #{$order->id} dari {$buyer->full_name}.";
                        $desc .= $marginPercent ? " (Margin {$marginPercent}% karena Anda {$levelBadge})" : " (Margin sesuai selisih harga karena Anda {$levelBadge})";
                        Notification::create([
                            'for_user_id' => $seller->id,
                            'category' => 'payment',
                            'title' => 'Margin Etalase Diterima (Terjadwal)',
                            'description' => $desc,
                        ]);
                    }
                }
            });
        } catch (\Throwable $e) {
            // Mark all items as failed on exception
            foreach ($items as $it) {
                /** @var ScheduledOrderItem $it */
                if ($it->status !== 'created') {
                    $it->update(['status' => 'failed', 'error_message' => substr($e->getMessage(), 0, 500)]);
                    $failed++;
                }
            }
        }

        return compact('created','failed');
    }
}
