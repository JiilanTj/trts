<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Events\NewChatMessage;
use Illuminate\Support\Facades\Storage;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_room_id',
        'user_id',
        'message',
        'message_type',
        'attachment_path',
        'is_read',
        'read_at',
        'is_from_guest',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'is_from_guest' => 'boolean',
    ];

    const TYPE_TEXT = 'text';
    const TYPE_IMAGE = 'image';
    const TYPE_FILE = 'file';
    const TYPE_SYSTEM = 'system';

    protected static function booted()
    {
        static::created(function ($message) {
            // Update chat room last message timestamp
            $message->chatRoom->updateLastMessage();
            
            // Broadcast new message event
            broadcast(new NewChatMessage($message));
        });
    }

    /**
     * Get the chat room this message belongs to
     */
    public function chatRoom(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class);
    }

    /**
     * Get the user who sent this message
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if message is from admin
     */
    public function isFromAdmin(): bool
    {
        return $this->sender && $this->sender->isAdmin();
    }

    /**
     * Check if message is from customer
     */
    public function isFromCustomer(): bool
    {
        return $this->sender && $this->sender->isUser();
    }

    /**
     * Mark message as read
     */
    public function markAsRead(): bool
    {
        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Check if message is text type
     */
    public function isText(): bool
    {
        return $this->message_type === self::TYPE_TEXT;
    }

    /**
     * Check if message has attachment
     */
    public function hasAttachment(): bool
    {
        return !empty($this->attachment_path);
    }

    /**
     * Get attachment URL
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment_path ? Storage::url($this->attachment_path) : null;
    }

    /**
     * Scope for unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for messages in chat room
     */
    public function scopeInRoom($query, $roomId)
    {
        return $query->where('chat_room_id', $roomId);
    }

    /**
     * Scope for messages from user
     */
    public function scopeFromUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if this message is from a guest
     */
    public function isFromGuest(): bool
    {
        return $this->is_from_guest === true;
    }

    /**
     * Check if this message is from a user
     */
    public function isFromUser(): bool
    {
        return !$this->is_from_guest && $this->user_id !== null;
    }

    /**
     * Get sender name (guest, user, or admin)
     */
    public function getSenderName(): string
    {
        if ($this->isFromGuest()) {
            return $this->chatRoom->guest_name ?? 'Guest';
        }

        return $this->sender?->full_name ?? 'Unknown';
    }

    /**
     * Scope for messages from guests
     */
    public function scopeFromGuests($query)
    {
        return $query->where('is_from_guest', true);
    }

    /**
     * Scope for messages from authenticated users (not guests)
     */
    public function scopeFromAuthenticatedUsers($query)
    {
        return $query->where('is_from_guest', false)->whereNotNull('user_id');
    }

    /**
     * Create a guest message
     */
    public static function createGuestMessage(int $chatRoomId, string $message, string $messageType = self::TYPE_TEXT): self
    {
        return self::create([
            'chat_room_id' => $chatRoomId,
            'user_id' => null,
            'message' => $message,
            'message_type' => $messageType,
            'is_from_guest' => true,
        ]);
    }
}
