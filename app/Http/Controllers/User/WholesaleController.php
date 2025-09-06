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
        // Get filter parameters - MUTUALLY EXCLUSIVE!
        $rankingType = $request->get('ranking_type'); // best_seller
        $rankingLimit = $request->get('ranking_limit', 20); // 20, 30, 50
        $profitType = $request->get('profit_type'); // profit
        $profitLimit = $request->get('profit_limit', 20);
        
        // MUTUALLY EXCLUSIVE LOGIC: Only one filter can be active
        if ($profitType && $rankingType) {
            // If both are present, prioritize the most recent one (profit)
            $rankingType = null;
            $rankingLimit = null;
        }
        
        // Default to best seller 20 if no filters are active
        if (!$rankingType && !$profitType) {
            $rankingType = 'best_seller';
            $rankingLimit = 20;
        }
        
        // Get basic stats for wholesale dashboard
        $totalProducts = Product::where('status', 'active')->count();
        $totalCategories = Category::count();
        
        // Get featured products based on filters
        $featuredProducts = $this->getFilteredProducts($rankingType, $rankingLimit, $profitType, $profitLimit);
        
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
                    'hasFilters' => $rankingType || $profitType,
                    'rankingType' => $rankingType,
                    'rankingLimit' => $rankingLimit,
                    'profitType' => $profitType,
                    'profitLimit' => $profitLimit,
                ],
                'count' => $featuredProducts->count()
            ]);
        }
        
        return view('user.wholesale.index', compact(
            'totalProducts', 
            'totalCategories', 
            'featuredProducts',
            'rankingType',
            'rankingLimit',
            'profitType',
            'profitLimit'
        ));
    }
    
    /**
     * Get filtered products based on parameters (MUTUALLY EXCLUSIVE)
     */
    private function getFilteredProducts($rankingType, $rankingLimit, $profitType, $profitLimit)
    {
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
}
