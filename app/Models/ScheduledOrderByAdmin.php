<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledOrderByAdmin extends Model
{
    use HasFactory;

    // Explicitly set table name (migration uses 'scheduled_order_by_admin')
    protected $table = 'scheduled_order_by_admin';

    protected $fillable = [
        'created_by',
        'user_id',
        'store_showcase_id',
        'product_id',
        'quantity',
        'schedule_at',
        'timezone',
        'status',
        'started_at',
        'finished_at',
        'created_order_id',
        'error_message',
    ];

    protected $casts = [
        'schedule_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
