<x-admin-layout>
    <x-slot name="title">Chat Room - {{ $chatRoom->getInitiatorName() }}</x-slot>

    <!-- Meta tags for JavaScript -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    <meta name="chat-room-id" content="{{ $chatRoom->id }}">

    <!-- Chat Room Container -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <!-- Back Button -->
                    <a href="{{ route('admin.chat.index') }}" class="flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span class="font-medium">Back to Chat List</span>
                    </a>
                    
                    <!-- Room Info -->
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                            <span class="text-sm font-bold text-white">
                                {{ strtoupper(substr($chatRoom->getInitiatorName(), 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">{{ $chatRoom->getInitiatorName() }}</h2>
                            <p class="text-gray-600 text-sm">
                                {{ $chatRoom->subject ?? 'Chat Support' }}
                                @if($chatRoom->isGuestChat())
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                        Guest Chat
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Status & Actions -->
                <div class="flex items-center space-x-4">
                    <!-- Status -->
                    @php
                        $statusClasses = [
                            'open' => 'bg-orange-100 text-orange-800',
                            'assigned' => 'bg-blue-100 text-blue-800',
                            'closed' => 'bg-gray-100 text-gray-800',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClasses[$chatRoom->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($chatRoom->status) }}
                    </span>

                    <!-- Priority -->
                    @php
                        $priorityClasses = [
                            'urgent' => 'bg-red-100 text-red-800',
                            'high' => 'bg-orange-100 text-orange-800',
                            'medium' => 'bg-yellow-100 text-yellow-800',
                            'low' => 'bg-green-100 text-green-800',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $priorityClasses[$chatRoom->priority] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($chatRoom->priority) }}
                    </span>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2">
                        @if($chatRoom->status === 'open')
                            <form method="POST" action="{{ route('admin.chat.assign', $chatRoom) }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Assign to Me
                                </button>
                            </form>
                        @endif
                        
                        @if($chatRoom->status !== 'closed')
                            <form method="POST" action="{{ route('admin.chat.close', $chatRoom) }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Close Chat
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Messages Area -->
        <div class="h-96 overflow-y-auto p-6 space-y-4" id="messagesContainer">
            @forelse($messages as $message)
                @php
                    $isFromInitiator = $chatRoom->isGuestChat() ? $message->is_from_guest : ($message->user_id === $chatRoom->user_id);
                @endphp
                <div class="flex {{ $isFromInitiator ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
                    <div class="max-w-xs lg:max-w-md">
                        <!-- Message bubble -->
                        <div class="px-4 py-3 rounded-lg {{ $isFromInitiator ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900' }}">
                            <p class="text-sm">{{ $message->message }}</p>
                        </div>
                        
                        <!-- Message info -->
                        <div class="mt-1 text-xs text-gray-500 {{ $isFromInitiator ? 'text-right' : 'text-left' }}">
                            <span>{{ $message->getSenderName() }}</span>
                            <span class="mx-1">•</span>
                            <span>{{ $message->created_at->format('H:i') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-8">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <p>No messages yet</p>
                </div>
            @endforelse
            
            <!-- Typing Indicator -->
            <div id="typingIndicator" style="display: none;"></div>
        </div>

        <!-- Message Input -->
        @if($chatRoom->status !== 'closed')
            <div class="px-6 py-4 border-t border-gray-200">
                <form id="messageForm" data-chat-room="{{ $chatRoom->id }}">
                    @csrf
                    <div class="flex items-end space-x-4">
                        <div class="flex-1">
                            <textarea 
                                name="message" 
                                id="messageInput"
                                rows="3" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none" 
                                placeholder="Type your message..."
                                required></textarea>
                        </div>
                        <button 
                            type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <p class="text-center text-gray-500">This chat has been closed</p>
            </div>
        @endif
    </div>

    <!-- Chat Info Sidebar -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Chat Information</h3>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="text-sm font-medium text-gray-500">
                    {{ $chatRoom->isGuestChat() ? 'Guest' : 'Customer' }}
                </label>
                <p class="text-sm text-gray-900">{{ $chatRoom->getInitiatorName() }}</p>
                <p class="text-sm text-gray-500">
                    {{ $chatRoom->getInitiatorEmail() ?? '-' }}
                    @if($chatRoom->isGuestChat())
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                            Guest Session
                        </span>
                    @endif
                </p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-500">Subject</label>
                <p class="text-sm text-gray-900">{{ $chatRoom->subject ?? 'General Support' }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-500">Assigned Admin</label>
                <p class="text-sm text-gray-900">{{ $chatRoom->admin->full_name ?? $chatRoom->admin->username ?? 'Not assigned' }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-500">Created</label>
                <p class="text-sm text-gray-900">{{ $chatRoom->created_at->format('d M Y, H:i') }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-500">Last Activity</label>
                <p class="text-sm text-gray-900">{{ $chatRoom->updated_at->diffForHumans() }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-500">Total Messages</label>
                <p class="text-sm text-gray-900">{{ $chatRoom->messages->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="fixed top-4 right-4 z-50 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg shadow-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-4 right-4 z-50 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg shadow-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- JavaScript for chat functionality -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const messagesContainer = document.getElementById('messagesContainer');
        const messageForm = document.getElementById('messageForm');
        const messageInput = document.getElementById('messageInput');
        const chatRoomId = {{ $chatRoom->id }};
        
        // Auto-scroll to bottom
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        console.log('🔄 Admin Chat: Using Polling System Only');

        // Handle form submission
        if (messageForm) {
            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                sendMessage();
            });
            
            // Handle enter key
            messageInput?.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
        }
        
        // Auto-hide alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.fixed.top-4.right-4');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);

        // Send message function
        async function sendMessage() {
            if (!messageInput.value.trim() || !chatRoomId) return;
            
            const message = messageInput.value.trim();
            
            // Disable form while sending
            const submitBtn = messageForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            
            try {
                const response = await fetch(`/admin/api/chat/${chatRoomId}/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ message })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        messageInput.value = '';
                        // Message will be displayed via polling from chat.js
                        console.log('✅ Message sent successfully');
                    }
                } else {
                    const errorData = await response.json();
                    alert(errorData.error || 'Failed to send message');
                }
            } catch (error) {
                console.error('Send message error:', error);
                alert('Failed to send message');
            } finally {
                submitBtn.disabled = false;
            }
        }
        
        // Request notification permission
        if (Notification.permission === 'default') {
            Notification.requestPermission();
        }
    });
    </script>
</x-admin-layout>
