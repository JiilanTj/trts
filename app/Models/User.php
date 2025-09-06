<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'username',
        'photo',
        'password',
        'balance',
        'level',
        'credit_score',
        'role',
        'is_seller',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'balance' => 'integer',
            'level' => 'integer',
            'credit_score' => 'integer',
            'is_seller' => 'boolean',
        ];
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id'; // Keep using 'id' for Laravel's auth system
    }

    /**
     * Check if user is admin
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is regular user
     *
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if user is a seller
     *
     * @return bool
     */
    public function isSeller(): bool
    {
        return $this->is_seller === true;
    }

    /**
     * Get the photo URL
     *
     * @return string|null
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/profiles/' . $this->photo) : null;
    }

    /**
     * Get avatar (photo or default)
     *
     * @return string
     */
    public function getAvatarAttribute(): string
    {
        return $this->photo_url ?? asset('images/default-avatar.png');
    }

    /**
     * Hash password if set directly (safety net in addition to cast)
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value): void
    {
        if ($value && !Hash::needsRehash($value)) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    /**
     * Scope a query to only include admins.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope a query to only include users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsers($query)
    {
        return $query->where('role', 'user');
    }

    /**
     * Scope a query to only include sellers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSellers($query)
    {
        return $query->where('is_seller', true);
    }

    /**
     * Products that belong to the seller.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Orders that belong to the seller.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Invitation codes created by this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invitationCodes()
    {
        return $this->hasMany(InvitationCode::class);
    }

    /**
     * Seller requests made by this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sellerRequests()
    {
        return $this->hasMany(SellerRequest::class);
    }

    /**
     * Seller info for this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sellerInfo()
    {
        return $this->hasOne(SellerInfo::class);
    }

    /**
     * User detail for this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function detail()
    {
        return $this->hasOne(UserDetail::class);
    }

    /**
     * KYC requests made by this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kycRequests()
    {
        return $this->hasMany(KycRequest::class);
    }

    /**
     * KYC information for this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function kyc()
    {
        return $this->hasOne(Kyc::class);
    }

    /**
     * Notifications for this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'for_user_id');
    }

    /**
     * Topup requests made by this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topupRequests()
    {
        return $this->hasMany(TopupRequest::class);
    }

    /**
     * Add amount to user balance
     *
     * @param int $amount
     * @return void
     */
    public function addBalance(int $amount): void
    {
        $this->increment('balance', $amount);
    }

    /**
     * Add credit score to user
     *
     * @param int $points
     * @return void
     */
    public function addCreditScore(int $points): void
    {
        $this->increment('credit_score', $points);
    }
}
