<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'for_user_id',
        'category',
        'title', 
        'description',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'for_user_id');
    }

    /**
     * Get notifications for specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('for_user_id', $userId);
    }

    /**
     * Get notifications by category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get recent notifications
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->latest()->limit($limit);
    }

    /**
     * Get unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Get read notifications
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Check if notification is read
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        if (!$this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Available notification categories
     */
    public static function categories()
    {
        return [
            'order' => 'Order',
            'payment' => 'Payment',
            'system' => 'System',
            'promotion' => 'Promotion',
        ];
    }
}
