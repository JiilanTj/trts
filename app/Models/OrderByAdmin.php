<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderByAdmin extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'order_by_admin';

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_CONFIRMED = 'CONFIRMED';
    public const STATUS_PACKED = 'PACKED';
    public const STATUS_SHIPPED = 'SHIPPED';
    public const STATUS_DELIVERED = 'DELIVERED';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'admin_id',
        'user_id',
        'store_showcase_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'total_price' => 'integer',
        'status' => 'string',
    ];

    /**
     * Relations
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function storeShowcase(): BelongsTo
    {
        return $this->belongsTo(StoreShowcase::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
