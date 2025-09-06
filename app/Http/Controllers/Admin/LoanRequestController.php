<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanRequest;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LoanRequestController extends Controller
{
    /**
     * Display a listing of all loan requests.
     */
    public function index(Request $request): View
    {
        $loanRequests = LoanRequest::with('user')
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15);

        $stats = [
            'total_requests' => LoanRequest::count(),
            'pending_requests' => LoanRequest::where('status', 'pending')->count(),
            'under_review' => LoanRequest::where('status', 'under_review')->count(),
            'approved_requests' => LoanRequest::where('status', 'approved')->count(),
            'rejected_requests' => LoanRequest::where('status', 'rejected')->count(),
            'active_loans' => LoanRequest::where('status', 'active')->count(),
            'total_amount_requested' => LoanRequest::whereIn('status', ['pending', 'under_review', 'approved', 'disbursed', 'active'])
                ->sum('amount_requested'),
            'total_disbursed' => LoanRequest::whereIn('status', ['disbursed', 'active', 'completed'])
                ->sum('amount_requested'),
        ];

        return view('admin.loan-requests.index', compact('loanRequests', 'stats'));
    }

    /**
     * Display the specified loan request.
     */
    public function show(LoanRequest $loanRequest): View
    {
        $loanRequest->load('user', 'approvedBy');
        
        return view('admin.loan-requests.show', compact('loanRequest'));
    }

    /**
     * Update loan request status and add admin notes.
     */
    public function updateStatus(Request $request, LoanRequest $loanRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,under_review,approved,rejected,disbursed,active,completed,defaulted',
            'admin_notes' => 'nullable|string|max:1000',
            'rejection_reason' => 'nullable|string|max:500|required_if:status,rejected',
            'interest_rate' => 'nullable|numeric|min:1|max:50|required_if:status,approved',
            'due_date' => 'nullable|date|after:today|required_if:status,disbursed',
        ]);

        $oldStatus = $loanRequest->status;
        
        $updateData = [
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'],
        ];

        // Handle status-specific updates
        switch ($validated['status']) {
            case 'approved':
                $updateData['approved_at'] = now();
                $updateData['approved_by'] = Auth::id();
                $updateData['interest_rate'] = $validated['interest_rate'];
                
                // Recalculate monthly payment with new interest rate
                $loanRequest->interest_rate = $validated['interest_rate'];
                $updateData['monthly_payment'] = $loanRequest->calculateMonthlyPayment();
                break;
                
            case 'rejected':
                $updateData['rejection_reason'] = $validated['rejection_reason'];
                break;
                
            case 'disbursed':
                $updateData['disbursed_at'] = now();
                $updateData['due_date'] = $validated['due_date'];
                break;
                
            case 'active':
                if ($oldStatus === 'disbursed') {
                    // Loan becomes active after disbursement
                    $updateData['due_date'] = $loanRequest->due_date ?: now()->addMonths($loanRequest->duration_months);
                }
                break;
        }

        $loanRequest->update($updateData);

        $statusLabel = $this->getStatusLabel($validated['status']);
        
        // Create notification for status change
        $this->createLoanNotification($loanRequest, $statusLabel, $this->getNotificationMessage($validated['status'], $loanRequest, $validated));
        
        return redirect()->route('admin.loan-requests.show', $loanRequest)
            ->with('success', "Status pinjaman berhasil diubah menjadi: {$statusLabel}");
    }

    /**
     * Bulk update multiple loan requests.
     */
    public function bulkUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'loan_request_ids' => 'required|array',
            'loan_request_ids.*' => 'exists:loan_requests,id',
            'bulk_action' => 'required|in:approve,reject,under_review',
            'bulk_admin_notes' => 'nullable|string|max:1000',
            'bulk_rejection_reason' => 'nullable|string|max:500|required_if:bulk_action,reject',
        ]);

        $status = match($validated['bulk_action']) {
            'approve' => 'approved',
            'reject' => 'rejected',
            'under_review' => 'under_review',
        };

        $updateData = [
            'status' => $status,
            'admin_notes' => $validated['bulk_admin_notes'],
        ];

        if ($status === 'approved') {
            $updateData['approved_at'] = now();
            $updateData['approved_by'] = Auth::id();
        } elseif ($status === 'rejected') {
            $updateData['rejection_reason'] = $validated['bulk_rejection_reason'];
        }

        $affectedRequests = LoanRequest::whereIn('id', $validated['loan_request_ids'])
            ->where('status', 'pending')
            ->get();

        LoanRequest::whereIn('id', $validated['loan_request_ids'])
            ->where('status', 'pending')
            ->update($updateData);

        // Create notifications for affected loan requests
        foreach ($affectedRequests as $loanRequest) {
            $this->createLoanNotification($loanRequest, $this->getStatusLabel($status), 
                $this->getNotificationMessage($status, $loanRequest, $validated));
        }

        $count = count($validated['loan_request_ids']);
        $statusLabel = $this->getStatusLabel($status);
        
        return redirect()->route('admin.loan-requests.index')
            ->with('success', "{$count} pengajuan pinjaman berhasil diubah menjadi: {$statusLabel}");
    }

    /**
     * Download loan request document.
     */
    public function downloadDocument(LoanRequest $loanRequest, $documentIndex)
    {
        if (!isset($loanRequest->documents[$documentIndex])) {
            abort(404, 'Document not found.');
        }

        $document = $loanRequest->documents[$documentIndex];
        $filePath = storage_path('app/public/' . $document['path']);

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $document['name']);
    }

    /**
     * Generate loan analytics report.
     */
    public function analytics(): View
    {
        $monthlyStats = LoanRequest::selectRaw('
            YEAR(created_at) as year,
            MONTH(created_at) as month,
            COUNT(*) as total_requests,
            SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
            SUM(amount_requested) as total_amount
        ')
        ->where('created_at', '>=', now()->subMonths(12))
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

        $purposeStats = LoanRequest::selectRaw('
            purpose,
            COUNT(*) as count,
            SUM(amount_requested) as total_amount,
            AVG(amount_requested) as avg_amount
        ')
        ->groupBy('purpose')
        ->get();

        $statusStats = LoanRequest::selectRaw('
            status,
            COUNT(*) as count,
            SUM(amount_requested) as total_amount
        ')
        ->groupBy('status')
        ->get();

        return view('admin.loan-requests.analytics', compact('monthlyStats', 'purposeStats', 'statusStats'));
    }

    /**
     * Get status label in Indonesian.
     */
    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'pending' => 'Menunggu',
            'under_review' => 'Dalam Tinjauan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'disbursed' => 'Dicairkan',
            'active' => 'Aktif',
            'completed' => 'Selesai',
            'defaulted' => 'Gagal Bayar',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Create notification for loan request-related actions
     */
    private function createLoanNotification(LoanRequest $loanRequest, string $title, string $description): void
    {
        Notification::create([
            'for_user_id' => $loanRequest->user_id,
            'category' => 'loan',
            'title' => $title,
            'description' => $description,
        ]);
    }

    /**
     * Get notification message based on status change
     */
    private function getNotificationMessage(string $status, LoanRequest $loanRequest, array $validated): string
    {
        return match($status) {
            'under_review' => 'Pengajuan pinjaman Anda sebesar ' . $loanRequest->formatted_amount . ' sedang dalam proses tinjauan oleh tim kami. Kami akan segera menghubungi Anda.',
            'approved' => 'Selamat! Pengajuan pinjaman Anda sebesar ' . $loanRequest->formatted_amount . ' telah disetujui dengan suku bunga ' . number_format($validated['interest_rate'], 2) . '% per tahun. Cicilan bulanan Anda adalah ' . $loanRequest->formatted_monthly_payment . '.',
            'rejected' => 'Maaf, pengajuan pinjaman Anda sebesar ' . $loanRequest->formatted_amount . ' tidak dapat disetujui. Alasan: ' . ($validated['rejection_reason'] ?? 'Tidak memenuhi kriteria penilaian kredit.'),
            'disbursed' => 'Dana pinjaman Anda sebesar ' . $loanRequest->formatted_amount . ' telah dicairkan ke akun Anda. Jatuh tempo pembayaran pada ' . \Carbon\Carbon::parse($validated['due_date'])->format('d M Y') . '.',
            'active' => 'Pinjaman Anda sebesar ' . $loanRequest->formatted_amount . ' sekarang aktif. Jangan lupa melakukan pembayaran cicilan bulanan sebesar ' . $loanRequest->formatted_monthly_payment . ' tepat waktu.',
            'completed' => 'Selamat! Pinjaman Anda sebesar ' . $loanRequest->formatted_amount . ' telah lunas. Terima kasih atas kepercayaan Anda menggunakan layanan kami.',
            'defaulted' => 'Pinjaman Anda sebesar ' . $loanRequest->formatted_amount . ' mengalami tunggakan. Silakan hubungi customer service kami untuk penyelesaian.',
            default => 'Status pengajuan pinjaman Anda telah diperbarui.'
        };
    }
}
