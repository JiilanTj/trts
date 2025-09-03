<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class KycRequest extends Model
{
    protected $fillable = [
        'user_id','full_name','nik','birth_place','birth_date','address','rt_rw','village','district','religion','marital_status','occupation','nationality','ktp_front_path','ktp_back_path','selfie_ktp_path','status_kyc','admin_notes','reviewed_at','reviewed_by'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_REVIEW = 'review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_REVIEW => 'Sedang Di Review',
            self::STATUS_APPROVED => 'Diterima',
            self::STATUS_REJECTED => 'Ditolak',
        ];
    }

    // Accessor untuk URL file (public disk)
    public function getKtpFrontUrlAttribute(): ?string
    {
        return $this->ktp_front_path ? Storage::url($this->ktp_front_path) : null;
    }
    public function getKtpBackUrlAttribute(): ?string
    {
        return $this->ktp_back_path ? Storage::url($this->ktp_back_path) : null;
    }
    public function getSelfieKtpUrlAttribute(): ?string
    {
        return $this->selfie_ktp_path ? Storage::url($this->selfie_ktp_path) : null;
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class,'reviewed_by'); }
    public function kyc(): HasOne { return $this->hasOne(Kyc::class); }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status_kyc] ?? ucfirst($this->status_kyc);
    }
}
