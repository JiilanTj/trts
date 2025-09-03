<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kyc extends Model
{
    protected $fillable = [
        'user_id','kyc_request_id','full_name','nik','birth_place','birth_date','address','rt_rw','village','district','religion','marital_status','occupation','nationality','ktp_front_path','ktp_back_path','selfie_ktp_path','verified_at','verified_by','meta'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'verified_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function request(): BelongsTo { return $this->belongsTo(KycRequest::class,'kyc_request_id'); }
    public function verifier(): BelongsTo { return $this->belongsTo(User::class,'verified_by'); }
}
