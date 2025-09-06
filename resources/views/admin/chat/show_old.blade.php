@extends('layouts.admin')

@section('title', 'Chat Room - ' . $chatRoom->user->full_name)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <!-- Back Button -->
                    <a href="{{ route('admin.chat.index') }}" class="flex items-center text-gray-600 hover:text-gray-900 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span class="font-medium">Back to Chat List</span>
                    </a>
                    
                    <!-- Room Info -->
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-sm">
                            <span class="text-sm font-bold text-white">
                                {{ strtoupper(substr($chatRoom->user->full_name, 0, 2)) }}
                            </span>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">{{ $chatRoom->user->full_name }}</h1>
                            <p class="text-gray-600 text-sm">{{ $chatRoom->subject ?? 'Chat Support' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Room Actions -->
                <div class="flex items-center space-x-3">
                    <!-- Status & Priority Badges -->
                    <span class="px-3 py-1 text-sm font-medium rounded-full
                        @if($chatRoom->status === 'open') bg-orange-100 text-orange-800
                        @elseif($chatRoom->status === 'assigned') bg-blue-100 text-blue-800  
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($chatRoom->status) }}
                    </span>

                    <span class="px-3 py-1 text-sm font-medium rounded-full
                        @if($chatRoom->priority === 'urgent') bg-red-100 text-red-800
                        @elseif($chatRoom->priority === 'high') bg-orange-100 text-orange-800
                        @elseif($chatRoom->priority === 'medium') bg-yellow-100 text-yellow-800
                        @else bg-green-100 text-green-800
                        @endif">
                        {{ ucfirst($chatRoom->priority) }}
                    </span>

                    <!-- Action Buttons -->
                    @if($chatRoom->status !== 'closed')
                        @if($chatRoom->assigned_admin_id !== auth()->id())
                            <form action="{{ route('admin.chat.rooms.assign', $chatRoom) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    Assign to Me
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('admin.chat.rooms.close', $chatRoom) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to close this chat room?')">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200">
                                Close Room
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Container -->
    <div class="flex flex-1 h-screen">
        <!-- Chat Messages Area -->
        <div class="flex-1 flex flex-col">
            <!-- Messages Container -->
            <div class="flex-1 overflow-y-auto px-6 py-4" id="messagesContainer">
                <div class="space-y-4" id="messagesList">
                    @forelse($chatRoom->messages as $message)
                        <div class="flex {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-xs lg:max-w-md">
                                <!-- Message Bubble -->
                                <div class="
                                    @if($message->user_id === auth()->id())
                                        bg-blue-600 text-white rounded-lg rounded-br-sm
                                    @else
                                        bg-white text-gray-900 rounded-lg rounded-bl-sm border border-gray-200
                                    @endif
                                    px-4 py-3 shadow-sm
                                ">
                                    <p class="text-sm">{{ $message->message }}</p>
                                </div>
                                
                                <!-- Message Meta -->
                                <div class="flex items-center {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }} mt-1 space-x-2">
                                    <span class="text-xs text-gray-500">
                                        {{ $message->user->full_name }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $message->created_at->format('H:i') }}
                                    </span>
                                    @if($message->is_read)
                                        <svg class="w-3 h-3 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada pesan</h3>
                            <p class="text-gray-600">Mulai percakapan dengan mengirim pesan.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Typing Indicator -->
                <div id="typingIndicator" class="hidden flex justify-start mt-4">
                    <div class="max-w-xs lg:max-w-md">
                        <div class="bg-gray-100 rounded-lg rounded-bl-sm px-4 py-3">
                            <div class="flex items-center space-x-1">
                                <div class="flex space-x-1">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                                <span class="text-xs text-gray-500 ml-2">sedang mengetik...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message Input Area -->
            @if($chatRoom->status !== 'closed')
                <div class="border-t border-gray-200 bg-white px-6 py-4">
                    <form id="messageForm" action="{{ route('admin.chat.rooms.messages.store', $chatRoom) }}" method="POST" class="flex items-end space-x-4">
                        @csrf
                        <div class="flex-1">
                            <textarea 
                                id="messageInput"
                                name="message" 
                                rows="3" 
                                placeholder="Type your message..." 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                required></textarea>
                        </div>
                        <button 
                            type="submit" 
                            id="sendButton"
                            class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            @else
                <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                    <div class="text-center text-gray-500">
                        <p class="font-medium">Chat room sudah ditutup</p>
                        <p class="text-sm">Percakapan tidak dapat dilanjutkan.</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div class="w-80 bg-white border-l border-gray-200">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Room Information</h3>
                
                <!-- User Info -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-sm">
                            <span class="text-sm font-bold text-white">
                                {{ strtoupper(substr($chatRoom->user->full_name, 0, 2)) }}
                            </span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $chatRoom->user->full_name }}</h4>
                            <p class="text-sm text-gray-600">{{ $chatRoom->user->email }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phone:</span>
                            <span class="text-gray-900">{{ $chatRoom->user->phone ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Joined:</span>
                            <span class="text-gray-900">{{ $chatRoom->user->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Room Details -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <p class="text-sm text-gray-900 bg-gray-50 rounded px-3 py-2">
                            {{ $chatRoom->subject ?? 'General Support' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <span class="inline-block px-3 py-1 text-sm font-medium rounded-full
                            @if($chatRoom->status === 'open') bg-orange-100 text-orange-800
                            @elseif($chatRoom->status === 'assigned') bg-blue-100 text-blue-800  
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($chatRoom->status) }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <span class="inline-block px-3 py-1 text-sm font-medium rounded-full
                            @if($chatRoom->priority === 'urgent') bg-red-100 text-red-800
                            @elseif($chatRoom->priority === 'high') bg-orange-100 text-orange-800
                            @elseif($chatRoom->priority === 'medium') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800
                            @endif">
                            {{ ucfirst($chatRoom->priority) }}
                        </span>
                    </div>

                    @if($chatRoom->assigned_admin_id)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Assigned to</label>
                            <p class="text-sm text-gray-900 bg-gray-50 rounded px-3 py-2">
                                {{ $chatRoom->assignedAdmin->full_name ?? 'Unknown Admin' }}
                            </p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Created</label>
                        <p class="text-sm text-gray-900 bg-gray-50 rounded px-3 py-2">
                            {{ $chatRoom->created_at->format('M d, Y H:i') }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Activity</label>
                        <p class="text-sm text-gray-900 bg-gray-50 rounded px-3 py-2">
                            {{ $chatRoom->updated_at->diffForHumans() }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Messages</label>
                        <p class="text-sm text-gray-900 bg-gray-50 rounded px-3 py-2">
                            {{ $chatRoom->messages->count() }} messages
                        </p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Quick Actions</h4>
                    <div class="space-y-2">
                        @if($chatRoom->status !== 'closed' && $chatRoom->assigned_admin_id !== auth()->id())
                            <form action="{{ route('admin.chat.rooms.assign', $chatRoom) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    Assign to Me
                                </button>
                            </form>
                        @endif

                        @if($chatRoom->status !== 'closed')
                            <form action="{{ route('admin.chat.rooms.close', $chatRoom) }}" method="POST" onsubmit="return confirm('Are you sure you want to close this chat room?')">
                                @csrf
                                <button type="submit" class="w-full px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200">
                                    Close Room
                                </button>
                            </form>
                        @endif

                        <button onclick="window.print()" class="w-full px-3 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors duration-200">
                            Print Chat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messagesContainer');
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');
    const messagesList = document.getElementById('messagesList');
    const typingIndicator = document.getElementById('typingIndicator');

    // Scroll to bottom
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Initial scroll to bottom
    scrollToBottom();

    // Handle form submission
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        // Disable send button
        sendButton.disabled = true;
        sendButton.innerHTML = `
            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        `;

        // Send message via fetch
        fetch(this.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add message to UI immediately
                addMessageToUI(data.message, true);
                messageInput.value = '';
                scrollToBottom();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to send message. Please try again.');
        })
        .finally(() => {
            // Re-enable send button
            sendButton.disabled = false;
            sendButton.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
            `;
        });
    });

    // Handle typing indicator
    let typingTimer;
    messageInput.addEventListener('input', function() {
        // Send typing indicator
        sendTypingIndicator();
        
        // Clear previous timer
        clearTimeout(typingTimer);
        
        // Stop typing after 3 seconds
        typingTimer = setTimeout(() => {
            stopTypingIndicator();
        }, 3000);
    });

    function sendTypingIndicator() {
        fetch('/admin/api/chat/typing', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                chat_room_id: {{ $chatRoom->id }},
                is_typing: true
            })
        });
    }

    function stopTypingIndicator() {
        fetch('/admin/api/chat/typing', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                chat_room_id: {{ $chatRoom->id }},
                is_typing: false
            })
        });
    }

    function addMessageToUI(message, isFromCurrentUser = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${isFromCurrentUser ? 'justify-end' : 'justify-start'}`;
        
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { 
            hour12: false, 
            hour: '2-digit', 
            minute: '2-digit' 
        });

        messageDiv.innerHTML = `
            <div class="max-w-xs lg:max-w-md">
                <div class="
                    ${isFromCurrentUser 
                        ? 'bg-blue-600 text-white rounded-lg rounded-br-sm' 
                        : 'bg-white text-gray-900 rounded-lg rounded-bl-sm border border-gray-200'
                    }
                    px-4 py-3 shadow-sm
                ">
                    <p class="text-sm">${message.message}</p>
                </div>
                <div class="flex items-center ${isFromCurrentUser ? 'justify-end' : 'justify-start'} mt-1 space-x-2">
                    <span class="text-xs text-gray-500">${message.user_name}</span>
                    <span class="text-xs text-gray-500">${timeString}</span>
                </div>
            </div>
        `;

        messagesList.appendChild(messageDiv);
    }

    // Auto-refresh messages (every 5 seconds)
    setInterval(function() {
        // This will be replaced with real-time WebSocket updates
        // For now, we can implement polling
        refreshMessages();
    }, 5000);

    function refreshMessages() {
        // Implementation for polling new messages
        console.log('Refreshing messages...');
    }

    // Enter key to send message (Shift+Enter for new line)
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            messageForm.dispatchEvent(new Event('submit'));
        }
    });
});
</script>
@endsection
