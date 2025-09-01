<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id','product_id','quantity',
        'unit_price','base_price','sell_price','discount','seller_margin','line_total'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'base_price' => 'integer',
        'sell_price' => 'integer',
        'discount' => 'integer',
        'seller_margin' => 'integer',
        'line_total' => 'integer',
    ];

    // Relationships
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }

    // Helpers
    public function marginTotal(): int { return ($this->seller_margin ?? 0) * ($this->quantity ?? 0); }
    public function discountTotal(): int { return ($this->discount ?? 0) * ($this->quantity ?? 0); }

    public function formattedUnitPrice(): string { return $this->formatCurrency($this->unit_price); }
    public function formattedLineTotal(): string { return $this->formatCurrency($this->line_total); }
    protected function formatCurrency($v): string { return 'Rp ' . number_format((int)$v, 0, ',', '.'); }

    public function computeLineTotal(): int
    {
        $unit = max(0, (int)$this->unit_price);
        $disc = max(0, min((int)$this->discount, $unit)); // prevent over discount
        $qty = max(0, (int)$this->quantity);
        return ($unit - $disc) * $qty;
    }

    protected static function boot()
    {
        parent::boot();
        static::saving(function (self $item) {
            // Ensure sane defaults
            $item->quantity = $item->quantity ?: 0;
            foreach (['unit_price','base_price','sell_price','discount','seller_margin'] as $f) {
                $item->{$f} = $item->{$f} ?: 0;
            }
            $item->line_total = $item->computeLineTotal();
        });
    }
}
