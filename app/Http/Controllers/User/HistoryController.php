<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        // Get real notifications for current user
        $notifications = auth()->user()->notifications()
            ->latest()
            ->limit(50)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'category' => $notification->category,
                    'title' => $notification->title,
                    'message' => $notification->description,
                    'time' => $notification->created_at->diffForHumans(),
                    'created_at' => $notification->created_at,
                    'read' => $notification->isRead(),
                    'icon' => $this->getCategoryIcon($notification->category),
                    'color' => $this->getCategoryColor($notification->category)
                ];
            });
        
        return view('user.history.index', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification)
    {
        // Ensure user owns this notification
        if ($notification->for_user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        auth()->user()->notifications()->unread()->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Get icon based on notification category
     */
    private function getCategoryIcon(string $category): string
    {
        return match($category) {
            'order' => 'check-circle',
            'payment' => 'credit-card',
            'system' => 'cog',
            'promotion' => 'gift',
            default => 'bell'
        };
    }

    /**
     * Get color based on notification category
     */
    private function getCategoryColor(string $category): string
    {
        return match($category) {
            'order' => 'emerald',
            'payment' => 'blue',
            'system' => 'purple',
            'promotion' => 'pink',
            default => 'cyan'
        };
    }
}
