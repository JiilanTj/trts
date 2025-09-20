<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduledOrderBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id', 'created_by', 'purchase_type', 'from_etalase', 'auto_paid',
        'external_customer_name', 'external_customer_phone', 'address', 'user_notes',
        'schedule_at', 'timezone', 'status', 'started_at', 'finished_at',
    ];

    protected $casts = [
        'from_etalase' => 'boolean',
        'auto_paid' => 'boolean',
        'schedule_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ScheduledOrderItem::class, 'batch_id');
    }
}
