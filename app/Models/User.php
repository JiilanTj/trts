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
        'total_transaction_amount',
        'last_level_check',
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
            'total_transaction_amount' => 'integer',
            'last_level_check' => 'datetime',
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

    /**
     * Get level requirements configuration
     *
     * @return array
     */
    public static function getLevelRequirements(): array
    {
        return [
            1 => ['transaction_amount' => 0, 'margin_percent' => null, 'badge' => 'Bintang 1'],
            2 => ['transaction_amount' => 100_000_000, 'margin_percent' => 13, 'badge' => 'Bintang 2'],
            3 => ['transaction_amount' => 250_000_000, 'margin_percent' => 15, 'badge' => 'Bintang 3'],
            4 => ['transaction_amount' => 500_000_000, 'margin_percent' => 18, 'badge' => 'Bintang 4'],
            5 => ['transaction_amount' => 1_000_000_000, 'margin_percent' => 22, 'badge' => 'Bintang 5'],
            6 => ['transaction_amount' => 2_000_000_000, 'margin_percent' => 25, 'badge' => 'Toko dari Mulut ke Mulut'],
        ];
    }

    /**
     * Get current level data with safe fallback
     *
     * @return array
     */
    public function getCurrentLevelData(): array
    {
        $levelRequirements = static::getLevelRequirements();
        $currentLevel = $this->level;
        
        // Handle levels outside our defined range (like admin level 10)
        if (!isset($levelRequirements[$currentLevel])) {
            return [
                'badge' => $currentLevel == 10 ? 'Admin' : "Level {$currentLevel}",
                'margin_percent' => 30, // Admin gets highest margin
                'transaction_amount' => 0
            ];
        }
        
        return $levelRequirements[$currentLevel];
    }

    /**
     * Get current level margin percentage
     *
     * @return int|null
     */
    public function getLevelMarginPercent(): ?int
    {
        $requirements = static::getLevelRequirements();
        return $requirements[$this->level]['margin_percent'] ?? null;
    }

    /**
     * Get current level badge name
     *
     * @return string
     */
    public function getLevelBadge(): string
    {
        $requirements = static::getLevelRequirements();
        return $requirements[$this->level]['badge'] ?? 'Default';
    }

    /**
     * Check if user can upgrade to a specific level
     *
     * @param int $targetLevel
     * @return bool
     */
    public function canUpgradeToLevel(int $targetLevel): bool
    {
        $requirements = static::getLevelRequirements();
        if (!isset($requirements[$targetLevel])) {
            return false;
        }

        $required = $requirements[$targetLevel]['transaction_amount'];
        return $this->total_transaction_amount >= $required;
    }

    /**
     * Check and upgrade user level automatically
     *
     * @return bool True if level was upgraded, false otherwise
     */
    public function checkAndUpgradeLevel(): bool
    {
        $requirements = static::getLevelRequirements();
        $currentLevel = $this->level;
        $newLevel = $currentLevel;

        // Find highest level user qualifies for
        foreach ($requirements as $level => $requirement) {
            if ($level <= $currentLevel) continue; // Skip current and lower levels
            
            if ($this->total_transaction_amount >= $requirement['transaction_amount']) {
                $newLevel = $level;
            } else {
                break; // Stop at first unmet requirement
            }
        }

        if ($newLevel > $currentLevel) {
            $this->update([
                'level' => $newLevel,
                'last_level_check' => now(),
            ]);

            // Send notification to user
            \App\Models\Notification::create([
                'for_user_id' => $this->id,
                'category' => 'level',
                'title' => 'Selamat! Bintang Naik',
                'description' => $newLevel == 6 
                    ? "Selamat! Anda telah mencapai status tertinggi: {$requirements[$newLevel]['badge']}! Komisi margin sekarang {$requirements[$newLevel]['margin_percent']}%!"
                    : "Selamat! Bintang Anda naik dari {$requirements[$currentLevel]['badge']} (Bintang {$currentLevel}) menjadi {$requirements[$newLevel]['badge']} (Bintang {$newLevel}). Komisi margin sekarang {$requirements[$newLevel]['margin_percent']}%!",
            ]);

            return true;
        }

        // Update last check time even if no upgrade
        $this->update(['last_level_check' => now()]);
        return false;
    }

    /**
     * Add transaction amount and check for level upgrade
     *
     * @param int $amount
     * @return void
     */
    public function addTransactionAmount(int $amount): void
    {
        $this->increment('total_transaction_amount', $amount);
        $this->checkAndUpgradeLevel();
    }

    /**
     * Get progress to next level as percentage
     *
     * @return float
     */
    public function getNextLevelProgress(): float
    {
        $requirements = static::getLevelRequirements();
        $nextLevel = $this->level + 1;

        if (!isset($requirements[$nextLevel])) {
            return 100.0; // Already at max level
        }

        $currentRequired = $requirements[$this->level]['transaction_amount'];
        $nextRequired = $requirements[$nextLevel]['transaction_amount'];
        $current = $this->total_transaction_amount;

        if ($nextRequired <= $currentRequired) {
            return 100.0;
        }

        $progress = (($current - $currentRequired) / ($nextRequired - $currentRequired)) * 100;
        return min(100.0, max(0.0, $progress));
    }

    /**
     * Get amount needed for next level
     *
     * @return int|null
     */
    public function getAmountNeededForNextLevel(): ?int
    {
        $requirements = static::getLevelRequirements();
        $nextLevel = $this->level + 1;

        if (!isset($requirements[$nextLevel])) {
            return null; // Already at max level
        }

        $needed = $requirements[$nextLevel]['transaction_amount'] - $this->total_transaction_amount;
        return max(0, $needed);
    }

    /**
     * Store showcases owned by this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function storeShowcases(): HasMany
    {
        return $this->hasMany(StoreShowcase::class);
    }

    /**
     * Active store showcases
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activeShowcases(): HasMany
    {
        return $this->hasMany(StoreShowcase::class)->active()->ordered();
    }

    /**
     * Featured store showcases
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function featuredShowcases(): HasMany
    {
        return $this->hasMany(StoreShowcase::class)->featured()->ordered();
    }
}
