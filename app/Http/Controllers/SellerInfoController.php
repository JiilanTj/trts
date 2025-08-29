<?php

namespace App\Http\Controllers;

use App\Models\SellerInfo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class SellerInfoController extends Controller
{
    /**
     * Display a listing of sellers.
     */
    public function index(): View
    {
        $sellers = SellerInfo::with('user')
            ->active()
            ->orderByCreditScore()
            ->paginate(15);

        return view('sellers.index', compact('sellers'));
    }

    /**
     * Display the specified seller profile.
     */
    public function show(SellerInfo $sellerInfo): View
    {
        $sellerInfo->load(['user', 'products' => function ($query) {
            $query->latest()->take(6);
        }]);

        // Increment visitor count if not the owner
        if (!Auth::check() || Auth::id() !== $sellerInfo->user_id) {
            $sellerInfo->incrementVisitors();
        }

        return view('sellers.show', compact('sellerInfo'));
    }

    /**
     * Show seller dashboard (for seller owner).
     */
    public function dashboard(): View
    {
        $user = Auth::user();
        
        if (!$user->isSeller()) {
            abort(403, 'Anda bukan seller.');
        }

        $sellerInfo = $user->sellerInfo;
        
        if (!$sellerInfo) {
            abort(404, 'Informasi seller tidak ditemukan.');
        }

        $sellerInfo->load('products');

        // Get some statistics
        $stats = [
            'total_products' => $sellerInfo->products()->count(),
            'active_products' => $sellerInfo->products()->where('status', 'active')->count(),
            'total_visitors' => $sellerInfo->visitors,
            'total_followers' => $sellerInfo->followers,
            'credit_score' => $sellerInfo->credit_score,
        ];

        return view('sellers.dashboard', compact('sellerInfo', 'stats'));
    }

    /**
     * Show the form for editing seller info.
     */
    public function edit(): View
    {
        $user = Auth::user();
        
        if (!$user->isSeller()) {
            abort(403, 'Anda bukan seller.');
        }

        $sellerInfo = $user->sellerInfo;
        
        if (!$sellerInfo) {
            abort(404, 'Informasi seller tidak ditemukan.');
        }

        return view('sellers.edit', compact('sellerInfo'));
    }

    /**
     * Update seller info.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        if (!$user->isSeller()) {
            abort(403, 'Anda bukan seller.');
        }

        $sellerInfo = $user->sellerInfo;
        
        if (!$sellerInfo) {
            abort(404, 'Informasi seller tidak ditemukan.');
        }

        $request->validate([
            'store_name' => 'required|string|max:255|unique:seller_info,store_name,' . $sellerInfo->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $sellerInfo->update([
            'store_name' => $request->store_name,
            'description' => $request->description,
        ]);

        return redirect()->route('sellers.dashboard')
            ->with('success', 'Informasi toko berhasil diperbarui.');
    }

    /**
     * Admin: Update seller status.
     */
    public function updateStatus(SellerInfo $sellerInfo): RedirectResponse
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $newStatus = $sellerInfo->isActive() ? SellerInfo::STATUS_INACTIVE : SellerInfo::STATUS_ACTIVE;
        $sellerInfo->update(['status' => $newStatus]);

        $statusText = $newStatus === SellerInfo::STATUS_ACTIVE ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->back()
            ->with('success', "Seller {$sellerInfo->store_name} berhasil {$statusText}.");
    }

    /**
     * Admin: Update credit score.
     */
    public function updateCreditScore(Request $request, SellerInfo $sellerInfo): RedirectResponse
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'credit_score' => 'required|integer|min:0|max:1000',
            'reason' => 'nullable|string|max:255',
        ]);

        $oldScore = $sellerInfo->credit_score;
        $sellerInfo->updateCreditScore($request->credit_score);

        return redirect()->back()
            ->with('success', "Credit score berhasil diubah dari {$oldScore} menjadi {$request->credit_score}.");
    }

    /**
     * Follow/Unfollow seller.
     */
    public function toggleFollow(SellerInfo $sellerInfo): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::id() === $sellerInfo->user_id) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat mengikuti toko sendiri.');
        }

        // Note: This is a basic implementation
        // In a real app, you'd have a followers table for many-to-many relationship
        // For now, we'll just increment/decrement the count
        
        // Check if user is already following (you'd need a followers table for this)
        // For demo purposes, just increment
        $sellerInfo->incrementFollowers();

        return redirect()->back()
            ->with('success', 'Berhasil mengikuti toko ' . $sellerInfo->store_name);
    }

    /**
     * Get seller statistics (API endpoint).
     */
    public function getStats(SellerInfo $sellerInfo)
    {
        if (!Auth::check() || (Auth::id() !== $sellerInfo->user_id && !Auth::user()->isAdmin())) {
            abort(403);
        }

        return response()->json([
            'visitors' => $sellerInfo->visitors,
            'followers' => $sellerInfo->followers,
            'credit_score' => $sellerInfo->credit_score,
            'products_count' => $sellerInfo->products()->count(),
            'active_products_count' => $sellerInfo->products()->where('status', 'active')->count(),
        ]);
    }
}
