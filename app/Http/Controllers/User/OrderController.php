<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
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
        $orders = Order::with('items.product')
            ->where('user_id', $user->id)
            ->latest()->paginate(15);

        // Placeholder: return view when Blade ready
        return view('user.orders.index', compact('orders'));
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
        $user = $request->user();
        $selfDefaultName = $user->full_name; // for autofill when self
        return view('user.orders.create', compact('products','prefill','purchaseType','singleMode','selfDefaultName'));
    }

    /**
     * Store a new order (manual payment workflow).
     * Expected payload:
     * purchase_type: self|external
     * external_customer_name/phone (when external)
     * address (required)
     * items: [ { product_id, quantity } ]
     */
    public function store(Request $request)
    {
        // Normalize potential array inputs coming from malformed submissions/autofill
        foreach (['external_customer_name','external_customer_phone','address'] as $field) {
            $val = $request->input($field);
            if (is_array($val)) {
                $flat = trim(implode(' ', array_map(fn($v)=> is_scalar($v)? $v : '', $val)));
                $request->merge([$field => $flat]);
            }
        }

        $user = $request->user();
        $data = $request->validate([
            'purchase_type' => 'required|in:self,external',
            'external_customer_name' => 'required_if:purchase_type,external|string|max:120',
            'external_customer_phone' => 'required_if:purchase_type,external|string|max:40',
            'address' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'user_notes' => 'nullable|string',
        ]);

        // Autofill for self purchase: treat user's own name as external_customer_name for consistency
        if ($data['purchase_type'] === 'self') {
            $data['external_customer_name'] = $user->full_name;
        }

        $purchaseType = $data['purchase_type'];
        $itemsInput = $data['items'];

        $order = DB::transaction(function () use ($user, $data, $itemsInput, $purchaseType) {
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
                'payment_method' => 'manual_transfer',
                'payment_status' => 'unpaid',
                'status' => 'pending',
                'user_notes' => $data['user_notes'] ?? null,
            ]);

            foreach ($itemsInput as $row) {
                $product = Product::find($row['product_id']);
                if (!$product || !$product->isActive()) {
                    abort(422, 'Produk tidak tersedia.');
                }
                $qty = (int)$row['quantity'];
                $basePrice = $product->harga_biasa; // snapshot
                $sellPrice = $product->harga_jual;  // snapshot
                $unitPrice = $product->getApplicablePrice($user, $purchaseType);
                $discount = 0; // future: apply promo logic
                $sellerMargin = 0;
                if ($user->isSeller() && $purchaseType === 'external') {
                    $sellerMargin = max(0, $sellPrice - $basePrice);
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

            $order->update([
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'seller_margin_total' => $marginTotal,
                'grand_total' => $grandTotal,
            ]);

            return $order->fresh(['items.product']);
        });

        return redirect()->route('user.orders.show', $order)->with('success', 'Order berhasil dibuat.');
    }

    /** Show single order (ownership enforced) */
    public function show(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        $order->load('items.product');
        return view('user.orders.show', compact('order'));
    }

    /** Upload / replace manual payment proof */
    public function uploadProof(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        if (!$order->canUploadProof()) {
            return back()->withErrors(['payment_proof' => 'Tidak bisa upload bukti pada status saat ini.']);
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

        return back()->with('success', 'Bukti pembayaran diupload. Menunggu konfirmasi admin.');
    }

    /** Cancel order (only if still pending/unpaid or rejected) */
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
