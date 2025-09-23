<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderByAdmin;
use App\Models\Product;
use App\Models\StoreShowcase;
use App\Models\User;
use App\Models\Notification; // add
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class OrderByAdminController extends Controller
{
    // List all orders by admin with optional status filter
    public function index(Request $request)
    {
        $this->ensureAdmin();

        $status = $request->query('status');
        $normalizedStatus = $status ? strtoupper($status) : null;
        $query = OrderByAdmin::query()->with(['admin','user','storeShowcase','product'])
            ->latest();
        if ($normalizedStatus) {
            $query->where('status', $normalizedStatus);
        }
        $orders = $query->paginate(20)->withQueryString();

        // Attach user profit data to each order for display
        $orders->setCollection(
            $orders->getCollection()->transform(function (OrderByAdmin $order) {
                $percent = $this->getUserProfitPercent($order->user ?? null); // decimal e.g., 0.15
                $order->user_profit_percent = $percent;
                $order->total_with_profit = (int) round(($order->total_price ?? 0) * (1 + $percent));
                return $order;
            })
        );

        if (view()->exists('admin.orders-by-admin.index')) {
            // Pass original filter value for UI selection convenience (can be lowercase)
            return view('admin.orders-by-admin.index', [
                'orders' => $orders,
                'status' => $status,
            ]);
        }
        return response()->json($orders);
    }

    /**
     * Compute user's profit percentage based on User model configuration.
     * Uses User::getLevelMarginPercent() to avoid divergence.
     * Returns decimal fraction (e.g., 0.15 for 15%).
     */
    private function getUserProfitPercent(?User $user): float
    {
        $intPercent = $user?->getLevelMarginPercent();
        $decimal = ($intPercent ?? 0) / 100;
        // clamp to [0, 1]
        if ($decimal < 0) { $decimal = 0.0; }
        if ($decimal > 1) { $decimal = 1.0; }
        return $decimal;
    }

    // Show create form
    public function create()
    {
        $this->ensureAdmin();

        // Only list seller users for selection
        $users = User::query()->users()->sellers()->orderByDesc('id')->limit(50)->get(['id','full_name','username']);
        // Products and showcases will be loaded dynamically based on selected seller
        $showcases = collect();
        $products = collect();

        if (view()->exists('admin.orders-by-admin.create')) {
            return view('admin.orders-by-admin.create', compact('users','showcases','products'));
        }
        return response()->json([
            'users' => $users,
            'showcases' => $showcases,
            'products' => $products,
        ]);
    }

    // Store new record
    public function store(Request $request)
    {
        $this->ensureAdmin();

        // Support multiple items: items[].{store_showcase_id, product_id, quantity}
        $data = $request->validate([
            'user_id' => ['required','exists:users,id'],
            'items' => ['required','array','min:1'],
            'items.*.store_showcase_id' => ['required','exists:store_showcases,id'],
            'items.*.product_id' => ['required','exists:products,id'],
            'items.*.quantity' => ['required','integer','min:1'],
            // unit_price & status are computed/locked server-side
        ]);

        $adminId = Auth::id();

        // Ensure selected user is a seller
        $buyer = User::findOrFail($data['user_id']);
        if (!$buyer->isSeller()) {
            return back()->withErrors(['user_id' => 'User harus seller.'])->withInput();
        }

        $created = [];

        DB::transaction(function () use ($data, $adminId, &$created) {
            foreach ($data['items'] as $idx => $item) {
                $showcase = StoreShowcase::with('product')->findOrFail($item['store_showcase_id']);
                $product = Product::findOrFail($item['product_id']);

                // Ensure showcase belongs to the selected seller
                if ((int)$showcase->user_id !== (int)$data['user_id']) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        "items.$idx.store_showcase_id" => 'Etalase tidak dimiliki oleh seller yang dipilih.'
                    ]);
                }

                // Ensure product matches the showcase product
                if ((int)$showcase->product_id !== (int)$product->id) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        "items.$idx.product_id" => 'Produk tidak sesuai dengan etalase yang dipilih.'
                    ]);
                }

                // Compute unit price from product seller price (harga_jual)
                $unitPrice = (int) ($product->harga_jual ?? $product->sell_price);
                $totalPrice = $unitPrice * (int)$item['quantity'];

                $created[] = OrderByAdmin::create([
                    'admin_id' => $adminId,
                    'user_id' => (int)$data['user_id'],
                    'store_showcase_id' => (int)$item['store_showcase_id'],
                    'product_id' => (int)$item['product_id'],
                    'quantity' => (int)$item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'status' => OrderByAdmin::STATUS_PENDING,
                ]);
            }
        });

        if (view()->exists('admin.orders-by-admin.index')) {
            return redirect()->route('admin.orders-by-admin.index')->with('status', 'Berhasil membuat '.count($created).' order.');
        }
        return response()->json($created, 201);
    }

    // Show one
    public function show(OrderByAdmin $orders_by_admin)
    {
        $this->ensureAdmin();

        $orders_by_admin->load(['admin','user','storeShowcase','product']);
        if (view()->exists('admin.orders-by-admin.show')) {
            return view('admin.orders-by-admin.show', ['order' => $orders_by_admin]);
        }
        return response()->json($orders_by_admin);
    }

    // Edit form
    public function edit(OrderByAdmin $orders_by_admin)
    {
        $this->ensureAdmin();

        $orders_by_admin->load(['admin','user','storeShowcase','product']);
        if (view()->exists('admin.orders-by-admin.edit')) {
            return view('admin.orders-by-admin.edit', ['order' => $orders_by_admin]);
        }
        return response()->json($orders_by_admin);
    }

    // Update
    public function update(Request $request, OrderByAdmin $orders_by_admin)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'quantity' => ['sometimes','integer','min:1'],
            'unit_price' => ['sometimes','integer','min:0'],
            'status' => ['sometimes','in:'.implode(',', [
                OrderByAdmin::STATUS_PENDING,
                OrderByAdmin::STATUS_CONFIRMED,
                OrderByAdmin::STATUS_PACKED,
                OrderByAdmin::STATUS_SHIPPED,
                OrderByAdmin::STATUS_DELIVERED,
            ])],
        ]);

        // Enforce forward-only status transitions
        $oldStatus = $orders_by_admin->status; // capture before changes
        if (array_key_exists('status', $data)) {
            $orderMap = [
                OrderByAdmin::STATUS_PENDING => 0,
                OrderByAdmin::STATUS_CONFIRMED => 1,
                OrderByAdmin::STATUS_PACKED => 2,
                OrderByAdmin::STATUS_SHIPPED => 3,
                OrderByAdmin::STATUS_DELIVERED => 4,
            ];
            $currentIdx = $orderMap[$orders_by_admin->status] ?? 0;
            $targetStatus = strtoupper($data['status']);
            $targetIdx = $orderMap[$targetStatus] ?? $currentIdx;
            if ($targetIdx < $currentIdx) {
                $message = 'Status hanya boleh maju, tidak boleh mundur.';
                if (view()->exists('admin.orders-by-admin.edit')) {
                    return back()->withErrors(['status' => $message])->withInput();
                }
                return response()->json(['message' => $message], 422);
            }
            // normalize stored value
            $data['status'] = $targetStatus;
        }

        $orders_by_admin->fill($data);

        // Recompute total when quantity or unit_price changes
        if (array_key_exists('quantity', $data) || array_key_exists('unit_price', $data)) {
            $qty = (int) ($data['quantity'] ?? $orders_by_admin->quantity);
            $unit = (int) ($data['unit_price'] ?? $orders_by_admin->unit_price);
            $orders_by_admin->total_price = $qty * $unit;
        }

        $orders_by_admin->save();

        // Notify seller (user) if status changed
        if (array_key_exists('status', $data) && $data['status'] !== $oldStatus) {
            $labelMap = [
                OrderByAdmin::STATUS_PENDING => 'Menunggu Konfirmasi',
                OrderByAdmin::STATUS_CONFIRMED => 'Dikonfirmasi',
                OrderByAdmin::STATUS_PACKED => 'Dikemas',
                OrderByAdmin::STATUS_SHIPPED => 'Dikirim',
                OrderByAdmin::STATUS_DELIVERED => 'Terkirim',
            ];
            $descMap = [
                OrderByAdmin::STATUS_CONFIRMED => "Order #{$orders_by_admin->id} telah dikonfirmasi admin.",
                OrderByAdmin::STATUS_PACKED => "Order #{$orders_by_admin->id} sedang dikemas.",
                OrderByAdmin::STATUS_SHIPPED => "Order #{$orders_by_admin->id} telah dikirim dan sedang dalam perjalanan.",
                OrderByAdmin::STATUS_DELIVERED => "Order #{$orders_by_admin->id} telah sampai di alamat tujuan.",
                OrderByAdmin::STATUS_PENDING => "Order #{$orders_by_admin->id} menunggu konfirmasi.",
            ];
            $new = $data['status'];
            $title = 'Order ' . ($labelMap[$new] ?? $new);
            $description = $descMap[$new] ?? ("Status order #{$orders_by_admin->id} berubah menjadi " . ($labelMap[$new] ?? $new) . '.');

            try {
                Notification::create([
                    'for_user_id' => $orders_by_admin->user_id,
                    'category' => 'order',
                    'title' => $title,
                    'description' => $description,
                ]);
            } catch (\Throwable $e) { /* ignore notif failure */ }
        }

        if (view()->exists('admin.orders-by-admin.show')) {
            return redirect()->route('admin.orders-by-admin.show', $orders_by_admin)->with('status', 'Updated');
        }
        return response()->json($orders_by_admin);
    }

    // Delete
    public function destroy(OrderByAdmin $orders_by_admin)
    {
        $this->ensureAdmin();

        $orders_by_admin->delete();

        if (view()->exists('admin.orders-by-admin.index')) {
            return redirect()->route('admin.orders-by-admin.index')->with('status', 'Deleted');
        }
        return response()->json(['deleted' => true]);
    }

    // Custom action: confirm
    public function confirm(OrderByAdmin $orders_by_admin)
    {
        $this->ensureAdmin();
        $orders_by_admin->update(['status' => OrderByAdmin::STATUS_CONFIRMED]);

        // Notify seller about confirmation
        try {
            Notification::create([
                'for_user_id' => $orders_by_admin->user_id,
                'category' => 'order',
                'title' => 'Order Dikonfirmasi',
                'description' => "Order #{$orders_by_admin->id} telah dikonfirmasi admin.",
            ]);
        } catch (\Throwable $e) { /* ignore notif failure */ }

        if (view()->exists('admin.orders-by-admin.show')) {
            return redirect()->route('admin.orders-by-admin.show', $orders_by_admin)->with('status', 'Confirmed');
        }
        return response()->json($orders_by_admin);
    }

    protected function ensureAdmin(): void
    {
        $u = Auth::user();
        if (!$u || !method_exists($u, 'isAdmin') || !$u->isAdmin()) {
            abort(403, 'Forbidden');
        }
    }
}
