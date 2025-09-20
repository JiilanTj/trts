<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Notification;
use App\Models\Product;
use App\Models\OrderItem; // added
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /** List orders (filters by status, payment_status, user) */
    public function index(Request $request)
    {
        $query = Order::with('user','items.product')->latest();
        if ($status = $request->get('status')) { $query->where('status', $status); }
        if ($p = $request->get('payment_status')) { $query->where('payment_status', $p); }
        if ($uid = $request->get('user_id')) { $query->where('user_id', $uid); }
        $orders = $query->paginate(30);
        return view('admin.orders.index', compact('orders'));
    }

    /** Show create form for admin to create an order */
    public function create(Request $request)
    {
        $users = \App\Models\User::users()->orderBy('full_name')->get(['id','full_name']);
        $sellers = \App\Models\User::sellers()->orderBy('full_name')->get(['id','full_name']);
        $products = Product::active()->orderBy('name')->get(['id','name','sell_price','promo_price','purchase_price']);
        return view('admin.orders.create', compact('users','sellers','products'));
    }

    /** Store order created by admin, with optional auto-paid bypass */
    public function store(Request $request)
    {
        $data = $request->validate([
            'buyer_id' => 'required|exists:users,id',
            'purchase_type' => 'required|in:self,external',
            'external_customer_name' => 'nullable|string|max:120',
            'external_customer_phone' => 'nullable|string|max:40',
            'address' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'from_etalase' => 'nullable|boolean',
            'seller_id' => 'nullable|integer|exists:users,id',
            'auto_paid' => 'nullable|boolean',
            'user_notes' => 'nullable|string',
        ]);

        $buyer = \App\Models\User::findOrFail($data['buyer_id']);
        $fromEtalase = (bool)($data['from_etalase'] ?? false);
        $etalaseSeller = null;
        if ($fromEtalase) {
            if (empty($data['seller_id'])) {
                return back()->withErrors('Seller etalase wajib diisi.')->withInput();
            }
            $etalaseSeller = \App\Models\User::find($data['seller_id']);
            if (!$etalaseSeller || !$etalaseSeller->isSeller()) {
                return back()->withErrors('Seller etalase tidak valid.')->withInput();
            }
        }

        $order = DB::transaction(function () use ($request, $data, $buyer, $fromEtalase, $etalaseSeller) {
            $subtotal = 0; $discountTotal = 0; $sellerMarginTotal = 0; $grandTotal = 0; $etalaseMarginTotal = 0;

            $orderData = [
                'user_id' => $buyer->id,
                'purchase_type' => $data['purchase_type'],
                'external_customer_name' => $data['external_customer_name'] ?? null,
                'external_customer_phone' => $data['external_customer_phone'] ?? null,
                'address' => $data['address'],
                'subtotal' => 0,
                'discount_total' => 0,
                'grand_total' => 0,
                'seller_margin_total' => 0,
                'payment_method' => 'manual_transfer',
                'payment_status' => 'unpaid',
                'status' => 'pending',
                'user_notes' => $data['user_notes'] ?? null,
            ];

            if ($fromEtalase) {
                $orderData['from_etalase'] = true;
                $orderData['seller_id'] = $etalaseSeller->id;
                $orderData['etalase_margin'] = 0;
                $sellerName = $etalaseSeller->sellerInfo->store_name ?? $etalaseSeller->full_name;
                $orderData['user_notes'] = trim(($orderData['user_notes'] ? $orderData['user_notes'] . "\n\n" : '') . 'Dibuat admin via etalase: ' . $sellerName);
            }

            $order = Order::create($orderData);

            foreach ($data['items'] as $row) {
                $product = Product::find($row['product_id']);
                if (!$product || !$product->isActive()) {
                    abort(422, 'Produk tidak tersedia.');
                }
                $qty = (int)$row['quantity'];
                $basePrice = $product->harga_biasa;
                $sellPrice = $product->harga_jual;

                // Unit price selection
                if ($fromEtalase) {
                    $unitPrice = $sellPrice; // etalase uses sell price
                } else {
                    $unitPrice = $product->getApplicablePrice($buyer, $data['purchase_type']);
                }

                $discount = 0;
                $sellerMargin = 0;

                if ($buyer->isSeller() && $data['purchase_type'] === 'external') {
                    $sellerMargin = $product->getSellerMargin($buyer);
                }

                if ($fromEtalase && $etalaseSeller) {
                    $marginPercent = $etalaseSeller->getLevelMarginPercent();
                    if ($marginPercent) {
                        $etalaseMargin = round($sellPrice * ($marginPercent / 100));
                    } else {
                        $etalaseMargin = max(0, $sellPrice - $basePrice);
                    }
                    $etalaseMarginTotal += $etalaseMargin * $qty;
                }

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
            }

            $order->update([
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'seller_margin_total' => $sellerMarginTotal,
                'grand_total' => $grandTotal,
                'etalase_margin' => $fromEtalase ? $etalaseMarginTotal : 0,
            ]);

            // Handle auto-paid
            if ($request->boolean('auto_paid')) {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'packaging',
                    'payment_confirmed_at' => now(),
                    'payment_confirmed_by' => $request->user()->id,
                ]);

                // Process external seller margin (buyer is seller doing external purchase)
                $order->processSellerMarginPayout();

                // Handle etalase settlements and stock
                if ($fromEtalase && $etalaseSeller) {
                    // level progression
                    $buyer->addTransactionAmount($grandTotal);
                    $etalaseSeller->addTransactionAmount($grandTotal);
                    // pay margin to etalase owner
                    if ($order->etalase_margin > 0) {
                        $etalaseSeller->increment('balance', $order->etalase_margin);
                    }
                    // reduce stock now
                    foreach ($order->items as $it) {
                        $p = Product::find($it->product_id);
                        if ($p) { $p->decrement('stock', $it->quantity); }
                    }

                    // Notifications
                    $sellerName = $etalaseSeller->sellerInfo->store_name ?? $etalaseSeller->full_name;
                    Notification::create([
                        'for_user_id' => $buyer->id,
                        'category' => 'payment',
                        'title' => 'Pembayaran Etalase Disetujui',
                        'description' => "Order #{$order->id} dari etalase {$sellerName} telah dibayar oleh admin. Order masuk tahap dikemas.",
                    ]);

                    if ($order->etalase_margin > 0) {
                        $levelBadge = $etalaseSeller->getLevelBadge();
                        $marginPercent = $etalaseSeller->getLevelMarginPercent();
                        $desc = "Margin sebesar Rp" . number_format($order->etalase_margin, 0, ',', '.') . " dari penjualan etalase telah ditambahkan ke saldo Anda. Order #{$order->id} dari {$buyer->full_name}.";
                        $desc .= $marginPercent ? " (Margin {$marginPercent}% karena Anda {$levelBadge})" : " (Margin sesuai selisih harga karena Anda {$levelBadge})";
                        Notification::create([
                            'for_user_id' => $etalaseSeller->id,
                            'category' => 'payment',
                            'title' => 'Margin Etalase Diterima',
                            'description' => $desc,
                        ]);
                    }
                } else {
                    // Normal order notifications
                    Notification::create([
                        'for_user_id' => $buyer->id,
                        'category' => 'payment',
                        'title' => 'Pembayaran Disetujui',
                        'description' => "Pembayaran untuk order #{$order->id} telah disetujui admin. Order akan segera dikemas.",
                    ]);
                }
            }

            return $order->fresh(['items.product','user','seller']);
        });

        return redirect()->route('admin.orders.show', $order)->with('success', $request->boolean('auto_paid') ? 'Order dibuat & ditandai lunas.' : 'Order berhasil dibuat.');
    }

    /** Show single order */
    public function show(Order $order)
    {
        $order->load('user','items.product','confirmer');
        return view('admin.orders.show', compact('order'));
    }

    /** Approve payment (when waiting_confirmation) */
    public function approvePayment(Request $request, Order $order)
    {
        if ($order->payment_status !== 'waiting_confirmation') {
            return back()->withErrors(['order' => 'Status pembayaran tidak valid untuk approve.']);
        }
        
        DB::transaction(function () use ($order, $request) {
            $order->update([
                'payment_status' => 'paid',
                'payment_confirmed_at' => now(),
                'payment_confirmed_by' => $request->user()->id,
                // Move to packaging after payment approval
                'status' => 'packaging',
            ]);
            
            // Process seller margin payout and credit score increase
            $order->processSellerMarginPayout();
            
            // Handle etalase orders
            if ($order->from_etalase && $order->seller && $order->etalase_margin > 0) {
                // Calculate proper margin based on etalase owner's level
                $etalaseOwner = $order->seller;
                $marginPercent = $etalaseOwner->getLevelMarginPercent();
                
                $actualMargin = 0;
                // Calculate margin for each item based on etalase owner's level
                foreach ($order->items as $item) {
                    if ($marginPercent) {
                        // Use percentage margin based on etalase owner's level
                        $itemMargin = round($item->sell_price * ($marginPercent / 100)) * $item->quantity;
                    } else {
                        // Level 1: Use admin-set margin (difference between sell_price and base_price)
                        $itemMargin = max(0, ($item->sell_price - $item->base_price)) * $item->quantity;
                    }
                    $actualMargin += $itemMargin;
                }
                
                // 1. Etalase owner gets level progression from total sales amount
                $order->seller->addTransactionAmount($order->grand_total);
                
                // 2. Add proper margin to etalase owner's balance
                $order->seller->increment('balance', $actualMargin);
                
                // 3. Update the order with actual margin used
                $order->update(['etalase_margin' => $actualMargin]);
                
                // 4. Notification for etalase owner
                $sellerName = $order->seller->sellerInfo->store_name ?? $order->seller->full_name;
                $levelBadge = $etalaseOwner->getLevelBadge();
                
                $marginDescription = "Margin sebesar Rp" . number_format($actualMargin, 0, ',', '.') . " dari penjualan etalase telah ditambahkan ke saldo Anda. Order #{$order->id} dari {$order->user->full_name}.";
                
                if ($marginPercent) {
                    $marginDescription .= " (Margin {$marginPercent}% karena Anda {$levelBadge})";
                } else {
                    $marginDescription .= " (Margin sesuai selisih harga karena Anda {$levelBadge})";
                }
                
                \App\Models\Notification::create([
                    'for_user_id' => $order->seller_id,
                    'category' => 'payment',
                    'title' => 'Margin Etalase Diterima',
                    'description' => $marginDescription,
                ]);
            }
            
            // Track transaction amount and check level upgrade for buyer
            $order->user->addTransactionAmount($order->grand_total);
            
            // Create notification for payment approval
            if ($order->from_etalase && $order->seller) {
                $sellerName = $order->seller->sellerInfo->store_name ?? $order->seller->full_name;
                $this->createOrderNotification($order, 'payment', 'Pembayaran Etalase Disetujui', 
                    "Pembayaran untuk order #{$order->id} dari etalase {$sellerName} telah disetujui. Order akan segera dikemas.");
            } else {
                $this->createOrderNotification($order, 'payment', 'Pembayaran Disetujui', 
                    "Pembayaran untuk order #{$order->id} telah disetujui. Order akan segera dikemas.");
            }
                
            // Create additional notification if margin was paid out
            if ($order->purchase_type === 'external' && $order->seller_margin_total > 0) {
                $this->createOrderNotification($order, 'payment', 'Margin Seller Diterima', 
                    "Margin sebesar Rp" . number_format($order->seller_margin_total, 0, ',', '.') . 
                    " telah ditambahkan ke saldo Anda. Credit score +5 poin!");
            }
        });
        
        return back()->with('success', 'Pembayaran disetujui, order masuk tahap dikemas.');
    }

    /** Reject / request reupload proof */
    public function rejectPayment(Request $request, Order $order)
    {
        if ($order->payment_status !== 'waiting_confirmation') {
            return back()->withErrors(['order' => 'Status pembayaran tidak valid untuk reject.']);
        }
        $data = $request->validate([
            'admin_notes' => 'nullable|string',
        ]);
        
        $order->update([
            'payment_status' => 'rejected',
            'status' => 'pending',
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);
        
        // Create notification for payment rejection
        $rejectionReason = $data['admin_notes'] ? " Alasan: " . $data['admin_notes'] : "";
        $this->createOrderNotification($order, 'payment', 'Pembayaran Ditolak', 
            "Pembayaran untuk order #{$order->id} ditolak. Silakan upload ulang bukti pembayaran yang valid.{$rejectionReason}");
            
        return back()->with('success', 'Pembayaran ditolak. User diminta upload ulang.');
    }

    /** Transition workflow status: packaging -> shipped -> delivered -> completed */
    public function advanceStatus(Request $request, Order $order)
    {
        $map = [
            'packaging' => 'shipped',
            'shipped' => 'delivered',
            'delivered' => 'completed',
        ];
        
        $statusLabels = [
            'shipped' => 'Dikirim',
            'delivered' => 'Diterima', 
            'completed' => 'Selesai',
        ];
        
        $next = $map[$order->status] ?? null;
        if (!$next) {
            return back()->withErrors(['order' => 'Tidak bisa lanjut status dari tahap ini.']);
        }
        
        $order->update(['status' => $next]);
        
        // Create notification for status change
        $statusLabel = $statusLabels[$next] ?? ucfirst($next);
        $descriptions = [
            'shipped' => "Order #{$order->id} telah dikirim dan sedang dalam perjalanan ke alamat tujuan.",
            'delivered' => "Order #{$order->id} telah sampai di alamat tujuan. Silakan konfirmasi penerimaan barang.",
            'completed' => "Order #{$order->id} telah selesai. Terima kasih telah berbelanja!",
        ];
        
        $this->createOrderNotification($order, 'order', "Order {$statusLabel}", 
            $descriptions[$next] ?? "Status order #{$order->id} berubah menjadi {$statusLabel}.");
            
        return back()->with('success', 'Status order berubah menjadi ' . $next . '.');
    }

    /** Manually set to cancelled */
    public function cancel(Request $request, Order $order)
    {
        if (in_array($order->status, ['completed','cancelled'])) {
            return back()->withErrors(['order' => 'Order sudah final.']);
        }
        $order->update(['status' => 'cancelled']);
        
        // Create notification for user
        $this->createOrderNotification($order, 'order', 'Order Dibatalkan', 
            "Order #{$order->id} telah dibatalkan oleh admin.");
            
        return back()->with('success', 'Order dibatalkan.');
    }

    /** Refund for balance-paid orders */
    public function refund(Request $request, Order $order)
    {
        if (!$order->isBalancePayment() || $order->payment_status !== 'paid') {
            return back()->withErrors(['refund' => 'Order tidak valid untuk refund.']);
        }
        if (in_array($order->status, ['shipped','delivered','completed'])) {
            return back()->withErrors(['refund' => 'Tidak bisa refund setelah pengiriman dimulai.']);
        }
        DB::transaction(function () use ($order, $request) {
            $user = $order->user()->lockForUpdate()->first();
            $user->increment('balance', $order->grand_total);
            $order->update([
                'payment_status' => 'refunded',
                'status' => 'cancelled',
                'payment_refunded_at' => now(),
                'payment_refunded_by' => $request->user()->id,
            ]);
            $this->createOrderNotification($order, 'payment', 'Order Direfund', 
                "Order #{$order->id} telah direfund. Saldo dikembalikan sebesar Rp" . number_format($order->grand_total, 0, ',', '.') . ".");
        });
        return back()->with('success', 'Refund berhasil.');
    }

    /** Update order status directly to any valid status */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,awaiting_confirmation,packaging,shipped,delivered,completed,cancelled',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Prevent certain impossible transitions
        if ($oldStatus === 'completed' && $newStatus !== 'completed') {
            return back()->withErrors(['status' => 'Order yang sudah selesai tidak dapat diubah statusnya.']);
        }

        if ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
            return back()->withErrors(['status' => 'Order yang sudah dibatalkan tidak dapat diubah statusnya.']);
        }

        // Update status
        $order->update([
            'status' => $newStatus,
            'admin_notes' => $request->admin_notes
        ]);

        // Create notification for status change
        $statusLabels = Order::statusOptions();
        $statusLabel = $statusLabels[$newStatus] ?? ucfirst($newStatus);
        
        $descriptions = [
            'pending' => "Order #{$order->id} dikembalikan ke status pending.",
            'awaiting_confirmation' => "Order #{$order->id} menunggu konfirmasi pembayaran.",
            'packaging' => "Order #{$order->id} sedang dalam tahap pengemasan.",
            'shipped' => "Order #{$order->id} telah dikirim dan sedang dalam perjalanan ke alamat tujuan.",
            'delivered' => "Order #{$order->id} telah sampai di alamat tujuan. Silakan konfirmasi penerimaan barang.",
            'completed' => "Order #{$order->id} telah selesai. Terima kasih telah berbelanja!",
            'cancelled' => "Order #{$order->id} telah dibatalkan oleh admin.",
        ];

        $this->createOrderNotification($order, 'order', "Order {$statusLabel}", 
            $descriptions[$newStatus] ?? "Status order #{$order->id} berubah menjadi {$statusLabel}.");

        return back()->with('success', "Status order berhasil diubah dari {$statusLabels[$oldStatus]} menjadi {$statusLabel}.");
    }

    /**
     * Create notification for order-related actions
     */
    private function createOrderNotification(Order $order, string $category, string $title, string $description): void
    {
        Notification::create([
            'for_user_id' => $order->user_id,
            'category' => $category,
            'title' => $title,
            'description' => $description,
        ]);
    }
}
