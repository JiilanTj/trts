<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'subject',
        'status',
        'priority',
        'last_message_at',
        'closed_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    const STATUS_OPEN = 'open';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_CLOSED = 'closed';

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';

    /**
     * Get the customer who started this chat
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the admin assigned to this chat
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Alias for admin relationship for consistency
     */
    public function assignedAdmin(): BelongsTo
    {
        return $this->admin();
    }

    /**
     * Get all messages in this chat room
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the latest message
     */
    public function latestMessage(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->latest();
    }

    /**
     * Get all participants
     */
    public function participants(): HasMany
    {
        return $this->hasMany(ChatRoomParticipant::class);
    }

    /**
     * Get users participating in this chat
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_room_participants')->withPivot('role', 'joined_at', 'left_at');
    }

    /**
     * Check if chat room is open
     */
    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    /**
     * Check if chat room is assigned
     */
    public function isAssigned(): bool
    {
        return $this->status === self::STATUS_ASSIGNED;
    }

    /**
     * Check if chat room is closed
     */
    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    /**
     * Assign chat to admin
     */
    public function assignTo(User $admin): bool
    {
        if (!$admin->isAdmin()) {
            return false;
        }

        $this->update([
            'admin_id' => $admin->id,
            'status' => self::STATUS_ASSIGNED,
        ]);

        // Add admin as participant
        $this->participants()->updateOrCreate(
            ['user_id' => $admin->id],
            ['role' => 'agent', 'joined_at' => now()]
        );

        return true;
    }

    /**
     * Close the chat room
     */
    public function close(): bool
    {
        return $this->update([
            'status' => self::STATUS_CLOSED,
            'closed_at' => now(),
        ]);
    }

    /**
     * Update last message timestamp
     */
    public function updateLastMessage(): bool
    {
        return $this->update(['last_message_at' => now()]);
    }

    /**
     * Scope for open chats
     */
    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    /**
     * Scope for assigned chats
     */
    public function scopeAssigned($query)
    {
        return $query->where('status', self::STATUS_ASSIGNED);
    }

    /**
     * Scope for closed chats
     */
    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    /**
     * Scope for high priority
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', self::PRIORITY_HIGH);
    }

    /**
     * Scope for admin's assigned chats
     */
    public function scopeAssignedToAdmin($query, $adminId)
    {
        return $query->where('admin_id', $adminId);
    }
}
