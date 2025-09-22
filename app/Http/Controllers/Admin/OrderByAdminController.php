<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderByAdmin;
use App\Models\Product;
use App\Models\StoreShowcase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderByAdminController extends Controller
{
    // List all orders by admin with optional status filter
    public function index(Request $request)
    {
        $this->ensureAdmin();

        $status = $request->query('status');
        $query = OrderByAdmin::query()->with(['admin','user','storeShowcase','product'])
            ->latest();
        if ($status) {
            $query->where('status', $status);
        }
        $orders = $query->paginate(20)->withQueryString();

        if (view()->exists('admin.orders-by-admin.index')) {
            return view('admin.orders-by-admin.index', compact('orders', 'status'));
        }
        return response()->json($orders);
    }

    // Show create form
    public function create()
    {
        $this->ensureAdmin();

        // Optional minimal datasets for dropdowns (can be replaced by AJAX search)
        $users = User::query()->users()->orderByDesc('id')->limit(20)->get(['id','full_name','username']);
        $showcases = StoreShowcase::query()->with(['user','product'])->orderByDesc('id')->limit(20)->get();
        $products = Product::query()->active()->orderByDesc('id')->limit(20)->get();

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

        $data = $request->validate([
            'user_id' => ['required','exists:users,id'],
            'store_showcase_id' => ['required','exists:store_showcases,id'],
            'product_id' => ['required','exists:products,id'],
            'quantity' => ['required','integer','min:1'],
            'unit_price' => ['nullable','integer','min:0'],
            'status' => ['nullable','in:'.implode(',', [OrderByAdmin::STATUS_PENDING, OrderByAdmin::STATUS_CONFIRMED])],
        ]);

        $adminId = Auth::id();
        $showcase = StoreShowcase::with('product')->findOrFail($data['store_showcase_id']);
        $product = Product::findOrFail($data['product_id']);

        // Ensure product matches the showcase product (basic integrity guard)
        if ($showcase->product_id !== $product->id) {
            return back()->withErrors(['product_id' => 'Produk tidak sesuai dengan etalase yang dipilih.'])->withInput();
        }

        // Default unit_price to product seller price if not provided
        $unitPrice = $data['unit_price'] ?? (int) ($product->harga_jual ?? $product->sell_price);
        $totalPrice = $unitPrice * (int)$data['quantity'];

        $order = OrderByAdmin::create([
            'admin_id' => $adminId,
            'user_id' => (int)$data['user_id'],
            'store_showcase_id' => (int)$data['store_showcase_id'],
            'product_id' => (int)$data['product_id'],
            'quantity' => (int)$data['quantity'],
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'status' => $data['status'] ?? OrderByAdmin::STATUS_PENDING,
        ]);

        if (view()->exists('admin.orders-by-admin.show')) {
            return redirect()->route('admin.orders-by-admin.show', $order);
        }
        return response()->json($order, 201);
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
            'status' => ['sometimes','in:'.implode(',', [OrderByAdmin::STATUS_PENDING, OrderByAdmin::STATUS_CONFIRMED])],
        ]);

        $orders_by_admin->fill($data);

        // Recompute total when quantity or unit_price changes
        if (array_key_exists('quantity', $data) || array_key_exists('unit_price', $data)) {
            $qty = (int) ($data['quantity'] ?? $orders_by_admin->quantity);
            $unit = (int) ($data['unit_price'] ?? $orders_by_admin->unit_price);
            $orders_by_admin->total_price = $qty * $unit;
        }

        $orders_by_admin->save();

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
