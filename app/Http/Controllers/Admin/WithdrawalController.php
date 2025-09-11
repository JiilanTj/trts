<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of all withdrawal requests.
     */
    public function index(Request $request): View
    {
        $withdrawals = WithdrawalRequest::with(['user'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                      ->orWhere('full_name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15);

        $statusCounts = [
            'all' => WithdrawalRequest::count(),
            'pending' => WithdrawalRequest::where('status', WithdrawalRequest::STATUS_PENDING)->count(),
            'processing' => WithdrawalRequest::where('status', WithdrawalRequest::STATUS_PROCESSING)->count(),
            'completed' => WithdrawalRequest::where('status', WithdrawalRequest::STATUS_COMPLETED)->count(),
            'rejected' => WithdrawalRequest::where('status', WithdrawalRequest::STATUS_REJECTED)->count(),
            'cancelled' => WithdrawalRequest::where('status', WithdrawalRequest::STATUS_CANCELLED)->count(),
        ];

        return view('admin.withdrawals.index', compact('withdrawals', 'statusCounts'));
    }

    /**
     * Display the specified withdrawal request.
     */
    public function show(WithdrawalRequest $withdrawal): View
    {
        $withdrawal->load(['user', 'processedBy']);
        
        return view('admin.withdrawals.show', compact('withdrawal'));
    }

    /**
     * Process a withdrawal request (mark as processing).
     */
    public function process(WithdrawalRequest $withdrawal): RedirectResponse
    {
        if (!$withdrawal->isPending()) {
            return back()->with('error', 'Hanya permintaan dengan status "Menunggu" yang dapat diproses.');
        }

        $withdrawal->update([
            'status' => WithdrawalRequest::STATUS_PROCESSING,
            'processed_at' => now(),
            'processed_by' => auth()->id(),
        ]);

        return back()->with('success', 'Permintaan penarikan berhasil diproses. Status berubah menjadi "Diproses".');
    }

    /**
     * Complete a withdrawal request.
     */
    public function complete(Request $request, WithdrawalRequest $withdrawal): RedirectResponse
    {
        $request->validate([
            'transaction_reference' => 'required|string|max:255',
            'admin_notes' => 'nullable|string|max:1000',
        ], [
            'transaction_reference.required' => 'Referensi transaksi harus diisi.',
            'transaction_reference.max' => 'Referensi transaksi maksimal 255 karakter.',
            'admin_notes.max' => 'Catatan admin maksimal 1000 karakter.',
        ]);

        if (!$withdrawal->isProcessing() && !$withdrawal->isPending()) {
            return back()->with('error', 'Hanya permintaan yang sedang diproses yang dapat diselesaikan.');
        }

        $withdrawal->update([
            'status' => WithdrawalRequest::STATUS_COMPLETED,
            'completed_at' => now(),
            'processed_at' => $withdrawal->processed_at ?? now(),
            'processed_by' => auth()->id(),
            'transaction_reference' => $request->transaction_reference,
            'admin_notes' => $request->admin_notes,
        ]);

        // TODO: Send notification to user about completion

        return back()->with('success', 'Permintaan penarikan berhasil diselesaikan.');
    }

    /**
     * Reject a withdrawal request.
     */
    public function reject(Request $request, WithdrawalRequest $withdrawal): RedirectResponse
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ], [
            'admin_notes.required' => 'Alasan penolakan harus diisi.',
            'admin_notes.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ]);

        if ($withdrawal->isCompleted()) {
            return back()->with('error', 'Permintaan yang sudah diselesaikan tidak dapat ditolak.');
        }

        // Refund the balance to user
        $withdrawal->user->addBalance($withdrawal->total_deducted);

        $withdrawal->update([
            'status' => WithdrawalRequest::STATUS_REJECTED,
            'processed_at' => now(),
            'processed_by' => auth()->id(),
            'admin_notes' => $request->admin_notes,
        ]);

        // TODO: Send notification to user about rejection

        return back()->with('success', 'Permintaan penarikan ditolak dan saldo dikembalikan ke user.');
    }

    /**
     * Get withdrawal statistics for dashboard.
     */
    public function stats(): array
    {
        $totalAmount = WithdrawalRequest::where('status', WithdrawalRequest::STATUS_COMPLETED)
            ->sum('amount');
        
        $totalFees = WithdrawalRequest::where('status', WithdrawalRequest::STATUS_COMPLETED)
            ->sum('admin_fee');
        
        $pendingAmount = WithdrawalRequest::where('status', WithdrawalRequest::STATUS_PENDING)
            ->sum('total_deducted');
        
        $todayWithdrawals = WithdrawalRequest::whereDate('created_at', today())->count();
        
        return [
            'total_completed_amount' => $totalAmount,
            'total_admin_fees' => $totalFees,
            'pending_amount' => $pendingAmount,
            'today_withdrawals' => $todayWithdrawals,
            'formatted_total_amount' => 'Rp ' . number_format($totalAmount, 0, ',', '.'),
            'formatted_total_fees' => 'Rp ' . number_format($totalFees, 0, ',', '.'),
            'formatted_pending_amount' => 'Rp ' . number_format($pendingAmount, 0, ',', '.'),
        ];
    }

    /**
     * Bulk actions for multiple withdrawals.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:process,reject',
            'withdrawal_ids' => 'required|array',
            'withdrawal_ids.*' => 'exists:withdrawal_requests,id',
            'bulk_admin_notes' => 'nullable|string|max:1000',
        ]);

        $withdrawalIds = $request->withdrawal_ids;
        $action = $request->action;
        $adminNotes = $request->bulk_admin_notes;

        $withdrawals = WithdrawalRequest::whereIn('id', $withdrawalIds)
            ->where('status', WithdrawalRequest::STATUS_PENDING)
            ->get();

        if ($withdrawals->isEmpty()) {
            return back()->with('error', 'Tidak ada permintaan yang valid untuk diproses.');
        }

        $processedCount = 0;

        foreach ($withdrawals as $withdrawal) {
            if ($action === 'process') {
                $withdrawal->update([
                    'status' => WithdrawalRequest::STATUS_PROCESSING,
                    'processed_at' => now(),
                    'processed_by' => auth()->id(),
                    'admin_notes' => $adminNotes,
                ]);
                $processedCount++;
            } elseif ($action === 'reject') {
                // Refund balance
                $withdrawal->user->addBalance($withdrawal->total_deducted);
                
                $withdrawal->update([
                    'status' => WithdrawalRequest::STATUS_REJECTED,
                    'processed_at' => now(),
                    'processed_by' => auth()->id(),
                    'admin_notes' => $adminNotes ?: 'Ditolak secara massal',
                ]);
                $processedCount++;
            }
        }

        $actionText = $action === 'process' ? 'diproses' : 'ditolak';
        return back()->with('success', "{$processedCount} permintaan penarikan berhasil {$actionText}.");
    }
}
