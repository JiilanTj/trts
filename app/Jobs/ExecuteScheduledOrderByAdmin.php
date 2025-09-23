<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\OrderByAdmin;
use App\Models\Product;
use App\Models\ScheduledOrderByAdmin;
use App\Models\ScheduledOrderByAdminItem;
use App\Models\StoreShowcase;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ExecuteScheduledOrderByAdmin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $scheduleId;

    public function __construct(int $scheduleId)
    {
        $this->scheduleId = $scheduleId;
        $this->onQueue('scheduled');
    }

    public function handle(): void
    {
        // Claim atomically: only the first job that changes status to `processing` may proceed
        $claimed = false;
        DB::transaction(function() use (&$claimed) {
            $row = ScheduledOrderByAdmin::where('id', $this->scheduleId)->lockForUpdate()->first();
            if (!$row) { return; }
            // If already being processed or finished/canceled, do not proceed
            if (in_array($row->status, ['processing', 'completed', 'canceled'])) { return; }
            // Otherwise claim it
            $row->update(['status' => 'processing', 'started_at' => now()]);
            $claimed = true;
        });
        if (!$claimed) {
            // Another worker already claimed or finished it
            return;
        }

        $row = ScheduledOrderByAdmin::with('items')->find($this->scheduleId);
        if (!$row) { return; }

        try {
            DB::transaction(function () use ($row) {
                /** @var User|null $seller */
                $seller = User::find($row->user_id);
                if (!$seller || !$seller->isSeller()) {
                    throw new \RuntimeException('Seller tidak valid.');
                }

                $createdOrderIds = [];

                // Multi-item path
                $items = $row->items;
                if ($items && $items->count() > 0) {
                    foreach ($items as $it) {
                        /** @var StoreShowcase|null $showcase */
                        $showcase = StoreShowcase::find($it->store_showcase_id);
                        if (!$showcase || (int)$showcase->user_id !== (int)$row->user_id) {
                            throw new \RuntimeException('Etalase tidak dimiliki oleh seller.');
                        }

                        /** @var Product|null $product */
                        $product = Product::find($it->product_id);
                        if (!$product || (int)$showcase->product_id !== (int)$product->id) {
                            throw new \RuntimeException('Produk tidak sesuai dengan etalase.');
                        }

                        $unitPrice = (int) ($product->harga_jual ?? $product->sell_price);
                        $totalPrice = $unitPrice * (int)$it->quantity;

                        $order = OrderByAdmin::create([
                            'admin_id' => (int)$row->created_by,
                            'user_id' => (int)$row->user_id,
                            'store_showcase_id' => (int)$it->store_showcase_id,
                            'product_id' => (int)$it->product_id,
                            'quantity' => (int)$it->quantity,
                            'unit_price' => $unitPrice,
                            'total_price' => $totalPrice,
                            'status' => OrderByAdmin::STATUS_PENDING,
                        ]);

                        $createdOrderIds[] = $order->id;
                        $it->update(['created_order_id' => $order->id]);
                    }
                } else {
                    // Backward-compat: single item fields
                    /** @var StoreShowcase|null $showcase */
                    $showcase = StoreShowcase::find($row->store_showcase_id);
                    if (!$showcase || (int)$showcase->user_id !== (int)$row->user_id) {
                        throw new \RuntimeException('Etalase tidak dimiliki oleh seller.');
                    }

                    /** @var Product|null $product */
                    $product = Product::find($row->product_id);
                    if (!$product || (int)$showcase->product_id !== (int)$product->id) {
                        throw new \RuntimeException('Produk tidak sesuai dengan etalase.');
                    }

                    $unitPrice = (int) ($product->harga_jual ?? $product->sell_price);
                    $totalPrice = $unitPrice * (int)$row->quantity;

                    $order = OrderByAdmin::create([
                        'admin_id' => (int)$row->created_by,
                        'user_id' => (int)$row->user_id,
                        'store_showcase_id' => (int)$row->store_showcase_id,
                        'product_id' => (int)$row->product_id,
                        'quantity' => (int)$row->quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'status' => OrderByAdmin::STATUS_PENDING,
                    ]);

                    $createdOrderIds[] = $order->id;
                }

                $row->update([
                    'status' => 'completed',
                    'finished_at' => now(),
                    'created_order_id' => $createdOrderIds[0] ?? null, // keep first for quick link
                ]);

                // Notify seller one-time (neutral wording)
                try {
                    $orderLabel = count($createdOrderIds) > 1 ? (count($createdOrderIds) . ' order') : ('Order #' . ($createdOrderIds[0] ?? ''));
                    Notification::create([
                        'for_user_id' => (int)$row->user_id,
                        'category' => 'order',
                        'title' => 'Order Dibuat',
                        'description' => $orderLabel . ' telah dibuat.',
                    ]);
                } catch (\Throwable $e) { /* ignore */ }
            });
        } catch (\Throwable $e) {
            ScheduledOrderByAdmin::where('id', $this->scheduleId)->update([
                'status' => 'failed',
                'finished_at' => now(),
                'error_message' => substr($e->getMessage(), 0, 500),
            ]);
        }
    }
}
