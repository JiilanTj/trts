<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'category_id',
        'stock',
        'purchase_price',
        'promo_price',
        'sell_price',
        'profit',
        'image',
        'description',
        'weight',
        'expiry_date',
        'status',
    ];

    protected $casts = [
        'purchase_price' => 'integer',
        'promo_price' => 'integer',
        'sell_price' => 'integer',
        'profit' => 'integer',
        'stock' => 'integer',
        'expiry_date' => 'date',
        'status' => 'string',
    ];

    /**
     * Get the category that owns the product
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope for active products
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if product is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if product is in stock
     */
    public function inStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Get formatted purchase price
     */
    public function getFormattedPurchasePriceAttribute(): string
    {
        return 'Rp ' . number_format($this->purchase_price, 0, ',', '.');
    }

    /**
     * Get formatted sell price
     */
    public function getFormattedSellPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->sell_price, 0, ',', '.');
    }

    /**
     * Get formatted promo price
     */
    public function getFormattedPromoPriceAttribute(): string
    {
        return $this->promo_price ? 'Rp ' . number_format($this->promo_price, 0, ',', '.') : '-';
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    /**
     * Calculate and set profit automatically
     */
    public function calculateProfit()
    {
        $this->profit = $this->sell_price - $this->purchase_price;
        return $this->profit;
    }

    /**
     * Boot method to auto-calculate profit
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($product) {
            $product->profit = $product->sell_price - $product->purchase_price;
        });
    }
}
