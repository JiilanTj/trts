<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopupRequest extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'payment_proof',
        'bank_name',
        'bank_account',
        'transfer_date',
        'notes',
        'admin_notes',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transfer_date' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>',
            'approved' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>',
            'rejected' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>',
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->amount, 0, ',', '.');
    }
}
