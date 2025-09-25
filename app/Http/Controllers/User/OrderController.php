<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting; // added
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * List orders for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $status = $request->get('status', 'all');
        $paymentStatus = $request->get('payment_status');
        
        $query = Order::with('items.product')->where('user_id', $user->id);
        
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }
        
        if ($paymentStatus && $paymentStatus !== 'all') {
            $query->where('payment_status', $paymentStatus);
        }
        
        $orders = $query->latest()->paginate(15)->appends($request->query());
        
        // Get counts for each status for the filter menu
        $statusCounts = [
            'all' => Order::where('user_id', $user->id)->count(),
        ];
        
        foreach (Order::statusOptions() as $statusKey => $statusLabel) {
            $statusCounts[$statusKey] = Order::where('user_id', $user->id)
                ->where('status', $statusKey)
                ->count();
        }
        
        // Get status options with labels
        $statusOptions = array_merge(['all' => 'Semua'], Order::statusOptions());
        
        return view('user.orders.index', compact('orders', 'statusCounts', 'status', 'statusOptions'));
    }

    /** Show create form */
    public function create(Request $request)
    {
        $products = Product::active()->get();
        $prefill = null;
        if ($request->filled('product_id')) {
            $prefill = $products->firstWhere('id', (int)$request->input('product_id'));
        }
        $purchaseType = $request->input('purchase_type','self');
        if(!in_array($purchaseType,['self','external'])) { $purchaseType = 'self'; }
        $singleMode = (bool)$prefill; // if coming from product page, go straight to single checkout
        $user = $request->user()->loadMissing('detail');

        // Check for etalase parameters from session
        $etalaseProductId = session('etalase_product_id');
        $etalaseSellerId = session('etalase_seller_id');
        $fromEtalase = session('from_etalase', false);
        
        \Log::info('OrderController create - checking etalase session', [
            'etalase_product_id' => $etalaseProductId,
            'etalase_seller_id' => $etalaseSellerId,
            'from_etalase' => $fromEtalase,
            'session_all' => session()->all(),
        ]);
        
        $etalaseInfo = null;
        if ($fromEtalase && $etalaseProductId && $etalaseSellerId) {
            $etalaseProduct = Product::find($etalaseProductId);
            $etalaseSeller = \App\Models\User::find($etalaseSellerId);
            
            if ($etalaseProduct && $etalaseSeller) {
                $prefill = $etalaseProduct;
                $singleMode = true;
                $purchaseType = 'self'; // Force self for etalase purchases
                
                $etalaseInfo = [
                    'product' => $etalaseProduct,
                    'seller' => $etalaseSeller,
                    'seller_id' => $etalaseSeller->id,
                    'seller_name' => $etalaseSeller->sellerInfo->store_name ?? $etalaseSeller->full_name,
                    'margin' => $etalaseProduct->harga_jual - $etalaseProduct->harga_biasa,
                ];
            }
        }

        // Check for wholesale products from session
        $wholesaleProducts = session('wholesale_products', []);
        $wholesaleMode = !empty($wholesaleProducts) && !$fromEtalase; // Disable wholesale mode for etalase
        
        // If wholesale products exist, get the actual product data
        $prefilledProducts = [];
        if ($wholesaleMode) {
            $productIds = array_column($wholesaleProducts, 'product_id');
            $productData = Product::whereIn('id', $productIds)->get()->keyBy('id');
            
            foreach ($wholesaleProducts as $item) {
                $product = $productData->get($item['product_id']);
                if ($product) {
                    $prefilledProducts[] = [
                        'product' => $product,
                        'quantity' => $item['quantity']
                    ];
                }
            }
        }

        // Self purchase prefill data (editable in UI)
        $selfPrefill = [
            'name' => $user->full_name,
            'phone' => $user->detail->phone ?? $user->detail->secondary_phone ?? '',
            'address' => $user->detail->address_line ?? '',
        ];

        return view('user.orders.create', compact('products','prefill','purchaseType','singleMode','selfPrefill','wholesaleMode','prefilledProducts','etalaseInfo','fromEtalase'));
    }

    /**
     * Store a new order with support for balance payment.
     */
    public function store(Request $request)
    {
        foreach (['external_customer_name','external_customer_phone','address'] as $field) {
            $val = $request->input($field);
            if (is_array($val)) {
                $flat = trim(implode(' ', array_map(fn($v)=> is_scalar($v)? $v : '', $val)));
                $request->merge([$field => $flat]);
            }
        }

        $user = $request->user()->loadMissing('detail');
        $data = $request->validate([
            'purchase_type' => 'required|in:self,external',
            'external_customer_name' => 'nullable|string|max:120',
            'external_customer_phone' => 'nullable|string|max:40',
            'address' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'user_notes' => 'nullable|string',
            'payment_method' => 'required|in:manual_transfer,balance',
            'from_etalase' => 'nullable|boolean',
            'seller_id' => 'nullable|integer|exists:users,id',
            'etalase_margin' => 'nullable|numeric',
        ]);

        // Check for etalase purchase from request data first, then session
        $fromEtalase = $data['from_etalase'] ?? session('from_etalase', false);
        $etalaseSellerId = $data['seller_id'] ?? session('etalase_seller_id');
        $etalaseProductId = session('etalase_product_id');
        
        $etalaseSeller = null;
        if ($fromEtalase && $etalaseSellerId) {
            $etalaseSeller = \App\Models\User::find($etalaseSellerId);
            if (!$etalaseSeller || !$etalaseSeller->isSeller()) {
                return back()->withErrors('Seller etalase tidak valid.')->withInput();
            }
            
            // For etalase purchases from shared link, skip product validation
            // since user might order different quantities or products
            if ($etalaseProductId) {
                // Verify the order contains only the etalase product (session-based)
                if (count($data['items']) !== 1 || $data['items'][0]['product_id'] != $etalaseProductId) {
                    return back()->withErrors('Order etalase hanya boleh berisi produk dari etalase yang dipilih.')->withInput();
                }
            }
        }

        if ($data['purchase_type'] === 'external' && !$user->isSeller()) {
            return back()->withErrors(['purchase_type' => 'Hanya seller yang dapat membeli untuk pelanggan eksternal.'])->withInput();
        }

        if ($data['purchase_type'] === 'external') {
            if (empty($data['external_customer_name'])) {
                return back()->withErrors(['external_customer_name' => 'Nama pelanggan wajib diisi untuk pembelian pelanggan.'])->withInput();
            }
            if (empty($data['external_customer_phone'])) {
                return back()->withErrors(['external_customer_phone' => 'No. telepon pelanggan wajib diisi untuk pembelian pelanggan.'])->withInput();
            }
        }

        if ($data['purchase_type'] === 'self') {
            $data['external_customer_name'] = $data['external_customer_name'] ?: $user->full_name;
            $data['external_customer_phone'] = $data['external_customer_phone'] ?: ($user->detail->phone ?? $user->detail->secondary_phone ?? null);
        }

        $purchaseType = $data['purchase_type'];
        $itemsInput = $data['items'];
        $paymentMethod = $data['payment_method'];
        $etalaseMarginTotal = 0; // Initialize variable
        $finalEtalaseSeller = null; // Store final etalase seller for notifications

        $order = DB::transaction(function () use ($user, $data, $itemsInput, $purchaseType, $paymentMethod, $fromEtalase, $etalaseSeller, &$etalaseMarginTotal, &$finalEtalaseSeller) {
            $subtotal = 0; $discountTotal = 0; $marginTotal = 0; $grandTotal = 0;
            $etalaseMarginTotal = 0;

            $orderData = [
                'user_id' => $user->id,
                'purchase_type' => $purchaseType,
                'external_customer_name' => $data['external_customer_name'] ?? null,
                'external_customer_phone' => $data['external_customer_phone'] ?? null,
                'address' => $data['address'] ?? null,
                'subtotal' => 0,
                'discount_total' => 0,
                'grand_total' => 0,
                'seller_margin_total' => 0,
                'payment_method' => $paymentMethod,
                'payment_status' => 'unpaid', // temp
                'status' => 'pending', // temp
                'user_notes' => $data['user_notes'] ?? null,
            ];
            
            // Add etalase fields if from etalase
            if ($fromEtalase) {
                if ($etalaseSeller) {
                    $orderData['seller_id'] = $etalaseSeller->id;
                    $finalEtalaseSeller = $etalaseSeller; // Store for notifications
                }
                $orderData['from_etalase'] = true;
                $orderData['etalase_margin'] = 0; // Will be calculated below
                
                // Update user notes to include etalase info
                if ($etalaseSeller) {
                    $sellerName = $etalaseSeller->sellerInfo->store_name ?? $etalaseSeller->full_name;
                    $orderData['user_notes'] = ($data['user_notes'] ?? '') . "\n\nDibeli dari etalase: " . $sellerName;
                    $orderData['user_notes'] = trim($orderData['user_notes']);
                }
            }

            $order = Order::create($orderData);

            foreach ($itemsInput as $row) {
                $product = Product::find($row['product_id']);
                if (!$product || !$product->isActive()) {
                    abort(422, 'Produk tidak tersedia.');
                }
                $qty = (int)$row['quantity'];
                $basePrice = $product->harga_biasa;
                $sellPrice = $product->harga_jual;
                
                // For etalase purchases, use sell price as unit price
                if ($fromEtalase) {
                    $unitPrice = $sellPrice; // Use harga_jual for etalase
                } else {
                    $unitPrice = $product->getApplicablePrice($user, $purchaseType);
                }
                
                $discount = 0;
                $sellerMargin = 0;
                
                // Calculate seller margin based on user level for external purchases
                if ($user->isSeller() && $purchaseType === 'external') {
                    $sellerMargin = $product->getSellerMargin($user);
                }
                
                // Calculate etalase margin if from etalase
                if ($fromEtalase && $etalaseSeller) {
                    $marginPercent = $etalaseSeller->getLevelMarginPercent();
                    if ($marginPercent) {
                        // Use percentage margin based on etalase owner's level
                        $etalaseMargin = round($sellPrice * ($marginPercent / 100));
                    } else {
                        // Level 1: Use admin-set margin (difference between sell price and base price)
                        $etalaseMargin = max(0, $sellPrice - $basePrice);
                    }
                    $etalaseMarginTotal += $etalaseMargin * $qty; // Total margin for this product
                }
                
                $lineTotal = ($unitPrice - $discount) * $qty;

                $orderItemData = [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'base_price' => $basePrice,
                    'sell_price' => $sellPrice,
                    'discount' => $discount,
                    'seller_margin' => $sellerMargin,
                    'line_total' => $lineTotal,
                ];

                OrderItem::create($orderItemData);

                $subtotal += $unitPrice * $qty;
                $discountTotal += $discount * $qty;
                $marginTotal += $sellerMargin * $qty;
                $grandTotal += $lineTotal;
            }

            // Balance validation & deduction if needed
            if ($paymentMethod === 'balance') {
                if ($user->balance < $grandTotal) {
                    abort(422, 'Saldo tidak cukup untuk pembayaran.');
                }
            }

            $updateData = [
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'seller_margin_total' => $marginTotal,
                'grand_total' => $grandTotal,
            ];
            
            // Add etalase margin to update data
            if ($fromEtalase) {
                $updateData['etalase_margin'] = $etalaseMarginTotal;
            }
            
            $order->update($updateData);

            if ($paymentMethod === 'balance') {
                // Deduct balance immediately and mark paid & move workflow forward
                $user->decrement('balance', $grandTotal);
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'packaging',
                    'payment_confirmed_at' => now(),
                    'payment_confirmed_by' => $user->id, // self auto-confirm context
                ]);
                
                // Process seller margin payout and credit score increase
                $order->processSellerMarginPayout();
                
                // Track transaction amount and check level upgrade
                if ($fromEtalase) {
                    // For etalase purchases:
                    // 1. Buyer gets level progression from their total purchase (what they actually paid)
                    $user->addTransactionAmount($grandTotal);
                    
                    // 2. Etalase owner gets level progression from total sales amount (grand total)
                    if ($etalaseSeller) {
                        $etalaseSeller->addTransactionAmount($grandTotal);
                        
                        // 3. Add margin to etalase owner's balance
                        $etalaseSeller->increment('balance', $etalaseMarginTotal);
                    }
                    
                    // 4. Update product stock (only for balance payments that are immediately processed)
                    foreach ($itemsInput as $row) {
                        $product = Product::find($row['product_id']);
                        if ($product) {
                            $product->decrement('stock', (int)$row['quantity']);
                        }
                    }
                    
                } else {
                    // Normal purchase - user gets level progression
                    $user->addTransactionAmount($grandTotal);
                }
            } else {
                $order->update([
                    'payment_status' => 'unpaid',
                    'status' => 'pending',
                ]);
            }

            return $order->fresh(['items.product']);
        });

        if ($order->payment_method === 'balance') {
            if ($fromEtalase && $finalEtalaseSeller) {
                $sellerName = $finalEtalaseSeller->sellerInfo->store_name ?? $finalEtalaseSeller->full_name;
                
                // Notification for buyer
                Notification::create([
                    'for_user_id' => $user->id,
                    'category' => 'payment',
                    'title' => 'Pembayaran Etalase Berhasil',
                    'description' => "Order #{$order->id} dari etalase {$sellerName} telah dibayar otomatis menggunakan saldo Anda. Order masuk tahap dikemas.",
                ]);
                
                // Notification for etalase owner
                if ($etalaseMarginTotal > 0) {
                    $levelBadge = $finalEtalaseSeller->getLevelBadge();
                    $marginPercent = $finalEtalaseSeller->getLevelMarginPercent();
                    
                    $marginDescription = "Margin sebesar Rp" . number_format($etalaseMarginTotal, 0, ',', '.') . " dari penjualan etalase telah ditambahkan ke saldo Anda. Order #{$order->id} dari {$user->full_name}.";
                    
                    if ($marginPercent) {
                        $marginDescription .= " (Margin {$marginPercent}% karena Anda {$levelBadge})";
                    } else {
                        $marginDescription .= " (Margin dihitung khusus untuk level Anda: {$levelBadge})"; // updated fallback
                    }
                    
                    Notification::create([
                        'for_user_id' => $finalEtalaseSeller->id,
                        'category' => 'payment',
                        'title' => 'Margin Etalase Diterima',
                        'description' => $marginDescription,
                    ]);
                }
            } else {
                Notification::create([
                    'for_user_id' => $user->id,
                    'category' => 'payment',
                    'title' => 'Pembayaran Saldo Berhasil',
                    'description' => "Order #{$order->id} telah dibayar otomatis menggunakan saldo Anda. Order masuk tahap dikemas.",
                ]);
            }
        } else {
            if ($fromEtalase && $finalEtalaseSeller) {
                $sellerName = $finalEtalaseSeller->sellerInfo->store_name ?? $finalEtalaseSeller->full_name;
                Notification::create([
                    'for_user_id' => $user->id,
                    'category' => 'order',
                    'title' => 'Order Etalase Dibuat',
                    'description' => "Order #{$order->id} dari etalase {$sellerName} telah dibuat dengan total Rp" . number_format($order->grand_total, 0, ',', '.') . ". Silakan lakukan pembayaran dan upload bukti transfer.",
                ]);
            } else {
                Notification::create([
                    'for_user_id' => $user->id,
                    'category' => 'order',
                    'title' => 'Order Berhasil Dibuat',
                    'description' => "Order #{$order->id} telah dibuat dengan total Rp" . number_format($order->grand_total, 0, ',', '.') . ". Silakan lakukan pembayaran dan upload bukti transfer.",
                ]);
            }
        }

        // Add margin payout notification for balance-paid external orders
        if ($order->payment_method === 'balance' && $order->purchase_type === 'external' && $order->seller_margin_total > 0) {
            $userLevel = $user->level;
            $levelBadge = $user->getLevelBadge();
            $marginPercent = $user->getLevelMarginPercent();
            
            $description = "Margin sebesar Rp" . number_format($order->seller_margin_total, 0, ',', '.') . " telah ditambahkan ke saldo Anda.";
            
            if ($marginPercent) {
                $description .= " (Margin {$marginPercent}% karena Anda {$levelBadge})";
            } else {
                $description .= " (Margin dihitung khusus untuk level Anda: {$levelBadge})"; // updated fallback
            }
            
            $description .= " Credit score +5 poin!";
            
            Notification::create([
                'for_user_id' => $user->id,
                'category' => 'payment',
                'title' => 'Margin Seller Diterima',
                'description' => $description,
            ]);
        }

        session()->forget('wholesale_products');
        
        // Clear etalase session data
        if ($fromEtalase) {
            session()->forget(['etalase_product_id', 'etalase_seller_id', 'from_etalase']);
        }

        $successMessage = $order->payment_method === 'balance' ? 'Order berhasil dibuat & dibayar dengan saldo.' : 'Order berhasil dibuat.';
        if ($fromEtalase && $finalEtalaseSeller) {
            $sellerName = $finalEtalaseSeller->sellerInfo->store_name ?? $finalEtalaseSeller->full_name;
            $successMessage = $order->payment_method === 'balance' 
                ? "Order dari etalase {$sellerName} berhasil dibuat & dibayar dengan saldo."
                : "Order dari etalase {$sellerName} berhasil dibuat.";
        }

        return redirect()->route('user.orders.show', $order)->with('success', $successMessage);
    }

    /** Show single order (ownership enforced) */
    public function show(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        $order->load('items.product');
        $setting = Setting::first(); // pass payment info
        return view('user.orders.show', compact('order','setting'));
    }

    /** Upload / replace manual payment proof */
    public function uploadProof(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        if (!$order->canUploadProof()) {
            return back()->withErrors(['payment_proof' => 'Tidak bisa upload bukti pada status saat ini.']);
        }
        if ($order->isBalancePayment()) {
            return back()->withErrors(['payment_proof' => 'Order ini sudah dibayar via saldo.']);
        }
        $data = $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($order->payment_proof_path) {
            Storage::disk('public')->delete($order->payment_proof_path);
        }
        $path = $data['payment_proof']->store('orders/proofs', 'public');

        $order->update([
            'payment_proof_path' => $path,
            'payment_status' => 'waiting_confirmation',
            'status' => 'awaiting_confirmation',
        ]);

        Notification::create([
            'for_user_id' => $order->user_id,
            'category' => 'payment',
            'title' => 'Bukti Pembayaran Diterima',
            'description' => "Bukti pembayaran untuk order #{$order->id} diterima dan menunggu konfirmasi admin.",
        ]);

        return back()->with('success', 'Bukti pembayaran diupload. Menunggu konfirmasi admin.');
    }

    /** Cancel order */
    public function cancel(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        if (!in_array($order->status, ['pending']) || !in_array($order->payment_status, ['unpaid','rejected'])) {
            return back()->withErrors(['order' => 'Order tidak bisa dibatalkan.']);
        }
        $order->update(['status' => 'cancelled']);
        return redirect()->route('user.orders.index')->with('success', 'Order dibatalkan.');
    }
}
