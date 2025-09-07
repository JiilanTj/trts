<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\ChatRoomParticipant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Show the guest chat room
     */
    public function index()
    {
        return view('guest.chat');
    }

    /**
     * Start a new guest chat session
     */
    public function startChat(Request $request): JsonResponse
    {
        $request->validate([
            'guest_name' => 'required|string|max:100',
            'guest_email' => 'nullable|email|max:150',
            'initial_message' => 'required|string|max:1000',
        ]);

        // Generate unique session ID for guest
        $sessionId = 'guest_' . Str::random(32);
        
        // Store session ID in session for tracking
        session(['guest_chat_session' => $sessionId]);

        // Create guest chat room
        $chatRoom = ChatRoom::createGuestChat(
            $sessionId,
            $request->guest_name,
            $request->guest_email
        );

        // Add guest as participant
        ChatRoomParticipant::createGuestParticipant($chatRoom->id);

        // Send initial message
        $message = ChatMessage::createGuestMessage(
            $chatRoom->id,
            $request->initial_message
        );

        return response()->json([
            'success' => true,
            'message' => 'Chat dimulai! Admin akan segera membantu Anda.',
            'data' => [
                'chat_room_id' => $chatRoom->id,
                'session_id' => $sessionId,
                'guest_name' => $chatRoom->guest_name,
                'status' => $chatRoom->status,
                'created_at' => $chatRoom->created_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Send message in guest chat
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'chat_room_id' => 'required|exists:chat_rooms,id',
        ]);

        // Get guest session
        $sessionId = session('guest_chat_session');
        if (!$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi chat tidak ditemukan. Silakan mulai chat baru.',
            ], 401);
        }

        // Verify chat room belongs to this guest session
        $chatRoom = ChatRoom::where('id', $request->chat_room_id)
            ->where('guest_session_id', $sessionId)
            ->where('is_guest', true)
            ->first();

        if (!$chatRoom) {
            return response()->json([
                'success' => false,
                'message' => 'Chat room tidak ditemukan atau tidak valid.',
            ], 404);
        }

        // Check if chat is still open
        if ($chatRoom->isClosed()) {
            return response()->json([
                'success' => false,
                'message' => 'Chat sudah ditutup. Silakan mulai chat baru.',
            ], 400);
        }

        // Send message
        $message = ChatMessage::createGuestMessage(
            $chatRoom->id,
            $request->message
        );

        return response()->json([
            'success' => true,
            'message' => 'Pesan terkirim',
            'data' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_name' => $chatRoom->guest_name,
                'is_from_guest' => true,
                'created_at' => $message->created_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Get chat messages for guest
     */
    public function getMessages(Request $request): JsonResponse
    {
        $request->validate([
            'chat_room_id' => 'required|exists:chat_rooms,id',
        ]);

        // Get guest session
        $sessionId = session('guest_chat_session');
        if (!$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi chat tidak ditemukan.',
            ], 401);
        }

        // Verify chat room belongs to this guest session
        $chatRoom = ChatRoom::where('id', $request->chat_room_id)
            ->where('guest_session_id', $sessionId)
            ->where('is_guest', true)
            ->first();

        if (!$chatRoom) {
            return response()->json([
                'success' => false,
                'message' => 'Chat room tidak ditemukan.',
            ], 404);
        }

        // Get messages
        $messages = $chatRoom->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) use ($chatRoom) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'message_type' => $message->message_type,
                    'sender_name' => $message->getSenderName(),
                    'is_from_guest' => $message->is_from_guest,
                    'is_from_admin' => $message->isFromAdmin(),
                    'attachment_url' => $message->attachment_url,
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'chat_room' => [
                    'id' => $chatRoom->id,
                    'subject' => $chatRoom->subject,
                    'status' => $chatRoom->status,
                    'guest_name' => $chatRoom->guest_name,
                    'assigned_admin' => $chatRoom->admin ? $chatRoom->admin->full_name : null,
                ],
                'messages' => $messages,
            ]
        ]);
    }

    /**
     * Check guest chat status
     */
    public function getChatStatus(): JsonResponse
    {
        $sessionId = session('guest_chat_session');
        
        if (!$sessionId) {
            return response()->json([
                'success' => true,
                'has_active_chat' => false,
                'data' => null,
            ]);
        }

        $chatRoom = ChatRoom::byGuestSession($sessionId)
            ->where('status', '!=', ChatRoom::STATUS_CLOSED)
            ->first();

        if (!$chatRoom) {
            return response()->json([
                'success' => true,
                'has_active_chat' => false,
                'data' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'has_active_chat' => true,
            'data' => [
                'chat_room_id' => $chatRoom->id,
                'status' => $chatRoom->status,
                'guest_name' => $chatRoom->guest_name,
                'assigned_admin' => $chatRoom->admin ? $chatRoom->admin->full_name : null,
                'created_at' => $chatRoom->created_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * End guest chat session
     */
    public function endChat(Request $request): JsonResponse
    {
        $request->validate([
            'chat_room_id' => 'required|exists:chat_rooms,id',
        ]);

        $sessionId = session('guest_chat_session');
        if (!$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi chat tidak ditemukan.',
            ], 401);
        }

        $chatRoom = ChatRoom::where('id', $request->chat_room_id)
            ->where('guest_session_id', $sessionId)
            ->where('is_guest', true)
            ->first();

        if (!$chatRoom) {
            return response()->json([
                'success' => false,
                'message' => 'Chat room tidak ditemukan.',
            ], 404);
        }

        // Close chat room
        $chatRoom->close();

        // Add system message
        ChatMessage::create([
            'chat_room_id' => $chatRoom->id,
            'user_id' => null,
            'message' => 'Guest telah mengakhiri chat.',
            'message_type' => ChatMessage::TYPE_SYSTEM,
            'is_from_guest' => false,
        ]);

        // Clear session
        session()->forget('guest_chat_session');

        return response()->json([
            'success' => true,
            'message' => 'Chat telah diakhiri. Terima kasih!',
        ]);
    }

    /**
     * Poll for new messages (for real-time updates)
     */
    public function pollMessages(Request $request): JsonResponse
    {
        $request->validate([
            'chat_room_id' => 'required|exists:chat_rooms,id',
            'last_message_id' => 'nullable|integer',
        ]);

        $sessionId = session('guest_chat_session');
        if (!$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi chat tidak ditemukan.',
            ], 401);
        }

        $chatRoom = ChatRoom::where('id', $request->chat_room_id)
            ->where('guest_session_id', $sessionId)
            ->where('is_guest', true)
            ->first();

        if (!$chatRoom) {
            return response()->json([
                'success' => false,
                'message' => 'Chat room tidak ditemukan.',
            ], 404);
        }

        // Get new messages since last message ID
        $query = $chatRoom->messages()->with('sender');
        
        if ($request->last_message_id) {
            $query->where('id', '>', $request->last_message_id);
        }

        $newMessages = $query->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) use ($chatRoom) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'message_type' => $message->message_type,
                    'sender_name' => $message->getSenderName(),
                    'is_from_guest' => $message->is_from_guest,
                    'is_from_admin' => $message->isFromAdmin(),
                    'attachment_url' => $message->attachment_url,
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'new_messages' => $newMessages,
                'chat_status' => $chatRoom->status,
                'assigned_admin' => $chatRoom->admin ? $chatRoom->admin->full_name : null,
            ]
        ]);
    }
}
