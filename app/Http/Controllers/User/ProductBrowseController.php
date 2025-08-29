<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category; // added
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductBrowseController extends Controller
{
    /**
     * List active products with optional basic filters.
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $categoryId = $request->get('category');
        $status = $request->get('status','active');

        $products = Product::query()
            ->when($status === 'active', fn($q) => $q->active())
            ->when($search, fn($q,$s) => $q->where(function($qq) use ($s){
                $qq->where('name','like',"%{$s}%")->orWhere('sku','like',"%{$s}%");
            }))
            ->when($categoryId, fn($q,$cid) => $q->where('category_id',$cid))
            ->with('category:id,name')
            ->orderBy('name')
            ->paginate(12)
            ->appends($request->query());

        $categories = Category::active()->orderBy('name')->get(['id','name']); // new

        return view('user.products.index', compact('products','search','categoryId','status','categories'));
    }

    /**
     * Show product detail (only active products)
     */
    public function show(Product $product): View
    {
        abort_unless($product->isActive(), 404);
        $product->load('category');
        // Related products: same category, exclude current, only active, limit 4 random
        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('user.products.show', compact('product','relatedProducts'));
    }

    /**
     * Mock buy action - no real transaction, just flash message.
     */
    public function buy(Request $request, Product $product)
    {
        abort_unless($product->isActive(), 404);
        if(!$product->inStock()) {
            return back()->with('error','Produk sedang habis stok.');
        }
        // Simulasi: tidak mengurangi stok atau membuat transaksi.
        return back()->with('success','Simulasi pembelian berhasil. (Fitur pembelian penuh belum tersedia)');
    }
}
