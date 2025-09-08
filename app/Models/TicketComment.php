<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketComment extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'comment',
        'attachments',
        'is_internal'
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_internal' => 'boolean'
    ];

    /**
     * Get the ticket that owns the comment
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user who made the comment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if comment is from admin
     */
    public function isFromAdmin(): bool
    {
        return $this->user->role === 'admin';
    }
}
