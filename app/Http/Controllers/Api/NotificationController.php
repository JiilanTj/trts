<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get unread notification count for current user
     */
    public function getUnreadCount()
    {
        $unreadCount = Notification::forUser(Auth::id())
            ->unread()
            ->count();

        return response()->json([
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark all notifications as read for current user
     */
    public function markAllAsRead()
    {
        $updatedCount = Notification::forUser(Auth::id())
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json([
            'marked_as_read' => $updatedCount
        ]);
    }
}
