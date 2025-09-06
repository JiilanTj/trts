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
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    const ROLE_CUSTOMER = 'customer';
    const ROLE_AGENT = 'agent';

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
}
