<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Events\UserTyping;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * Show chat list for user
     */
    public function index(): View
    {
        $user = Auth::user();
        
        $chatRooms = ChatRoom::where('user_id', $user->id)
            ->with(['admin', 'latestMessage'])
            ->orderBy('last_message_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.chat.index', compact('chatRooms'));
    }

    /**
     * Show specific chat room
     */
    public function show(ChatRoom $chatRoom): View
    {
        $user = Auth::user();
        
        // Check if user owns this chat room
        if ($chatRoom->user_id !== $user->id) {
            abort(403, 'Unauthorized access to chat room.');
        }

        // Load messages with sender info
        $messages = $chatRoom->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read (messages from admin to user)
        ChatMessage::where('chat_room_id', $chatRoom->id)
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('user.chat.show', compact('chatRoom', 'messages'));
    }

    /**
     * Create new chat room
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'priority' => 'sometimes|in:low,medium,high',
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::user();

        // Create chat room
        $chatRoom = ChatRoom::create([
            'user_id' => $user->id,
            'subject' => $request->subject,
            'priority' => $request->priority ?? ChatRoom::PRIORITY_MEDIUM,
            'status' => ChatRoom::STATUS_OPEN,
        ]);

        // Add customer as participant
        $chatRoom->participants()->create([
            'user_id' => $user->id,
            'role' => 'customer',
            'joined_at' => now(),
        ]);

        // Create initial message
        $message = $chatRoom->messages()->create([
            'user_id' => $user->id,
            'message' => $request->message,
            'message_type' => ChatMessage::TYPE_TEXT,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Chat berhasil dimulai!',
            'chat_room' => [
                'id' => $chatRoom->id,
                'subject' => $chatRoom->subject,
                'status' => $chatRoom->status,
                'created_at' => $chatRoom->created_at->toISOString(),
            ],
        ]);
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

        $user = Auth::user();

        // Check if user owns this chat room
        if ($chatRoom->user_id !== $user->id) {
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
            'user_id' => $user->id,
            'message' => $request->message,
            'message_type' => $attachmentPath ? ChatMessage::TYPE_FILE : ChatMessage::TYPE_TEXT,
            'attachment_path' => $attachmentPath,
        ]);

        return response()->json([
            'success' => true,
            'message' => $message->load('sender'),
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

        $user = Auth::user();
        $chatRoom = ChatRoom::find($request->chat_room_id);

        // Check if user owns this chat room
        if ($chatRoom->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Broadcast typing event
        broadcast(new UserTyping($user, $request->chat_room_id, $request->is_typing));

        return response()->json(['success' => true]);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $request->validate([
            'chat_room_id' => 'required|exists:chat_rooms,id',
        ]);

        $user = Auth::user();
        $chatRoom = ChatRoom::find($request->chat_room_id);

        // Check if user owns this chat room
        if ($chatRoom->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mark messages as read (from admin to user)
        $updated = ChatMessage::where('chat_room_id', $request->chat_room_id)
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'success' => true,
            'updated_count' => $updated,
        ]);
    }
}
