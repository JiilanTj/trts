<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTopupRequest;
use App\Models\TopupRequest;
use App\Models\Notification;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TopupController extends Controller
{
    public function index()
    {
        $topupRequests = Auth::user()->topupRequests()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $hasPendingTopup = Auth::user()->topupRequests()
            ->pending()
            ->exists();

        return view('user.topup.index', compact('topupRequests', 'hasPendingTopup'));
    }

    public function create()
    {
        // Check if user has pending topup request
        $hasPendingTopup = Auth::user()->topupRequests()
            ->pending()
            ->exists();

        if ($hasPendingTopup) {
            return redirect()->route('user.topup.index')
                ->with('error', 'Anda masih memiliki permintaan topup yang belum diproses. Silakan tunggu hingga disetujui atau ditolak.');
        }

        // Get bank account from settings (single row)
        $setting = Setting::first();
        
        return view('user.topup.create', compact('setting'));
    }

    public function store(StoreTopupRequest $request)
    {
        // Upload payment proof
        $paymentProofPath = $request->file('payment_proof')->store('topup-proofs', 'public');

        // Create topup request
        $topupRequest = TopupRequest::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'bank_name' => $request->bank_name,
            'bank_account' => $request->bank_account,
            'transfer_date' => now(), // Auto set to current date
            'payment_proof' => $paymentProofPath,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        // Create notification for user
        Notification::create([
            'for_user_id' => Auth::id(),
            'category' => 'topup',
            'title' => 'Permintaan Topup Dikirim',
            'description' => 'Permintaan topup saldo sebesar Rp ' . number_format($request->amount, 0, ',', '.') . ' berhasil dikirim dan sedang menunggu persetujuan admin.',
        ]);

        // Create notification for admin (optional - if you want to notify admin)
        Notification::create([
            'for_user_id' => Auth::id(), // For the user who made the topup request
            'category' => 'admin_topup',
            'title' => 'Permintaan Topup Baru',
            'description' => 'User ' . Auth::user()->username . ' mengajukan topup saldo sebesar Rp ' . number_format($request->amount, 0, ',', '.') . '. ID Request: #' . $topupRequest->id,
        ]);

        return redirect()->route('user.topup.index')
            ->with('success', 'Permintaan topup berhasil dikirim! Admin akan memproses dalam 1x24 jam.');
    }

    public function show(TopupRequest $topupRequest)
    {
        // Ensure user can only view their own topup requests
        if ($topupRequest->user_id !== Auth::id()) {
            abort(403);
        }

        return view('user.topup.show', compact('topupRequest'));
    }
}
