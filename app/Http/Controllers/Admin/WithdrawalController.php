<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Models\User;
use App\Models\Notification;
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
            ->when($request->bank, function ($query, $bank) {
                return $query->where('bank_name', 'like', "%{$bank}%");
            })
            ->when($request->date_from, function ($query, $dateFrom) {
                return $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function ($query, $dateTo) {
                return $query->whereDate('created_at', '<=', $dateTo);
            })
            ->when($request->search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                      ->orWhere('full_name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15);

        // Stats for dashboard cards
        $stats = [
            'pending' => WithdrawalRequest::where('status', WithdrawalRequest::STATUS_PENDING)->count(),
            'processing' => WithdrawalRequest::where('status', WithdrawalRequest::STATUS_PROCESSING)->count(),
            'completed' => WithdrawalRequest::where('status', WithdrawalRequest::STATUS_COMPLETED)->count(),
            'rejected' => WithdrawalRequest::where('status', WithdrawalRequest::STATUS_REJECTED)->count(),
        ];

        $totalAmount = WithdrawalRequest::where('status', WithdrawalRequest::STATUS_COMPLETED)->sum('amount');

        return view('admin.withdrawals.index', compact('withdrawals', 'stats', 'totalAmount'));
    }

    /**
     * Display the specified withdrawal request.
     */
    public function show(WithdrawalRequest $withdrawal): View
    {
        $withdrawal->load(['user.detail', 'processedBy']);
        
        // User stats for sidebar
        $userStats = [
            'total_withdrawals' => WithdrawalRequest::where('user_id', $withdrawal->user_id)->count(),
            'completed_withdrawals' => WithdrawalRequest::where('user_id', $withdrawal->user_id)
                ->where('status', WithdrawalRequest::STATUS_COMPLETED)->count(),
            'total_amount' => WithdrawalRequest::where('user_id', $withdrawal->user_id)
                ->where('status', WithdrawalRequest::STATUS_COMPLETED)->sum('amount'),
        ];
        
        return view('admin.withdrawals.show', compact('withdrawal', 'userStats'));
    }

    /**
     * Process a withdrawal request (mark as processing).
     */
    public function process(Request $request, WithdrawalRequest $withdrawal): RedirectResponse
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ], [
            'admin_notes.max' => 'Catatan admin maksimal 1000 karakter.',
        ]);

        if (!$withdrawal->isPending()) {
            return back()->with('error', 'Hanya permintaan dengan status "Menunggu" yang dapat diproses.');
        }

        $withdrawal->update([
            'status' => WithdrawalRequest::STATUS_PROCESSING,
            'processed_at' => now(),
            'processed_by' => auth()->id(),
            'admin_notes' => $request->admin_notes,
        ]);

        // Create notification for user
        $this->createWithdrawalNotification($withdrawal, 'withdrawal', 'Penarikan Sedang Diproses', 
            "Permintaan penarikan Anda sebesar Rp" . number_format((float) $withdrawal->amount, 0, ',', '.') . " sedang diproses oleh tim finance.");

        return back()->with('success', 'Permintaan penarikan berhasil diproses. Status berubah menjadi "Diproses".');
    }

    /**
     * Complete a withdrawal request.
     */
    public function complete(Request $request, WithdrawalRequest $withdrawal): RedirectResponse
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ], [
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
            'admin_notes' => $request->admin_notes,
        ]);

        // Create notification for user
        $this->createWithdrawalNotification($withdrawal, 'withdrawal', 'Penarikan Berhasil Diselesaikan', 
            "Penarikan Anda sebesar Rp" . number_format((float) $withdrawal->amount, 0, ',', '.') . " ke rekening {$withdrawal->bank_name} telah berhasil ditransfer.");

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
        $withdrawal->user->addBalance((int) $withdrawal->total_deducted);

        $withdrawal->update([
            'status' => WithdrawalRequest::STATUS_REJECTED,
            'processed_at' => now(),
            'processed_by' => auth()->id(),
            'admin_notes' => $request->admin_notes,
        ]);

        // Create notification for user
        $this->createWithdrawalNotification($withdrawal, 'withdrawal', 'Penarikan Ditolak', 
            "Permintaan penarikan Anda sebesar Rp" . number_format((float) $withdrawal->amount, 0, ',', '.') . " ditolak. Alasan: {$request->admin_notes}. Saldo telah dikembalikan.");

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
            'action' => 'required|in:process,complete,reject',
            'withdrawal_ids' => 'required|array',
            'withdrawal_ids.*' => 'exists:withdrawal_requests,id',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $withdrawalIds = $request->withdrawal_ids;
        $action = $request->action;
        $adminNotes = $request->admin_notes;

        $withdrawals = WithdrawalRequest::whereIn('id', $withdrawalIds)
            ->whereIn('status', [WithdrawalRequest::STATUS_PENDING, WithdrawalRequest::STATUS_PROCESSING])
            ->get();

        if ($withdrawals->isEmpty()) {
            return back()->with('error', 'Tidak ada permintaan yang valid untuk diproses.');
        }

        $processedCount = 0;

        foreach ($withdrawals as $withdrawal) {
            if ($action === 'process' && $withdrawal->isPending()) {
                $withdrawal->update([
                    'status' => WithdrawalRequest::STATUS_PROCESSING,
                    'processed_at' => now(),
                    'processed_by' => auth()->id(),
                    'admin_notes' => $adminNotes,
                ]);
                
                // Create notification
                $this->createWithdrawalNotification($withdrawal, 'withdrawal', 'Penarikan Sedang Diproses', 
                    "Permintaan penarikan Anda sebesar Rp" . number_format((float) $withdrawal->amount, 0, ',', '.') . " sedang diproses oleh tim finance.");
                
                $processedCount++;
            } elseif ($action === 'complete' && ($withdrawal->isProcessing() || $withdrawal->isPending())) {
                $withdrawal->update([
                    'status' => WithdrawalRequest::STATUS_COMPLETED,
                    'completed_at' => now(),
                    'processed_at' => $withdrawal->processed_at ?? now(),
                    'processed_by' => auth()->id(),
                    'admin_notes' => $adminNotes,
                ]);
                
                // Create notification
                $this->createWithdrawalNotification($withdrawal, 'withdrawal', 'Penarikan Berhasil Diselesaikan', 
                    "Penarikan Anda sebesar Rp" . number_format((float) $withdrawal->amount, 0, ',', '.') . " ke rekening {$withdrawal->bank_name} telah berhasil ditransfer.");
                
                $processedCount++;
            } elseif ($action === 'reject' && ($withdrawal->isPending() || $withdrawal->isProcessing())) {
                // Refund balance
                $withdrawal->user->addBalance((int) $withdrawal->total_deducted);
                
                $withdrawal->update([
                    'status' => WithdrawalRequest::STATUS_REJECTED,
                    'processed_at' => now(),
                    'processed_by' => auth()->id(),
                    'admin_notes' => $adminNotes ?: 'Ditolak secara massal',
                ]);
                
                // Create notification
                $this->createWithdrawalNotification($withdrawal, 'withdrawal', 'Penarikan Ditolak', 
                    "Permintaan penarikan Anda sebesar Rp" . number_format((float) $withdrawal->amount, 0, ',', '.') . " ditolak. Alasan: " . ($adminNotes ?: 'Ditolak secara massal') . ". Saldo telah dikembalikan.");
                
                $processedCount++;
            }
        }

        $actionText = $action === 'process' ? 'diproses' : ($action === 'complete' ? 'diselesaikan' : 'ditolak');
        return back()->with('success', "{$processedCount} permintaan penarikan berhasil {$actionText}.");
    }

    /**
     * API endpoint to get pending withdrawal count for sidebar badge.
     */
    public function getPendingCount()
    {
        $count = WithdrawalRequest::where('status', WithdrawalRequest::STATUS_PENDING)->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Create notification for withdrawal-related actions
     */
    private function createWithdrawalNotification(WithdrawalRequest $withdrawal, string $category, string $title, string $description): void
    {
        Notification::create([
            'for_user_id' => $withdrawal->user_id,
            'category' => $category,
            'title' => $title,
            'description' => $description,
        ]);
    }

    /**
     * Update withdrawal status directly to any valid status
     */
    public function updateStatus(Request $request, WithdrawalRequest $withdrawal): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,rejected,cancelled',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $withdrawal->status;
        $newStatus = $request->status;

        // Prevent certain impossible transitions
        if ($oldStatus === WithdrawalRequest::STATUS_COMPLETED && $newStatus !== WithdrawalRequest::STATUS_COMPLETED) {
            return back()->withErrors(['status' => 'Penarikan yang sudah selesai tidak dapat diubah statusnya.']);
        }

        // Handle balance refund when changing to rejected/cancelled
        if (in_array($newStatus, [WithdrawalRequest::STATUS_REJECTED, WithdrawalRequest::STATUS_CANCELLED]) && 
            !in_array($oldStatus, [WithdrawalRequest::STATUS_REJECTED, WithdrawalRequest::STATUS_CANCELLED])) {
            // Refund balance if changing to rejected/cancelled from other status
            $withdrawal->user->addBalance((int) $withdrawal->total_deducted);
        } elseif (!in_array($newStatus, [WithdrawalRequest::STATUS_REJECTED, WithdrawalRequest::STATUS_CANCELLED]) && 
                  in_array($oldStatus, [WithdrawalRequest::STATUS_REJECTED, WithdrawalRequest::STATUS_CANCELLED])) {
            // Deduct balance if changing from rejected/cancelled to other status
            if (!$withdrawal->user->hasSufficientBalance($withdrawal->total_deducted)) {
                return back()->withErrors(['status' => 'Saldo user tidak mencukupi untuk mengaktifkan kembali penarikan ini.']);
            }
            $withdrawal->user->deductBalance($withdrawal->total_deducted);
        }

        // Update withdrawal
        $withdrawal->update([
            'status' => $newStatus,
            'processed_at' => in_array($newStatus, [WithdrawalRequest::STATUS_PROCESSING, WithdrawalRequest::STATUS_COMPLETED, WithdrawalRequest::STATUS_REJECTED]) ? now() : $withdrawal->processed_at,
            'completed_at' => $newStatus === WithdrawalRequest::STATUS_COMPLETED ? now() : null,
            'processed_by' => auth()->id(),
            'admin_notes' => $request->admin_notes,
        ]);

        // Create notification
        $statusLabels = WithdrawalRequest::getStatuses();
        $statusLabel = $statusLabels[$newStatus] ?? ucfirst($newStatus);
        
        $descriptions = [
            WithdrawalRequest::STATUS_PENDING => "Permintaan penarikan Anda sebesar Rp" . number_format((float) $withdrawal->amount, 0, ',', '.') . " dikembalikan ke status pending.",
            WithdrawalRequest::STATUS_PROCESSING => "Permintaan penarikan Anda sebesar Rp" . number_format((float) $withdrawal->amount, 0, ',', '.') . " sedang diproses oleh tim finance.",
            WithdrawalRequest::STATUS_COMPLETED => "Penarikan Anda sebesar Rp" . number_format((float) $withdrawal->amount, 0, ',', '.') . " ke rekening {$withdrawal->bank_name} telah berhasil ditransfer.",
            WithdrawalRequest::STATUS_REJECTED => "Permintaan penarikan Anda sebesar Rp" . number_format((float) $withdrawal->amount, 0, ',', '.') . " ditolak. Saldo telah dikembalikan.",
            WithdrawalRequest::STATUS_CANCELLED => "Permintaan penarikan Anda sebesar Rp" . number_format((float) $withdrawal->amount, 0, ',', '.') . " dibatalkan. Saldo telah dikembalikan.",
        ];

        $description = $descriptions[$newStatus] ?? "Status penarikan Anda berubah menjadi {$statusLabel}.";
        if ($request->admin_notes && in_array($newStatus, [WithdrawalRequest::STATUS_REJECTED, WithdrawalRequest::STATUS_CANCELLED])) {
            $description .= " Alasan: {$request->admin_notes}";
        }

        $this->createWithdrawalNotification($withdrawal, 'withdrawal', "Penarikan {$statusLabel}", $description);

        return back()->with('success', "Status penarikan berhasil diubah dari {$statusLabels[$oldStatus]} menjadi {$statusLabel}.");
    }
}
