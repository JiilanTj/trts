<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class InvitationCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'is_active',
        'max_usage',
        'used_count',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'max_usage' => 'integer',
        'used_count' => 'integer',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the invitation code.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the invitation code is valid and can be used.
     */
    public function isValid(): bool
    {
        return $this->is_active 
            && $this->used_count < $this->max_usage
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Use the invitation code (increment used_count).
     */
    public function use(): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $this->increment('used_count');
        
        // Deactivate if max usage reached
        if ($this->used_count >= $this->max_usage) {
            $this->update(['is_active' => false]);
        }

        return true;
    }

    /**
     * Generate a new unique invitation code.
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Scope to only active codes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to non-expired codes.
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope to available codes (active, not expired, not max used).
     */
    public function scopeAvailable($query)
    {
        return $query->active()
                    ->notExpired()
                    ->whereRaw('used_count < max_usage');
    }
}
