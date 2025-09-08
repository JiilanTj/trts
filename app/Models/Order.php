<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id','purchase_type','external_customer_name','external_customer_phone',
        'address',
        'subtotal','discount_total','grand_total','seller_margin_total',
        'payment_method','payment_status','payment_proof_path','payment_confirmed_at','payment_confirmed_by',
        'payment_refunded_at','payment_refunded_by',
        'status','admin_notes','user_notes'
    ];

    protected $casts = [
        'payment_confirmed_at' => 'datetime',
        'payment_refunded_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function confirmer(): BelongsTo { return $this->belongsTo(User::class,'payment_confirmed_by'); }
    public function refunder(): BelongsTo { return $this->belongsTo(User::class,'payment_refunded_by'); }
    public function items(): HasMany { return $this->hasMany(OrderItem::class); }

    // Scopes / Helpers
    public function isPending(): bool { return $this->status === 'pending'; }
    public function isAwaitingConfirmation(): bool { return $this->status === 'awaiting_confirmation'; }
    public function isPackaging(): bool { return $this->status === 'packaging'; }
    public function isShipped(): bool { return $this->status === 'shipped'; }
    public function isDelivered(): bool { return $this->status === 'delivered'; }
    public function isCompleted(): bool { return $this->status === 'completed'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }

    public function canUploadProof(): bool { return in_array($this->payment_status, ['unpaid','rejected']); }
    public function canBeConfirmed(): bool { return $this->payment_status === 'waiting_confirmation'; }
    public function isBalancePayment(): bool { return $this->payment_method === 'balance'; }

    // Label helpers
    public static function statusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'awaiting_confirmation' => 'Menunggu Konfirmasi',
            'packaging' => 'Dikemas',
            'shipped' => 'Dikirim',
            'delivered' => 'Diterima',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
    }

    public static function paymentStatusOptions(): array
    {
        return [
            'unpaid' => 'Belum Bayar',
            'waiting_confirmation' => 'Menunggu Konfirmasi',
            'paid' => 'Dibayar',
            'rejected' => 'Ditolak',
            'refunded' => 'Refunded',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status] ?? ucfirst(str_replace('_',' ',$this->status));
    }

    public function paymentStatusLabel(): string
    {
        return self::paymentStatusOptions()[$this->payment_status] ?? ucfirst(str_replace('_',' ',$this->payment_status));
    }

    /**
     * Process seller margin payout when order is confirmed as paid
     *
     * @return void
     */
    public function processSellerMarginPayout(): void
    {
        // Only process for external/seller purchases that have margin
        if ($this->purchase_type === 'external' && $this->seller_margin_total > 0) {
            // Add margin to user balance
            $this->user->addBalance($this->seller_margin_total);
            
            // Add 5 points to credit score
            $this->user->addCreditScore(5);
        }
    }
}
