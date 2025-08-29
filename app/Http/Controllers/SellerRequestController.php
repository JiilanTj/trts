<?php

namespace App\Http\Controllers;

use App\Models\SellerRequest;
use App\Models\InvitationCode;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SellerRequestController extends Controller
{
    /**
     * Display a listing of seller requests.
     */
    public function index(): View
    {
        $sellerRequests = SellerRequest::with('user')
            ->when(!Auth::user()->isAdmin(), function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->when(request('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->latest()
            ->paginate(15);

        // Return different views based on user role
        $view = Auth::user()->isAdmin() ? 'admin.seller-requests.index' : 'seller-requests.index';
        
        return view($view, compact('sellerRequests'));
    }

    /**
     * Show the form for creating a new seller request.
     */
    public function create()
    {
        // Check if user already has a pending or approved request
        $existingRequest = SellerRequest::where('user_id', Auth::id())
            ->whereIn('status', [SellerRequest::STATUS_PENDING, SellerRequest::STATUS_APPROVED])
            ->first();

        if ($existingRequest) {
            return redirect()->route('seller-requests.show', $existingRequest)
                ->with('info', 'Anda sudah memiliki permintaan seller yang sedang diproses atau sudah disetujui.');
        }

        // Check if user is already a seller
        if (Auth::user()->isSeller()) {
            return redirect()->route('dashboard')
                ->with('info', 'Anda sudah menjadi seller.');
        }

        return view('seller-requests.create');
    }

    /**
     * Store a newly created seller request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Check if user already has a pending or approved request
        $existingRequest = SellerRequest::where('user_id', Auth::id())
            ->whereIn('status', [SellerRequest::STATUS_PENDING, SellerRequest::STATUS_APPROVED])
            ->first();

        if ($existingRequest) {
            return redirect()->route('seller-requests.show', $existingRequest)
                ->with('error', 'Anda sudah memiliki permintaan seller yang sedang diproses atau sudah disetujui.');
        }

        // Check if user is already a seller
        if (Auth::user()->isSeller()) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda sudah menjadi seller.');
        }

        $request->validate([
            'store_name' => 'required|string|max:255|unique:seller_requests,store_name',
            'invite_code' => 'required|string|exists:invitation_codes,code',
            'description' => 'nullable|string|max:1000',
        ]);

        // Validate invitation code
        $invitationCode = InvitationCode::where('code', $request->invite_code)->first();
        
        if (!$invitationCode || !$invitationCode->isValid()) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['invite_code' => 'Kode undangan tidak valid atau sudah tidak dapat digunakan.']);
        }

        try {
            DB::beginTransaction();

            $sellerRequest = SellerRequest::create([
                'user_id' => Auth::id(),
                'store_name' => $request->store_name,
                'invite_code' => $request->invite_code,
                'description' => $request->description,
            ]);

            DB::commit();

            return redirect()->route('seller-requests.show', $sellerRequest)
                ->with('success', 'Permintaan untuk menjadi seller berhasil dikirim. Silakan tunggu persetujuan admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengirim permintaan. Silakan coba lagi.');
        }
    }

    /**
     * Display the specified seller request.
     */
    public function show(SellerRequest $sellerRequest): View
    {
        // Check authorization
        if (!Auth::user()->isAdmin() && $sellerRequest->user_id !== Auth::id()) {
            abort(403);
        }

        $sellerRequest->load('user');
        
        // Return different views based on user role
        $view = Auth::user()->isAdmin() ? 'admin.seller-requests.show' : 'seller-requests.show';
        
        return view($view, compact('sellerRequest'));
    }

    /**
     * Approve the seller request.
     */
    public function approve(Request $request, SellerRequest $sellerRequest): RedirectResponse
    {
        // Only admin can approve
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

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
        // Only admin can reject
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

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
        if (!Auth::user()->isAdmin()) {
            return response()->json(['count' => 0]);
        }

        $count = SellerRequest::pending()->count();
        
        return response()->json(['count' => $count]);
    }
}
