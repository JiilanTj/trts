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
     * Display a listing of seller requests for current user.
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Get latest seller request for current user
        $sellerRequest = SellerRequest::where('user_id', $user->id)
            ->latest()
            ->first();
        
        $hasRequest = $sellerRequest !== null;

        return view('user.seller-requests.index', compact('sellerRequest', 'hasRequest'));
    }

    /**
     * Show the form for creating a new seller request.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Check if user already has a pending or approved request
        $existingRequest = SellerRequest::where('user_id', $user->id)
            ->whereIn('status', [SellerRequest::STATUS_PENDING, SellerRequest::STATUS_APPROVED])
            ->first();

        if ($existingRequest) {
            return redirect()->route('seller-requests.index')
                ->with('info', 'Anda sudah memiliki permintaan seller yang sedang diproses atau sudah disetujui.');
        }

        // Check if user is already a seller
        if ($user->isSeller()) {
            return redirect()->route('dashboard')
                ->with('info', 'Anda sudah menjadi seller.');
        }

        return view('user.seller-requests.create');
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
        if ($sellerRequest->user_id !== Auth::id()) {
            abort(403);
        }

        $sellerRequest->load('user');
        
        return view('user.seller-requests.show', compact('sellerRequest'));
    }
}
