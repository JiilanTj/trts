<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Events\ChatRoomAssigned;
use App\Events\UserTyping;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * Show admin chat dashboard
     */
    public function index(): View
    {
        $admin = Auth::user();
        
        // Get filters from request
        $status = request('status');
        $priority = request('priority');
        
        // Build query with filters
        $query = ChatRoom::with(['user', 'assignedAdmin', 'messages' => function($q) {
            $q->latest()->limit(1);
        }])->withCount('messages');
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($priority) {
            $query->where('priority', $priority);
        }
        
        $chatRooms = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate statistics
        $statistics = [
            'total_rooms' => ChatRoom::count(),
            'open_rooms' => ChatRoom::where('status', 'open')->count(),
            'assigned_rooms' => ChatRoom::where('status', 'assigned')->count(),
            'avg_response_time' => '2m 30s', // This would be calculated from actual data
        ];

        return view('admin.chat.index', compact('chatRooms', 'statistics'));
    }

    /**
     * Show specific chat room for admin
     */
    public function show(ChatRoom $chatRoom): View
    {
        $admin = Auth::user();
        
        // Load messages with sender info
        $messages = $chatRoom->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // Auto-assign chat to admin if it's open
        if ($chatRoom->isOpen()) {
            $chatRoom->assignTo($admin);
            broadcast(new ChatRoomAssigned($chatRoom));
        }

        // Mark messages as read (messages from customer to admin)
        ChatMessage::where('chat_room_id', $chatRoom->id)
            ->where('user_id', $chatRoom->user_id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('admin.chat.show', compact('chatRoom', 'messages'));
    }

    /**
     * Assign chat room to admin
     */
    public function assign(Request $request, ChatRoom $chatRoom): JsonResponse
    {
        $admin = Auth::user();

        if (!$chatRoom->isOpen()) {
            return response()->json(['error' => 'Chat room is not available for assignment'], 400);
        }

        $success = $chatRoom->assignTo($admin);

        if ($success) {
            broadcast(new ChatRoomAssigned($chatRoom));

            return response()->json([
                'success' => true,
                'message' => 'Chat berhasil diambil!',
                'chat_room' => $chatRoom->load(['customer', 'admin']),
            ]);
        }

        return response()->json(['error' => 'Failed to assign chat room'], 500);
    }

    /**
     * Close chat room
     */
    public function close(Request $request, ChatRoom $chatRoom): JsonResponse
    {
        $request->validate([
            'reason' => 'sometimes|string|max:500',
        ]);

        $admin = Auth::user();

        // Check if admin can close this chat
        if ($chatRoom->admin_id !== $admin->id && !$admin->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $success = $chatRoom->close();

        if ($success) {
            // Send system message about chat closure
            $chatRoom->messages()->create([
                'user_id' => $admin->id,
                'message' => 'Chat ditutup oleh admin' . ($request->reason ? ': ' . $request->reason : '.'),
                'message_type' => ChatMessage::TYPE_SYSTEM,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Chat berhasil ditutup!',
            ]);
        }

        return response()->json(['error' => 'Failed to close chat room'], 500);
    }

    /**
     * Send message to chat room
     */
    public function sendMessage(Request $request, ChatRoom $chatRoom): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'attachment' => 'sometimes|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        $admin = Auth::user();

        // Check if admin can send message to this chat
        if ($chatRoom->admin_id !== $admin->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if chat room is not closed
        if ($chatRoom->isClosed()) {
            return response()->json(['error' => 'Chat room is closed'], 400);
        }

        // Handle file attachment
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('chat-attachments', 'public');
        }

        // Create message
        $message = $chatRoom->messages()->create([
            'user_id' => $admin->id,
            'message' => $request->message,
            'message_type' => $attachmentPath ? ChatMessage::TYPE_FILE : ChatMessage::TYPE_TEXT,
            'attachment_path' => $attachmentPath,
        ]);

        // Load relationship data for broadcasting
        $message->load('user');

        // Broadcast the message to all participants
        broadcast(new \App\Events\NewChatMessage($message));

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'user_id' => $message->user_id,
                'user_name' => $message->user->name,
                'created_at' => $message->created_at->toISOString(),
                'time' => $message->created_at->format('H:i'),
            ],
        ]);
    }

    /**
     * Handle typing indicator
     */
    public function typing(Request $request): JsonResponse
    {
        $request->validate([
            'chat_room_id' => 'required|exists:chat_rooms,id',
            'is_typing' => 'required|boolean',
        ]);

        $admin = Auth::user();
        $chatRoom = ChatRoom::find($request->chat_room_id);

        // Check if admin is assigned to this chat
        if ($chatRoom->admin_id !== $admin->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Broadcast typing event
        broadcast(new UserTyping($admin, $request->chat_room_id, $request->is_typing));

        return response()->json(['success' => true]);
    }

    /**
     * Get chat statistics for API
     */
    public function statisticsApi(): JsonResponse
    {
        $admin = Auth::user();

        $stats = [
            'open_chats' => ChatRoom::where('status', 'open')->count(),
            'assigned_chats' => ChatRoom::where('status', 'assigned')->count(),
            'my_chats' => ChatRoom::where('assigned_admin_id', $admin->id)->count(),
            'closed_today' => ChatRoom::where('status', 'closed')->whereDate('updated_at', today())->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Show chat statistics page
     */
    public function statistics(): View
    {
        // Dummy data for now - in production this would come from actual database queries
        $statistics = [
            'total_conversations' => ChatRoom::count(),
            'avg_response_time' => '2m 30s',
            'resolution_rate' => '89%',
            'satisfaction_score' => '4.2',
            'chat_volume_labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'chat_volume_data' => [12, 19, 8, 15, 22, 13, 17],
            'response_time_labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'response_time_data' => [2.5, 1.8, 3.2, 2.1, 1.9, 2.8, 2.3],
            'admin_performance' => [
                ['name' => 'Admin 1', 'chat_count' => 45, 'avg_response_time' => '2m 15s', 'rating' => 4.5],
                ['name' => 'Admin 2', 'chat_count' => 38, 'avg_response_time' => '2m 45s', 'rating' => 4.2],
                ['name' => 'Admin 3', 'chat_count' => 52, 'avg_response_time' => '1m 55s', 'rating' => 4.8],
            ],
            'recent_activity' => [
                ['description' => 'New chat room opened by John Doe', 'time' => '5 minutes ago'],
                ['description' => 'Chat room #123 closed successfully', 'time' => '12 minutes ago'],
                ['description' => 'Admin assigned to chat room #124', 'time' => '18 minutes ago'],
                ['description' => 'Customer satisfied with resolution', 'time' => '25 minutes ago'],
                ['description' => 'High priority chat escalated', 'time' => '32 minutes ago'],
            ],
        ];

        return view('admin.chat.statistics', compact('statistics'));
    }

    /**
     * Get dashboard updates for polling (production)
     */
    public function dashboardUpdates(): JsonResponse
    {
        $admin = Auth::user();
        
        // Get recent chat rooms with updates
        $chatRooms = ChatRoom::with(['user', 'admin'])
            ->withCount('messages')
            ->where('updated_at', '>', now()->subMinutes(5))
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();

        // Get statistics
        $statistics = [
            'total_rooms' => ChatRoom::count(),
            'open_rooms' => ChatRoom::where('status', 'open')->count(),
            'assigned_rooms' => ChatRoom::where('status', 'assigned')->count(),
        ];

        return response()->json([
            'chat_rooms' => $chatRooms,
            'statistics' => $statistics,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Get new messages for polling (production)
     */
    public function getNewMessages(Request $request, ChatRoom $chatRoom): JsonResponse
    {
        $afterId = $request->get('after', 0);
        
        $messages = $chatRoom->messages()
            ->with('user')
            ->where('id', '>', $afterId)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'user_id' => $message->user_id,
                    'user_name' => $message->user->name ?? 'Unknown',
                    'created_at' => $message->created_at->toISOString(),
                    'message_type' => $message->message_type ?? 'text',
                ];
            });

        return response()->json([
            'messages' => $messages,
            'last_id' => $messages->max('id') ?? $afterId,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Send message via API (production)
     */
    public function sendMessageApi(Request $request, ChatRoom $chatRoom): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $admin = Auth::user();

        // Check if chat room is not closed
        if ($chatRoom->isClosed()) {
            return response()->json(['error' => 'Chat room is closed'], 400);
        }

        // Auto-assign if not assigned
        if ($chatRoom->status === 'open') {
            $chatRoom->assignTo($admin);
        }

        // Create message
        $message = $chatRoom->messages()->create([
            'user_id' => $admin->id,
            'message' => $request->message,
            'message_type' => 'text',
        ]);

        // Update chat room timestamp
        $chatRoom->touch();

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'user_id' => $message->user_id,
                'user_name' => $admin->name,
                'created_at' => $message->created_at->toISOString(),
            ]
        ]);
    }

    /**
     * Get typing status (for polling)
     */
    public function getTypingStatus(Request $request, ChatRoom $chatRoom): JsonResponse
    {
        // In a real implementation, you'd store typing status in cache/database
        // For now, return empty array
        return response()->json([
            'typing_users' => [],
            'timestamp' => now()->toISOString()
        ]);
    }
}
