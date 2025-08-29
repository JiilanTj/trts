<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SellerInfo extends Model
{
    use HasFactory;

    protected $table = 'seller_info';

    protected $fillable = [
        'user_id',
        'store_name',
        'visitors',
        'followers',
        'credit_score',
        'description',
        'status',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    protected $casts = [
        'visitors' => 'integer',
        'followers' => 'integer',
        'credit_score' => 'integer',
        'status' => 'string',
    ];

    /**
     * Get the user that owns the seller info.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all products for this seller.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'user_id', 'user_id');
    }

    /**
     * Check if the seller is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if the seller is inactive.
     */
    public function isInactive(): bool
    {
        return $this->status === self::STATUS_INACTIVE;
    }

    /**
     * Activate the seller.
     */
    public function activate(): bool
    {
        return $this->update(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Deactivate the seller.
     */
    public function deactivate(): bool
    {
        return $this->update(['status' => self::STATUS_INACTIVE]);
    }

    /**
     * Increment visitors count.
     */
    public function incrementVisitors(int $count = 1): bool
    {
        return $this->increment('visitors', $count);
    }

    /**
     * Increment followers count.
     */
    public function incrementFollowers(int $count = 1): bool
    {
        return $this->increment('followers', $count);
    }

    /**
     * Decrement followers count.
     */
    public function decrementFollowers(int $count = 1): bool
    {
        return $this->decrement('followers', max(0, $count));
    }

    /**
     * Update credit score.
     */
    public function updateCreditScore(int $score): bool
    {
        return $this->update(['credit_score' => max(0, $score)]);
    }

    /**
     * Scope to only active sellers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope to only inactive sellers.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    /**
     * Scope to order by credit score.
     */
    public function scopeOrderByCreditScore($query, $direction = 'desc')
    {
        return $query->orderBy('credit_score', $direction);
    }

    /**
     * Scope to order by followers.
     */
    public function scopeOrderByFollowers($query, $direction = 'desc')
    {
        return $query->orderBy('followers', $direction);
    }
}
