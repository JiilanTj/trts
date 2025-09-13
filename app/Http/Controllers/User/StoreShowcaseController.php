<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\StoreShowcase;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreShowcaseController extends Controller
{
    /**
     * Check if user is seller before executing any method
     */
    private function checkSellerAccess()
    {
        if (!Auth::user() || !Auth::user()->isSeller()) {
            if (request()->ajax()) {
                throw new \Exception('Hanya seller yang bisa menggunakan fitur etalase.');
            }
            abort(403, 'Hanya seller yang bisa menggunakan fitur etalase.');
        }
    }

    /**
     * Display a listing of user's store showcases.
     */
    public function index()
    {
        $this->checkSellerAccess();
        
        $showcases = Auth::user()->storeShowcases()
            ->withProduct()
            ->ordered()
            ->paginate(12);

        $totalShowcases = Auth::user()->storeShowcases()->count();
        $activeShowcases = Auth::user()->activeShowcases()->count();
        $featuredShowcases = Auth::user()->featuredShowcases()->count();

        return view('user.store-showcase.index', compact(
            'showcases', 
            'totalShowcases', 
            'activeShowcases', 
            'featuredShowcases'
        ));
    }

    /**
     * Show the form for creating a new showcase.
     */
    public function create(Request $request)
    {
        $this->checkSellerAccess();
        
        // Get products that are not already in user's showcase
        $existingProductIds = Auth::user()->storeShowcases()->pluck('product_id');
        
        $availableProducts = Product::active()
            ->whereNotIn('id', $existingProductIds)
            ->with('category')
            ->paginate(20);

        // If product_id is provided in query (from "Add to Showcase" button)
        $selectedProduct = null;
        if ($request->filled('product_id')) {
            $selectedProduct = Product::active()
                ->where('id', $request->product_id)
                ->whereNotIn('id', $existingProductIds)
                ->first();
            
            if (!$selectedProduct) {
                return redirect()->route('user.showcases.create')
                    ->withErrors('Produk tidak tersedia atau sudah ada di etalase Anda.');
            }
        }

        return view('user.store-showcase.create', compact('availableProducts', 'selectedProduct'));
    }

    /**
     * Store a newly created showcase in storage.
     */
    public function store(Request $request)
    {
        $this->checkSellerAccess();
        
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'is_featured' => 'boolean',
            'featured_until' => 'nullable|date|after:now',
        ]);

        $product = Product::active()->findOrFail($request->product_id);

        // Check if product is already in showcase
        if (Auth::user()->storeShowcases()->where('product_id', $request->product_id)->exists()) {
            return back()->withErrors(['product_id' => 'Produk ini sudah ada di etalase Anda.']);
        }

        // Get next sort order
        $nextSortOrder = Auth::user()->storeShowcases()->max('sort_order') + 1;

        Auth::user()->storeShowcases()->create([
            'product_id' => $request->product_id,
            'sort_order' => $nextSortOrder,
            'is_featured' => $request->boolean('is_featured'),
            'featured_until' => $request->featured_until,
            'is_active' => true,
        ]);

        return redirect()->route('user.showcases.index')
            ->with('success', "Produk '{$product->name}' berhasil ditambahkan ke etalase dengan harga jual Rp " . number_format($product->harga_jual, 0, ',', '.'));
    }

    /**
     * Display the specified showcase.
     */
    public function show(StoreShowcase $showcase)
    {
        $this->checkSellerAccess();
        
        // Make sure showcase belongs to current user
        if ($showcase->user_id !== Auth::id()) {
            abort(404);
        }

        $showcase->load('product.category');

        return view('user.store-showcase.show', compact('showcase'));
    }

    /**
     * Show the form for editing the specified showcase.
     */
    public function edit(StoreShowcase $showcase)
    {
        $this->checkSellerAccess();
        
        // Make sure showcase belongs to current user
        if ($showcase->user_id !== Auth::id()) {
            abort(404);
        }

        $showcase->load('product');

        return view('user.store-showcase.edit', compact('showcase'));
    }

    /**
     * Update the specified showcase in storage.
     */
    public function update(Request $request, StoreShowcase $showcase)
    {
        $this->checkSellerAccess();
        
        // Make sure showcase belongs to current user
        if ($showcase->user_id !== Auth::id()) {
            abort(404);
        }

        $request->validate([
            'is_featured' => 'boolean',
            'featured_until' => 'nullable|date|after:now',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:1',
        ]);

        $showcase->update([
            'is_featured' => $request->boolean('is_featured'),
            'featured_until' => $request->featured_until,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? $showcase->sort_order,
        ]);

        return redirect()->route('user.showcases.index')
            ->with('success', 'Showcase berhasil diperbarui.');
    }

    /**
     * Remove the specified showcase from storage.
     */
    public function destroy(StoreShowcase $showcase)
    {
        $this->checkSellerAccess();
        
        // Make sure showcase belongs to current user
        if ($showcase->user_id !== Auth::id()) {
            abort(404);
        }

        $productName = $showcase->product->name;
        $showcase->delete();

        return redirect()->route('user.showcases.index')
            ->with('success', "Produk '{$productName}' berhasil dihapus dari etalase.");
    }

    /**
     * Toggle active status of showcase.
     */
    public function toggleActive(StoreShowcase $showcase)
    {
        $this->checkSellerAccess();
        
        // Make sure showcase belongs to current user
        if ($showcase->user_id !== Auth::id()) {
            abort(404);
        }

        $showcase->is_active = !$showcase->is_active;
        $showcase->save();

        $status = $showcase->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return response()->json([
            'success' => true,
            'message' => "Showcase berhasil {$status}.",
            'is_active' => $showcase->is_active
        ]);
    }

    /**
     * Toggle featured status of showcase.
     */
    public function toggleFeatured(StoreShowcase $showcase)
    {
        $this->checkSellerAccess();
        
        // Make sure showcase belongs to current user
        if ($showcase->user_id !== Auth::id()) {
            abort(404);
        }

        $showcase->is_featured = !$showcase->is_featured;
        
        // If setting as featured, set featured_until to 30 days from now
        if ($showcase->is_featured) {
            $showcase->featured_until = now()->addDays(30);
        } else {
            $showcase->featured_until = null;
        }
        
        $showcase->save();

        $status = $showcase->is_featured ? 'difeatured' : 'diunfeatured';

        return response()->json([
            'success' => true,
            'message' => "Showcase berhasil {$status}.",
            'is_featured' => $showcase->is_featured,
            'featured_until' => $showcase->featured_until?->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Update showcase order.
     */
    public function updateOrder(Request $request)
    {
        $this->checkSellerAccess();
        
        $request->validate([
            'showcase_ids' => 'required|array',
            'showcase_ids.*' => 'exists:store_showcases,id'
        ]);

        // Make sure all showcases belong to current user
        $showcases = StoreShowcase::whereIn('id', $request->showcase_ids)
            ->where('user_id', Auth::id())
            ->get();

        if ($showcases->count() !== count($request->showcase_ids)) {
            return response()->json(['error' => 'Invalid showcase IDs.'], 400);
        }

        foreach ($request->showcase_ids as $index => $showcaseId) {
            StoreShowcase::where('id', $showcaseId)
                ->where('user_id', Auth::id())
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan showcase berhasil diperbarui.'
        ]);
    }

    /**
     * Generate or regenerate etalase share token for current user
     */
    public function generateEtalaseShareToken()
    {
        try {
            \Log::info('generateEtalaseShareToken called');
            \Log::info('Request method: ' . request()->method());
            \Log::info('Is AJAX: ' . (request()->ajax() ? 'true' : 'false'));
            \Log::info('Accept header: ' . request()->header('Accept'));
            \Log::info('Content-Type header: ' . request()->header('Content-Type'));
            
            $this->checkSellerAccess();
            
            \Log::info('Seller access check passed');
            
            $user = Auth::user();
            \Log::info('User: ' . $user->id . ' - ' . $user->full_name);
            
            $shareToken = $user->regenerateEtalaseShareToken();
            \Log::info('Share token generated: ' . $shareToken);
            
            $shareUrl = $user->getEtalaseShareUrlAttribute();
            \Log::info('Share URL: ' . $shareUrl);
            
            // Always return JSON for AJAX requests
            if (request()->ajax() || request()->wantsJson()) {
                \Log::info('Returning JSON response');
                return response()->json([
                    'success' => true,
                    'share_token' => $shareToken,
                    'share_url' => $shareUrl,
                    'message' => 'Link sharing etalase berhasil diperbarui!'
                ]);
            }

            return back()->with('success', 'Link sharing etalase berhasil diperbarui!')
                        ->with('share_url', $shareUrl);
        } catch (\Exception $e) {
            \Log::error('Error generating etalase share token: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Always return JSON for AJAX requests
            if (request()->ajax() || request()->wantsJson()) {
                \Log::info('Returning JSON error response');
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal generate link sharing: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors('Gagal generate link sharing: ' . $e->getMessage());
        }
    }

    /**
     * Display shared etalase (public view) - show all products from user's etalase
     */
    public function showShared($token)
    {
        // Find user by etalase share token
        $user = \App\Models\User::findByEtalaseShareToken($token);
        
        if (!$user) {
            abort(404, 'Etalase tidak ditemukan atau link sharing tidak valid.');
        }

        // Check if user is seller
        if (!$user->isSeller()) {
            abort(404, 'Etalase tidak tersedia.');
        }

        // Get all active showcases for this user
        $showcases = $user->activeShowcases()
            ->withProduct()
            ->get();

        $seller = $user;
        $sellerInfo = $seller->sellerInfo;

        return view('user.store-showcase.shared', compact(
            'showcases', 
            'seller', 
            'sellerInfo'
        ));
    }

    /**
     * Debug method to test AJAX requests
     */
    public function debugAjax()
    {
        try {
            \Log::info('Debug AJAX called');
            \Log::info('Request method: ' . request()->method());
            \Log::info('Is AJAX: ' . (request()->ajax() ? 'true' : 'false'));
            \Log::info('wantsJson: ' . (request()->wantsJson() ? 'true' : 'false'));
            \Log::info('Accept header: ' . request()->header('Accept'));
            \Log::info('Content-Type header: ' . request()->header('Content-Type'));
            \Log::info('User authenticated: ' . (auth()->check() ? 'true' : 'false'));
            
            if (auth()->check()) {
                $user = auth()->user();
                \Log::info('User ID: ' . $user->id);
                \Log::info('User is seller: ' . ($user->isSeller() ? 'true' : 'false'));
            }

            return response()->json([
                'success' => true,
                'message' => 'Debug test successful',
                'request_info' => [
                    'method' => request()->method(),
                    'is_ajax' => request()->ajax(),
                    'wants_json' => request()->wantsJson(),
                    'accept' => request()->header('Accept'),
                    'content_type' => request()->header('Content-Type'),
                    'authenticated' => auth()->check(),
                    'is_seller' => auth()->check() ? auth()->user()->isSeller() : false
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Debug AJAX error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Debug test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buy product from shared etalase
     * Logic: margin goes to etalase owner, total spend counts for both parties (buyer and seller)
     */
    public function buyFromEtalase(Request $request, Product $product)
    {
        \Log::info('=== buyFromEtalase START ===');
        \Log::info('buyFromEtalase called', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'user_id' => auth()->id(),
            'request_data' => $request->all(),
            'is_authenticated' => auth()->check()
        ]);
        
        // Check if user is authenticated
        if (!auth()->check()) {
            \Log::info('User not authenticated, redirecting to login');
            return redirect()->route('login')->with('warning', 'Silakan login terlebih dahulu untuk membeli produk.');
        }
        
        try {
            \Log::info('Starting validation');
            
            // Validate request
            $request->validate([
                'seller_id' => 'required|exists:users,id',
                'from_etalase' => 'required',
                'quantity' => 'integer|min:1|max:' . $product->stock,
            ]);

            \Log::info('Validation passed');

            $quantity = $request->input('quantity', 1);
            $sellerId = $request->input('seller_id');
            $buyer = auth()->user();
            
            \Log::info('Request data extracted', [
                'quantity' => $quantity,
                'seller_id' => $sellerId,
                'buyer_id' => $buyer->id
            ]);
            
            // Find the seller (etalase owner)
            $seller = \App\Models\User::findOrFail($sellerId);
            
            // Make sure seller is actually a seller and has this product in their etalase
            if (!$seller->isSeller()) {
                return back()->withErrors('Seller tidak valid.');
            }
            
            // Check if product is in seller's active showcase
            $showcase = $seller->activeShowcases()
                ->where('product_id', $product->id)
                ->first();
                
            if (!$showcase) {
                return back()->withErrors('Produk tidak tersedia di etalase seller ini.');
            }
            
            // Check stock
            if ($product->stock < $quantity) {
                return back()->withErrors('Stok tidak mencukupi.');
            }
            
            // Calculate prices and margin
            $hargaBiasa = $product->harga_biasa;
            $hargaJual = $product->harga_jual;
            $margin = $hargaJual - $hargaBiasa;
            $totalHarga = $hargaJual * $quantity;
            $totalMargin = $margin * $quantity;
            
            \DB::beginTransaction();
            
            try {
                // Create order
                $order = \App\Models\Order::create([
                    'user_id' => $buyer->id,
                    'purchase_type' => 'self',
                    'subtotal' => $totalHarga,
                    'grand_total' => $totalHarga,
                    'payment_method' => 'manual_transfer',
                    'payment_status' => 'unpaid',
                    'status' => 'pending',
                    'user_notes' => 'Dibeli dari etalase ' . ($seller->sellerInfo->store_name ?? $seller->full_name),
                    // Add seller info for etalase purchases
                    'seller_id' => $seller->id,
                    'from_etalase' => true,
                    'etalase_margin' => $totalMargin
                ]);
                
                // Create order item
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price_per_item' => $hargaJual,
                    'total_price' => $totalHarga
                ]);
                
                // Update product stock
                $product->decrement('stock', $quantity);
                
                // Update spending and levels for BOTH buyer and seller
                // 1. Buyer gets normal level progression from their purchase
                $buyer->addTransactionAmount($totalHarga);
                
                // 2. Seller gets level progression from the margin they earned
                $seller->addTransactionAmount($totalMargin);
                
                \DB::commit();
                
                \Log::info('Transaction committed successfully');
                
                $sellerName = $seller->sellerInfo->store_name ?? $seller->full_name;
                $successMessage = "Berhasil membeli {$quantity} {$product->name} dari etalase {$sellerName}!";
                
                \Log::info('Redirecting to order show', [
                    'order_id' => $order->id,
                    'message' => $successMessage
                ]);
                
                return redirect()->route('user.orders.show', $order)
                    ->with('success', $successMessage);
                    
            } catch (\Exception $e) {
                \Log::error('Transaction failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                \DB::rollback();
                throw $e;
            }
            
        } catch (\Exception $e) {
            \Log::error('Error buying from etalase', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors('Terjadi kesalahan saat memproses pembelian: ' . $e->getMessage());
        } finally {
            \Log::info('=== buyFromEtalase END ===');
        }
    }
}
