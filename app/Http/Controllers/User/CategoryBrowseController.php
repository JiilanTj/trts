<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\View\View;

class CategoryBrowseController extends Controller
{
    /**
     * List active categories for normal users.
     */
    public function index(): View
    {
        $categories = Category::active()->withCount(['products' => fn($q) => $q->active()])->orderBy('name')->paginate(12);
        return view('user.categories.index', compact('categories'));
    }

    /**
     * Show a single category with its active products.
     */
    public function show(Category $category): View
    {
        // Only allow active categories
        abort_unless($category->isActive(), 404);
        $products = $category->products()->active()->orderBy('name')->paginate(12);
        return view('user.categories.show', compact('category','products'));
    }
}
