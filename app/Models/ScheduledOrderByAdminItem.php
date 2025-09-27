<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledOrderByAdminItem extends Model
{
    use HasFactory;

    protected $table = 'scheduled_order_by_admin_items';

    protected $fillable = [
        'scheduled_id',
        'store_showcase_id',
        'product_id',
        'quantity',
        'adress',
        'created_order_id',
        'error_message',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ScheduledOrderByAdmin::class, 'scheduled_id');
    }

    public function storeShowcase(): BelongsTo
    {
        return $this->belongsTo(StoreShowcase::class, 'store_showcase_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function createdOrder(): BelongsTo
    {
        return $this->belongsTo(OrderByAdmin::class, 'created_order_id');
    }
}
