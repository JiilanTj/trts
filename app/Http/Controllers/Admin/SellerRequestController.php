<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class SellerRequestController extends Controller
{
    /**
     * Display a listing of seller requests.
     */
    public function index(): View
    {
        $sellerRequests = SellerRequest::with('user')
            ->when(request('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->latest()
            ->paginate(15);

        return view('admin.seller-requests.index', compact('sellerRequests'));
    }

    /**
     * Display the specified seller request.
     */
    public function show(SellerRequest $sellerRequest): View
    {
        $sellerRequest->load('user');
        
        return view('admin.seller-requests.show', compact('sellerRequest'));
    }

    /**
     * Approve the seller request.
     */
    public function approve(Request $request, SellerRequest $sellerRequest): RedirectResponse
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $success = $sellerRequest->approve($request->admin_note);

            if (!$success) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Permintaan tidak dapat disetujui. Mungkin statusnya sudah berubah.');
            }

            DB::commit();

            return redirect()->route('admin.seller-requests.show', $sellerRequest)
                ->with('success', 'Permintaan seller berhasil disetujui. User sekarang menjadi seller.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyetujui permintaan: ' . $e->getMessage());
        }
    }

    /**
     * Reject the seller request.
     */
    public function reject(Request $request, SellerRequest $sellerRequest): RedirectResponse
    {
        $request->validate([
            'admin_note' => 'required|string|max:1000',
        ]);

        $success = $sellerRequest->reject($request->admin_note);

        if (!$success) {
            return redirect()->back()
                ->with('error', 'Permintaan tidak dapat ditolak. Mungkin statusnya sudah berubah.');
        }

        return redirect()->route('admin.seller-requests.show', $sellerRequest)
            ->with('success', 'Permintaan seller berhasil ditolak.');
    }

    /**
     * Get pending requests count for admin dashboard.
     */
    public function getPendingCount()
    {
        $count = SellerRequest::pending()->count();
        
        return response()->json(['count' => $count]);
    }
}
