<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount_requested',
        'duration_months',
        'purpose',
        'purpose_description',
        'disbursement_method',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'interest_rate',
        'monthly_payment',
        'status',
        'admin_notes',
        'disbursement_notes',
        'disbursement_reference',
        'rejection_reason',
        'documents',
        'credit_assessment',
        'approved_at',
        'disbursed_at',
        'due_date',
        'approved_by'
    ];

    protected $casts = [
        'documents' => 'array',
        'credit_assessment' => 'array',
        'approved_at' => 'datetime',
        'disbursed_at' => 'datetime',
        'due_date' => 'datetime',
        'amount_requested' => 'integer',
        'monthly_payment' => 'integer',
        'duration_months' => 'integer',
        'interest_rate' => 'decimal:2'
    ];

    /**
     * Get the user that owns the loan request
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who approved the loan
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Calculate monthly payment based on amount, duration, and interest rate
     */
    public function calculateMonthlyPayment(): int
    {
        if (!$this->interest_rate || $this->interest_rate == 0) {
            return intval($this->amount_requested / $this->duration_months);
        }

        $monthlyInterestRate = $this->interest_rate / 100 / 12;
        $numberOfPayments = $this->duration_months;

        $monthlyPayment = $this->amount_requested * 
            ($monthlyInterestRate * pow(1 + $monthlyInterestRate, $numberOfPayments)) /
            (pow(1 + $monthlyInterestRate, $numberOfPayments) - 1);

        return intval(round($monthlyPayment));
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount_requested, 0, ',', '.');
    }

    /**
     * Get formatted monthly payment
     */
    public function getFormattedMonthlyPaymentAttribute(): string
    {
        return 'Rp ' . number_format($this->monthly_payment, 0, ',', '.');
    }

    /**
     * Get purpose label
     */
    public function getPurposeLabelAttribute(): string
    {
        return match($this->purpose) {
            'business_expansion' => 'Ekspansi Bisnis',
            'inventory' => 'Pembelian Inventori',
            'equipment' => 'Pembelian Peralatan',
            'working_capital' => 'Modal Kerja',
            'other' => 'Lainnya',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-500/15 text-yellow-400',
            'under_review' => 'bg-blue-500/15 text-blue-400',
            'approved' => 'bg-green-500/15 text-green-400',
            'rejected' => 'bg-red-500/15 text-red-400',
            'disbursed' => 'bg-purple-500/15 text-purple-400',
            'active' => 'bg-emerald-500/15 text-emerald-400',
            'completed' => 'bg-gray-500/15 text-gray-400',
            'defaulted' => 'bg-red-600/15 text-red-300',
            default => 'bg-neutral-500/15 text-neutral-400'
        };
    }

    /**
     * Get status label in Indonesian
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
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
     * Get disbursement method label
     */
    public function getDisbursementMethodLabelAttribute(): string
    {
        return match($this->disbursement_method) {
            'saldo' => 'Transfer ke Saldo',
            'bank_transfer' => 'Transfer ke Rekening Bank',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Get formatted bank account info
     */
    public function getBankAccountInfoAttribute(): string
    {
        if ($this->disbursement_method === 'bank_transfer') {
            return $this->bank_name . ' - ' . $this->bank_account_number . ' (' . $this->bank_account_name . ')';
        }
        return 'Transfer ke Saldo User';
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for user's loan requests
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
