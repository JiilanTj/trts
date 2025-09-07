<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatRoomParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_room_id',
        'user_id',
        'role',
        'joined_at',
        'left_at',
        'is_guest',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'is_guest' => 'boolean',
    ];

    const ROLE_CUSTOMER = 'customer';
    const ROLE_AGENT = 'agent';
    const ROLE_GUEST = 'guest';

    /**
     * Get the chat room
     */
    public function chatRoom(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class);
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if participant is customer
     */
    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    /**
     * Check if participant is agent
     */
    public function isAgent(): bool
    {
        return $this->role === self::ROLE_AGENT;
    }

    /**
     * Check if participant is guest
     */
    public function isGuest(): bool
    {
        return $this->role === self::ROLE_GUEST || $this->is_guest === true;
    }

    /**
     * Check if participant is authenticated user
     */
    public function isAuthenticatedUser(): bool
    {
        return !$this->is_guest && $this->user_id !== null;
    }

    /**
     * Mark participant as left
     */
    public function leave(): bool
    {
        return $this->update(['left_at' => now()]);
    }

    /**
     * Check if participant is active
     */
    public function isActive(): bool
    {
        return is_null($this->left_at);
    }

    /**
     * Scope for active participants
     */
    public function scopeActive($query)
    {
        return $query->whereNull('left_at');
    }

    /**
     * Scope for customers
     */
    public function scopeCustomers($query)
    {
        return $query->where('role', self::ROLE_CUSTOMER);
    }

    /**
     * Scope for agents
     */
    public function scopeAgents($query)
    {
        return $query->where('role', self::ROLE_AGENT);
    }

    /**
     * Scope for guests
     */
    public function scopeGuests($query)
    {
        return $query->where('role', self::ROLE_GUEST)->orWhere('is_guest', true);
    }

    /**
     * Scope for authenticated users (not guests)
     */
    public function scopeAuthenticatedUsers($query)
    {
        return $query->where('is_guest', false)->whereNotNull('user_id');
    }

    /**
     * Create a guest participant
     */
    public static function createGuestParticipant(int $chatRoomId): self
    {
        return self::create([
            'chat_room_id' => $chatRoomId,
            'user_id' => null,
            'role' => self::ROLE_GUEST,
            'is_guest' => true,
            'joined_at' => now(),
        ]);
    }
}
