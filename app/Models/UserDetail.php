<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDetail extends Model
{
    protected $fillable = [
        'user_id','phone','secondary_phone','gender','birth_date','birth_place','address_line','rt_rw','village','district','city','province','postal_code','nationality','marital_status','religion','occupation','extra'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'extra' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
