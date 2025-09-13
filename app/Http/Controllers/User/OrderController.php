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

        // Check for wholesale products from session
        $wholesaleProducts = session('wholesale_products', []);
        $wholesaleMode = !empty($wholesaleProducts);
        
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

        return view('user.orders.create', compact('products','prefill','purchaseType','singleMode','selfPrefill','wholesaleMode','prefilledProducts'));
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
        ]);

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

        $order = DB::transaction(function () use ($user, $data, $itemsInput, $purchaseType, $paymentMethod) {
            $subtotal = 0; $discountTotal = 0; $marginTotal = 0; $grandTotal = 0;

            $order = Order::create([
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
            ]);

            foreach ($itemsInput as $row) {
                $product = Product::find($row['product_id']);
                if (!$product || !$product->isActive()) {
                    abort(422, 'Produk tidak tersedia.');
                }
                $qty = (int)$row['quantity'];
                $basePrice = $product->harga_biasa;
                $sellPrice = $product->harga_jual;
                $unitPrice = $product->getApplicablePrice($user, $purchaseType);
                $discount = 0;
                $sellerMargin = 0;
                
                // Calculate seller margin based on user level for external purchases
                if ($user->isSeller() && $purchaseType === 'external') {
                    $sellerMargin = $product->getSellerMargin($user);
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
                $marginTotal += $sellerMargin * $qty;
                $grandTotal += $lineTotal;
            }

            // Balance validation & deduction if needed
            if ($paymentMethod === 'balance') {
                if ($user->balance < $grandTotal) {
                    abort(422, 'Saldo tidak cukup untuk pembayaran.');
                }
            }

            $order->update([
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'seller_margin_total' => $marginTotal,
                'grand_total' => $grandTotal,
            ]);

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
                $user->addTransactionAmount($grandTotal);
            } else {
                $order->update([
                    'payment_status' => 'unpaid',
                    'status' => 'pending',
                ]);
            }

            return $order->fresh(['items.product']);
        });

        if ($order->payment_method === 'balance') {
            Notification::create([
                'for_user_id' => $user->id,
                'category' => 'payment',
                'title' => 'Pembayaran Saldo Berhasil',
                'description' => "Order #{$order->id} telah dibayar otomatis menggunakan saldo Anda. Order masuk tahap dikemas.",
            ]);
        } else {
            Notification::create([
                'for_user_id' => $user->id,
                'category' => 'order',
                'title' => 'Order Berhasil Dibuat',
                'description' => "Order #{$order->id} telah dibuat dengan total Rp" . number_format($order->grand_total, 0, ',', '.') . ". Silakan lakukan pembayaran dan upload bukti transfer.",
            ]);
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
                $description .= " (Margin sesuai harga jual karena Anda {$levelBadge})";
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

        return redirect()->route('user.orders.show', $order)->with('success', $order->payment_method === 'balance' ? 'Order berhasil dibuat & dibayar dengan saldo.' : 'Order berhasil dibuat.');
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
