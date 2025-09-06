<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\LoanRequest;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LoanRequestController extends Controller
{
    /**
     * Display a listing of the user's loan requests.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        
        $loanRequests = LoanRequest::forUser($user->id)
            ->when($request->status, function ($query, $status) {
                return $query->byStatus($status);
            })
            ->latest()
            ->paginate(10);

        $stats = [
            'total_requests' => LoanRequest::forUser($user->id)->count(),
            'pending_requests' => LoanRequest::forUser($user->id)->byStatus('pending')->count(),
            'approved_requests' => LoanRequest::forUser($user->id)->byStatus('approved')->count(),
            'active_loans' => LoanRequest::forUser($user->id)->byStatus('active')->count(),
            'total_borrowed' => LoanRequest::forUser($user->id)
                ->whereIn('status', ['disbursed', 'active', 'completed'])
                ->sum('amount_requested'),
        ];

        return view('user.loan-requests.index', compact('loanRequests', 'stats'));
    }

    /**
     * Show the form for creating a new loan request.
     */
    public function create(): View
    {
        $purposes = [
            'business_expansion' => 'Ekspansi Bisnis',
            'inventory' => 'Pembelian Inventori',
            'equipment' => 'Pembelian Peralatan',
            'working_capital' => 'Modal Kerja',
            'other' => 'Lainnya',
        ];

        return view('user.loan-requests.create', compact('purposes'));
    }

    /**
     * Store a newly created loan request.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount_requested' => 'required|integer|min:1000000|max:1000000000', // Min 1jt, Max 1M
            'duration_months' => 'required|integer|min:3|max:60', // 3 months to 5 years
            'purpose' => ['required', Rule::in(['business_expansion', 'inventory', 'equipment', 'working_capital', 'other'])],
            'purpose_description' => 'required|string|max:1000',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // 2MB max per file
        ]);

        $user = $request->user();

        // Check if user has any pending/under_review loan requests
        $existingPendingRequest = LoanRequest::forUser($user->id)
            ->whereIn('status', ['pending', 'under_review'])
            ->exists();

        if ($existingPendingRequest) {
            return back()->withErrors([
                'general' => 'You already have a pending loan request. Please wait for it to be processed.'
            ]);
        }

        // Handle document uploads
        $documents = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('loan-documents', 'public');
                $documents[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'uploaded_at' => now(),
                ];
            }
        }

        // Calculate estimated interest rate based on user's credit score and loan amount
        $interestRate = $this->calculateInterestRate($user, $validated['amount_requested']);

        $loanRequest = LoanRequest::create([
            'user_id' => $user->id,
            'amount_requested' => $validated['amount_requested'],
            'duration_months' => $validated['duration_months'],
            'purpose' => $validated['purpose'],
            'purpose_description' => $validated['purpose_description'],
            'interest_rate' => $interestRate,
            'documents' => $documents,
            'status' => 'pending',
        ]);

        // Calculate and save monthly payment
        $loanRequest->monthly_payment = $loanRequest->calculateMonthlyPayment();
        $loanRequest->save();

        // Create notification for user
        $this->createLoanNotification($loanRequest, 'Pengajuan Pinjaman Diterima', 
            'Pengajuan pinjaman Anda sebesar ' . $loanRequest->formatted_amount . ' telah diterima dan sedang dalam proses peninjauan. Tim kami akan segera menghubungi Anda.');

        return redirect()->route('user.loan-requests.show', $loanRequest)
            ->with('success', 'Pengajuan pinjaman berhasil dikirim! Kami akan meninjau aplikasi Anda dan segera menghubungi Anda.');
    }

    /**
     * Display the specified loan request.
     */
    public function show(LoanRequest $loanRequest): View
    {
        // Ensure user can only view their own loan requests
        if ($loanRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to loan request.');
        }

        return view('user.loan-requests.show', compact('loanRequest'));
    }

    /**
     * Show the form for editing the specified loan request.
     */
    public function edit(LoanRequest $loanRequest): View
    {
        // Ensure user can only edit their own pending loan requests
        if ($loanRequest->user_id !== Auth::id() || $loanRequest->status !== 'pending') {
            abort(403, 'Cannot edit this loan request.');
        }

        $purposes = [
            'business_expansion' => 'Ekspansi Bisnis',
            'inventory' => 'Pembelian Inventori',
            'equipment' => 'Pembelian Peralatan',
            'working_capital' => 'Modal Kerja',
            'other' => 'Lainnya',
        ];

        return view('user.loan-requests.edit', compact('loanRequest', 'purposes'));
    }

    /**
     * Update the specified loan request.
     */
    public function update(Request $request, LoanRequest $loanRequest): RedirectResponse
    {
        // Ensure user can only update their own pending loan requests
        if ($loanRequest->user_id !== Auth::id() || $loanRequest->status !== 'pending') {
            abort(403, 'Cannot update this loan request.');
        }

        $validated = $request->validate([
            'amount_requested' => 'required|integer|min:1000000|max:1000000000',
            'duration_months' => 'required|integer|min:3|max:60',
            'purpose' => ['required', Rule::in(['business_expansion', 'inventory', 'equipment', 'working_capital', 'other'])],
            'purpose_description' => 'required|string|max:1000',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Handle document uploads
        $documents = $loanRequest->documents ?? [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('loan-documents', 'public');
                $documents[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'uploaded_at' => now(),
                ];
            }
        }

        // Recalculate interest rate
        $interestRate = $this->calculateInterestRate($request->user(), $validated['amount_requested']);

        $loanRequest->update([
            'amount_requested' => $validated['amount_requested'],
            'duration_months' => $validated['duration_months'],
            'purpose' => $validated['purpose'],
            'purpose_description' => $validated['purpose_description'],
            'interest_rate' => $interestRate,
            'documents' => $documents,
        ]);

        // Recalculate monthly payment
        $loanRequest->monthly_payment = $loanRequest->calculateMonthlyPayment();
        $loanRequest->save();

        // Create notification for user
        $this->createLoanNotification($loanRequest, 'Pengajuan Pinjaman Diperbarui', 
            'Pengajuan pinjaman Anda sebesar ' . $loanRequest->formatted_amount . ' telah berhasil diperbarui. Tim kami akan meninjau ulang perubahan yang Anda buat.');

        return redirect()->route('user.loan-requests.show', $loanRequest)
            ->with('success', 'Pengajuan pinjaman berhasil diperbarui!');
    }

    /**
     * Remove the specified loan request.
     */
    public function destroy(LoanRequest $loanRequest): RedirectResponse
    {
        // Ensure user can only delete their own pending loan requests
        if ($loanRequest->user_id !== Auth::id() || $loanRequest->status !== 'pending') {
            abort(403, 'Cannot delete this loan request.');
        }

        // Delete uploaded documents
        if ($loanRequest->documents) {
            foreach ($loanRequest->documents as $document) {
                Storage::disk('public')->delete($document['path']);
            }
        }

        // Create notification for user
        $this->createLoanNotification($loanRequest, 'Pengajuan Pinjaman Dibatalkan', 
            'Pengajuan pinjaman Anda sebesar ' . $loanRequest->formatted_amount . ' telah dibatalkan sesuai permintaan Anda.');

        $loanRequest->delete();

        return redirect()->route('user.loan-requests.index')
            ->with('success', 'Pengajuan pinjaman berhasil dibatalkan.');
    }

    /**
     * Calculate interest rate based on user's profile and loan amount
     */
    private function calculateInterestRate($user, $amount): float
    {
        $baseRate = 12.0; // Base annual interest rate

        // Adjust based on user's credit score
        $creditScore = $user->credit_score ?? 0;
        if ($creditScore >= 700) {
            $baseRate -= 2.0; // Good credit gets lower rate
        } elseif ($creditScore >= 600) {
            $baseRate -= 1.0; // Fair credit gets slight discount
        } elseif ($creditScore < 500) {
            $baseRate += 3.0; // Poor credit gets higher rate
        }

        // Adjust based on loan amount (larger loans get better rates)
        if ($amount >= 50000000) { // 50M+
            $baseRate -= 1.5;
        } elseif ($amount >= 20000000) { // 20M+
            $baseRate -= 1.0;
        } elseif ($amount >= 10000000) { // 10M+
            $baseRate -= 0.5;
        }

        // Adjust based on user type
        if ($user->isSeller() && $user->sellerInfo) {
            $baseRate -= 1.0; // Sellers get preferential rates
        }

        // Ensure rate is within reasonable bounds
        return max(8.0, min(25.0, $baseRate));
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
}
