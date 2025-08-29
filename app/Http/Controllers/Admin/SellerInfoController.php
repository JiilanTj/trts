<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerInfo;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SellerInfoController extends Controller
{
    /**
     * Display a listing of sellers.
     */
    public function index(): View
    {
        $sellers = SellerInfo::with('user')
            ->when(request('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->orderByCreditScore()
            ->paginate(15);

        return view('admin.sellers.index', compact('sellers'));
    }

    /**
     * Display the specified seller profile.
     */
    public function show(SellerInfo $sellerInfo): View
    {
        $sellerInfo->load(['user', 'products' => function ($query) {
            $query->latest()->take(10);
        }]);

        // Get some statistics
        $stats = [
            'total_products' => $sellerInfo->products()->count(),
            'active_products' => $sellerInfo->products()->where('status', 'active')->count(),
            'total_visitors' => $sellerInfo->visitors,
            'total_followers' => $sellerInfo->followers,
            'credit_score' => $sellerInfo->credit_score,
        ];

        return view('admin.sellers.show', compact('sellerInfo', 'stats'));
    }

    /**
     * Update seller status.
     */
    public function updateStatus(SellerInfo $sellerInfo): RedirectResponse
    {
        $newStatus = $sellerInfo->isActive() ? SellerInfo::STATUS_INACTIVE : SellerInfo::STATUS_ACTIVE;
        $sellerInfo->update(['status' => $newStatus]);

        $statusText = $newStatus === SellerInfo::STATUS_ACTIVE ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->back()
            ->with('success', "Seller {$sellerInfo->store_name} berhasil {$statusText}.");
    }

    /**
     * Update credit score.
     */
    public function updateCreditScore(Request $request, SellerInfo $sellerInfo): RedirectResponse
    {
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
     * Get seller statistics (API endpoint).
     */
    public function getStats(SellerInfo $sellerInfo)
    {
        return response()->json([
            'visitors' => $sellerInfo->visitors,
            'followers' => $sellerInfo->followers,
            'credit_score' => $sellerInfo->credit_score,
            'products_count' => $sellerInfo->products()->count(),
            'active_products_count' => $sellerInfo->products()->where('status', 'active')->count(),
        ]);
    }
}
