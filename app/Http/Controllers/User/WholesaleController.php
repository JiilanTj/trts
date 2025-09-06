<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WholesaleController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters - MUTUALLY EXCLUSIVE for quick filters!
        $rankingType = $request->get('ranking_type'); // best_seller
        $rankingLimit = $request->get('ranking_limit', 20); // 20, 30, 50
        $profitType = $request->get('profit_type'); // profit
        $profitLimit = $request->get('profit_limit', 20);
        
        // Additional filter parameters for manual search
        $priceMin = $request->get('price_min');
        $priceMax = $request->get('price_max');
        $categoryId = $request->get('category_id');
        $sku = $request->get('sku');
        $productName = $request->get('product_name');
        
        // MUTUALLY EXCLUSIVE LOGIC: Only one quick filter can be active
        if ($profitType && $rankingType) {
            // If both are present, prioritize the most recent one (profit)
            $rankingType = null;
            $rankingLimit = null;
        }
        
        // Check if manual filters are being used
        $hasManualFilters = $priceMin || $priceMax || $categoryId || $sku || $productName;
        
        // Default to best seller 20 if no filters are active
        if (!$rankingType && !$profitType && !$hasManualFilters) {
            $rankingType = 'best_seller';
            $rankingLimit = 20;
        }
        
        // Get basic stats for wholesale dashboard
        $totalProducts = Product::where('status', 'active')->count();
        $totalCategories = Category::count();
        $categories = Category::where('status', 'active')->orderBy('name')->get();
        
        // Get featured products based on filters
        $featuredProducts = $this->getFilteredProducts(
            $rankingType, 
            $rankingLimit, 
            $profitType, 
            $profitLimit,
            $priceMin,
            $priceMax,
            $categoryId,
            $sku,
            $productName
        );
        
        // Ensure featuredProducts is always a collection
        if (!$featuredProducts) {
            $featuredProducts = collect([]);
        }
        
        // Handle AJAX requests for super fast loading!
        if ($request->ajax() || $request->get('ajax')) {
            $html = view('user.wholesale.partials.products-grid', compact('featuredProducts'))->render();
            
            return response()->json([
                'html' => $html,
                'filters' => [
                    'hasFilters' => $rankingType || $profitType || $hasManualFilters,
                    'rankingType' => $rankingType,
                    'rankingLimit' => $rankingLimit,
                    'profitType' => $profitType,
                    'profitLimit' => $profitLimit,
                    'priceMin' => $priceMin,
                    'priceMax' => $priceMax,
                    'categoryId' => $categoryId,
                    'sku' => $sku,
                    'productName' => $productName,
                    'hasManualFilters' => $hasManualFilters,
                ],
                'count' => $featuredProducts->count()
            ]);
        }
        
        return view('user.wholesale.index', compact(
            'totalProducts', 
            'totalCategories', 
            'categories',
            'featuredProducts',
            'rankingType',
            'rankingLimit',
            'profitType',
            'profitLimit',
            'priceMin',
            'priceMax',
            'categoryId',
            'sku',
            'productName',
            'hasManualFilters'
        ));
    }
    
    /**
     * Get filtered products based on parameters (MUTUALLY EXCLUSIVE for quick filters)
     */
    private function getFilteredProducts($rankingType, $rankingLimit, $profitType, $profitLimit, $priceMin = null, $priceMax = null, $categoryId = null, $sku = null, $productName = null)
    {
        // Check if manual filters are being used
        $hasManualFilters = $priceMin || $priceMax || $categoryId || $sku || $productName;
        
        // If manual filters are used, prioritize them over quick filters
        if ($hasManualFilters) {
            return $this->getManualFilteredProducts($priceMin, $priceMax, $categoryId, $sku, $productName);
        }
        
        // PROFIT FILTER takes priority (mutually exclusive)
        if ($profitType === 'profit') {
            return $this->getTopProfitProducts($profitLimit);
        }
        
        // RANKING FILTER (best seller)
        if ($rankingType === 'best_seller') {
            return $this->getTopSellingProducts($rankingLimit);
        }
        
        // DEFAULT: Best sellers
        return $this->getTopSellingProducts(20);
    }
    
    /**
     * Get top selling products based on total quantity sold
     */
    private function getTopSellingProducts(int $limit = 20)
    {
        // Get product IDs with their total sales using a subquery approach
        $productSales = DB::table('order_items')
            ->join('orders', function($join) {
                $join->on('order_items.order_id', '=', 'orders.id')
                     ->whereIn('orders.status', ['completed', 'delivered', 'shipped', 'packaging'])
                     ->where('orders.payment_status', 'paid');
            })
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.status', 'active')
            ->where('products.stock', '>', 10)
            ->groupBy('products.id')
            ->selectRaw('products.id, SUM(order_items.quantity) as total_sold')
            ->orderByDesc('total_sold')
            ->take($limit)
            ->pluck('total_sold', 'id');
            
        if ($productSales->isEmpty()) {
            // Fallback to products with highest stock if no sales data
            return Product::where('status', 'active')
                ->where('stock', '>', 10)
                ->orderByDesc('stock')
                ->take($limit)
                ->get();
        }
        
        // Get the actual products and maintain the order
        $productIds = $productSales->keys()->toArray();
        $products = Product::whereIn('id', $productIds)->get();
        
        // Sort products by their sales order
        return $products->sortByDesc(function($product) use ($productSales) {
            return $productSales->get($product->id, 0);
        })->values();
    }
    
    /**
     * Get top profit products based on profit margin
     */
    private function getTopProfitProducts(int $limit = 20)
    {
        return Product::where('status', 'active')
            ->where('stock', '>', 10)
            ->where('profit', '>', 0) // Only products with positive profit
            ->orderByDesc('profit')
            ->orderByDesc('stock') // Secondary sort by stock
            ->take($limit)
            ->get();
    }
    
    /**
     * Get products filtered by manual search criteria
     */
    private function getManualFilteredProducts($priceMin = null, $priceMax = null, $categoryId = null, $sku = null, $productName = null)
    {
        $query = Product::where('status', 'active')
            ->where('stock', '>', 0); // At least some stock
        
        // If no filters provided, return all active products (limited)
        $hasFilters = $priceMin || $priceMax || $categoryId || $sku || $productName;
        
        if ($hasFilters) {
            // Filter by price range
            if ($priceMin) {
                $query->where('sell_price', '>=', (int) str_replace('.', '', $priceMin));
            }
            
            if ($priceMax) {
                $query->where('sell_price', '<=', (int) str_replace('.', '', $priceMax));
            }
            
            // Filter by category
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }
            
            // Filter by SKU (product code)
            if ($sku) {
                $query->where('sku', 'like', '%' . $sku . '%');
            }
            
            // Filter by product name
            if ($productName) {
                $query->where('name', 'like', '%' . $productName . '%');
            }
            
            // Order by stock and profit for better wholesale distribution
            return $query->orderByDesc('stock')
                ->orderByDesc('profit')
                ->limit(100) // Reasonable limit for manual search
                ->get();
        } else {
            // No filters: return recent products with good stock
            return $query->where('stock', '>', 10)
                ->orderByDesc('created_at')
                ->orderByDesc('stock')
                ->limit(50) // Show recent products for manual browsing
                ->get();
        }
    }
    
    /**
     * Create order from wholesale bulk selection
     */
    public function createOrder(Request $request)
    {
        $user = $request->user();
        
        $data = $request->validate([
            'selected_products' => 'required|array|min:1',
            'selected_products.*.product_id' => 'required|exists:products,id',
            'selected_products.*.quantity' => 'integer|min:1|max:1000',
        ]);
        
        // Default quantity to 1 if not provided
        $selectedProducts = collect($data['selected_products'])->map(function($item) {
            return [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'] ?? 1
            ];
        })->toArray();
        
        // Get products data for validation and prefill
        $productIds = array_column($selectedProducts, 'product_id');
        $products = Product::whereIn('id', $productIds)->where('status', 'active')->get();
        
        if ($products->count() !== count($productIds)) {
            return back()->withErrors(['selected_products' => 'Beberapa produk tidak tersedia atau tidak aktif.'])->withInput();
        }
        
        // Check stock availability
        foreach ($selectedProducts as $item) {
            $product = $products->find($item['product_id']);
            if ($product->stock < $item['quantity']) {
                return back()->withErrors(['selected_products' => "Stok produk '{$product->name}' tidak mencukupi (tersedia: {$product->stock}, diminta: {$item['quantity']})."])->withInput();
            }
        }
        
        // Redirect to order creation page with pre-filled products
        return redirect()->route('user.orders.create')
            ->with('wholesale_products', $selectedProducts)
            ->with('success', 'Produk berhasil dipilih! Lengkapi data order di bawah.');
    }
}
