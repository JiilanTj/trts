<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWithdrawalRequest;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of user's withdrawal requests.
     */
    public function index(Request $request): View
    {
        $withdrawals = auth()->user()->withdrawalRequests()
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => auth()->user()->withdrawalRequests()->count(),
            'pending' => auth()->user()->withdrawalRequests()->where('status', WithdrawalRequest::STATUS_PENDING)->count(),
            'processing' => auth()->user()->withdrawalRequests()->where('status', WithdrawalRequest::STATUS_PROCESSING)->count(),
            'completed' => auth()->user()->withdrawalRequests()->where('status', WithdrawalRequest::STATUS_COMPLETED)->count(),
            'rejected' => auth()->user()->withdrawalRequests()->where('status', WithdrawalRequest::STATUS_REJECTED)->count(),
        ];

        return view('user.withdrawals.index', compact('withdrawals', 'stats'));
    }

    /**
     * Show the form for creating a new withdrawal request.
     */
    public function create(): View
    {
        $user = auth()->user();
        
        return view('user.withdrawals.create', compact('user'));
    }

    /**
     * Store a newly created withdrawal request.
     */
    public function store(StoreWithdrawalRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $adminFee = $request->getAdminFee();
        $totalDeducted = $request->getTotalDeducted();

        // Double check balance
        if (!$user->hasSufficientBalance($totalDeducted)) {
            return back()->withErrors(['amount' => 'Saldo tidak mencukupi untuk penarikan ini.'])->withInput();
        }

        // Create withdrawal request
        $withdrawal = $user->withdrawalRequests()->create([
            'account_holder_name' => $request->account_holder_name,
            'account_number' => $request->account_number,
            'bank_name' => $request->bank_name,
            'bank_code' => $request->bank_code,
            'amount' => $request->amount,
            'admin_fee' => $adminFee,
            'total_deducted' => $totalDeducted,
            'notes' => $request->notes,
            'status' => WithdrawalRequest::STATUS_PENDING,
        ]);

        // Deduct balance immediately (hold the funds)
        $user->deductBalance($totalDeducted);

        return redirect()->route('user.withdrawals.show', $withdrawal)
            ->with('success', 'Permintaan penarikan berhasil dibuat. Dana telah ditahan dan akan diproses dalam 1-2 hari kerja.');
    }

    /**
     * Display the specified withdrawal request.
     */
    public function show(WithdrawalRequest $withdrawal): View
    {
        // Ensure user can only see their own withdrawals
        if ($withdrawal->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        return view('user.withdrawals.show', compact('withdrawal'));
    }

    /**
     * Show the form for editing the specified withdrawal (not implemented for security).
     */
    public function edit(WithdrawalRequest $withdrawal): RedirectResponse
    {
        return redirect()->route('user.withdrawals.index')
            ->with('error', 'Permintaan penarikan tidak dapat diedit setelah dibuat.');
    }

    /**
     * Update the specified withdrawal (not implemented for security).
     */
    public function update(Request $request, WithdrawalRequest $withdrawal): RedirectResponse
    {
        return redirect()->route('user.withdrawals.index')
            ->with('error', 'Permintaan penarikan tidak dapat diedit setelah dibuat.');
    }

    /**
     * Cancel a pending withdrawal request.
     */
    public function destroy(WithdrawalRequest $withdrawal): RedirectResponse
    {
        // Ensure user can only cancel their own withdrawals
        if ($withdrawal->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        // Only pending withdrawals can be cancelled
        if (!$withdrawal->canBeCancelled()) {
            return back()->with('error', 'Hanya permintaan penarikan yang masih menunggu yang dapat dibatalkan.');
        }

        // Refund the balance
        auth()->user()->addBalance($withdrawal->total_deducted);
        
        // Update status
        $withdrawal->update(['status' => WithdrawalRequest::STATUS_CANCELLED]);

        return redirect()->route('user.withdrawals.index')
            ->with('success', 'Permintaan penarikan dibatalkan dan saldo dikembalikan.');
    }

    /**
     * Get withdrawal preview (AJAX endpoint)
     */
    public function preview(Request $request)
    {
        $amount = (float) $request->input('amount', 0);
        
        if ($amount < 10000) {
            return response()->json(['error' => 'Nominal minimal Rp 10.000'], 400);
        }

        // Calculate admin fee using same logic as form request
        if ($amount <= 100000) {
            $adminFee = 2500;
        } elseif ($amount <= 500000) {
            $adminFee = $amount * 0.025;
        } elseif ($amount <= 1000000) {
            $adminFee = $amount * 0.02;
        } else {
            $adminFee = $amount * 0.015;
        }

        $totalDeducted = $amount + $adminFee;

        return response()->json([
            'amount' => $amount,
            'admin_fee' => $adminFee,
            'total_deducted' => $totalDeducted,
            'formatted_amount' => 'Rp ' . number_format($amount, 0, ',', '.'),
            'formatted_admin_fee' => 'Rp ' . number_format($adminFee, 0, ',', '.'),
            'formatted_total_deducted' => 'Rp ' . number_format($totalDeducted, 0, ',', '.'),
            'sufficient_balance' => auth()->user()->hasSufficientBalance($totalDeducted),
            'current_balance' => auth()->user()->balance,
            'formatted_current_balance' => 'Rp ' . number_format(auth()->user()->balance, 0, ',', '.'),
        ]);
    }
}
