<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'visitors',
        'followers',
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
            'visitors' => 'integer',
            'followers' => 'integer',
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

    /**
     * Increment visitor count
     *
     * @param int $count
     * @return void
     */
    public function incrementVisitors(int $count = 1): void
    {
        $this->increment('visitors', $count);
    }

    /**
     * Increment follower count
     *
     * @param int $count
     * @return void
     */
    public function incrementFollowers(int $count = 1): void
    {
        $this->increment('followers', $count);
    }

    /**
     * Decrement follower count
     *
     * @param int $count
     * @return void
     */
    public function decrementFollowers(int $count = 1): void
    {
        $this->decrement('followers', $count);
    }

    /**
     * Get formatted visitor count
     *
     * @return string
     */
    public function getFormattedVisitorsAttribute(): string
    {
        if ($this->visitors >= 1000000) {
            return round($this->visitors / 1000000, 1) . 'M';
        } elseif ($this->visitors >= 1000) {
            return round($this->visitors / 1000, 1) . 'K';
        }
        return (string) $this->visitors;
    }

    /**
     * Get formatted follower count
     *
     * @return string
     */
    public function getFormattedFollowersAttribute(): string
    {
        if ($this->followers >= 1000000) {
            return round($this->followers / 1000000, 1) . 'M';
        } elseif ($this->followers >= 1000) {
            return round($this->followers / 1000, 1) . 'K';
        }
        return (string) $this->followers;
    }

    /**
     * Get chat rooms where user is the customer
     */
    public function chatRooms(): HasMany
    {
        return $this->hasMany(ChatRoom::class, 'user_id');
    }

    /**
     * Get chat rooms assigned to admin
     */
    public function assignedChats(): HasMany
    {
        return $this->hasMany(ChatRoom::class, 'admin_id');
    }

    /**
     * Get chat messages sent by user
     */
    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'user_id');
    }

    /**
     * Get chat room participations
     */
    public function chatParticipations(): HasMany
    {
        return $this->hasMany(ChatRoomParticipant::class, 'user_id');
    }

    /**
     * Get withdrawal requests made by user
     */
    public function withdrawalRequests(): HasMany
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    /**
     * Get withdrawal requests processed by this user (admin)
     */
    public function processedWithdrawals(): HasMany
    {
        return $this->hasMany(WithdrawalRequest::class, 'processed_by');
    }

    /**
     * Deduct amount from user balance
     *
     * @param float $amount
     * @return bool
     */
    public function deductBalance(float $amount): bool
    {
        if ($this->balance >= $amount) {
            $this->decrement('balance', $amount);
            return true;
        }
        return false;
    }

    /**
     * Check if user has sufficient balance
     *
     * @param float $amount
     * @return bool
     */
    public function hasSufficientBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }
}
