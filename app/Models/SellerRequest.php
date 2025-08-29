<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',
        'invite_code',
        'description',
        'status',
        'admin_note',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the user that made the seller request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the invitation code used for this request.
     */
    public function invitationCode(): BelongsTo
    {
        return $this->belongsTo(InvitationCode::class, 'invite_code', 'code');
    }

    /**
     * Check if the request is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the request is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the request is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Approve the seller request.
     */
    public function approve(string $adminNote = null): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        // Update request status
        $this->update([
            'status' => self::STATUS_APPROVED,
            'admin_note' => $adminNote,
        ]);

        // Update user to be a seller
        $this->user->update(['is_seller' => true]);

        // Create seller info
        SellerInfo::create([
            'user_id' => $this->user_id,
            'store_name' => $this->store_name,
            'description' => $this->description,
        ]);

        // Use the invitation code
        $invitationCode = InvitationCode::where('code', $this->invite_code)->first();
        if ($invitationCode) {
            $invitationCode->use();
        }

        return true;
    }

    /**
     * Reject the seller request.
     */
    public function reject(string $adminNote = null): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_REJECTED,
            'admin_note' => $adminNote,
        ]);

        return true;
    }

    /**
     * Scope to only pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to only approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope to only rejected requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }
}
