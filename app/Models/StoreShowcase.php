<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreShowcase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'sort_order',
        'is_featured',
        'is_active',
        'featured_until',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'featured_until' => 'datetime',
    ];

    /**
     * Get the user that owns the showcase
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product being showcased
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if the showcase is currently featured
     */
    public function getIsFeaturedActiveAttribute()
    {
        if (!$this->is_featured) {
            return false;
        }

        if ($this->featured_until && $this->featured_until->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Get the seller price (harga_jual) from the product
     */
    public function getPriceAttribute()
    {
        return $this->product ? $this->product->harga_jual : 0;
    }

    /**
     * Get the original price (harga_biasa) from the product
     */
    public function getOriginalPriceAttribute()
    {
        return $this->product ? $this->product->harga_biasa : 0;
    }

    /**
     * Get description from product or custom description
     */
    public function getDescriptionAttribute()
    {
        return $this->product ? $this->product->description : null;
    }

    /**
     * Scope for active showcases
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured showcases
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
                    ->where(function($q) {
                        $q->whereNull('featured_until')
                          ->orWhere('featured_until', '>', now());
                    });
    }

    /**
     * Scope for specific user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for ordering by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }

    /**
     * Scope to include product data
     */
    public function scopeWithProduct($query)
    {
        return $query->with('product');
    }
}
