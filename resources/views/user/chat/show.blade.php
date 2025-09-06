<x-app-layout>
    @php($user = auth()->user())
    @php($initials = collect(explode(' ', trim($user->full_name ?: $user->username)))->filter()->take(2)->map(fn($p)=> strtoupper(mb_substr($p,0,1)))->implode(''))
    
    <div class="min-h-screen bg-[#1a1d21] text-neutral-100">
        <!-- Header Section -->
        <div class="sticky top-0 z-40 backdrop-blur bg-[#1f2226]/95 border-b border-neutral-800/70">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <!-- Back Button -->
                            <a href="{{ route('user.chat.index') }}" class="p-2 rounded-lg bg-neutral-800/50 hover:bg-neutral-700/50 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </a>
                            <!-- Chat Info -->
                            <div>
                                <h1 class="text-lg font-semibold">{{ $chatRoom->subject }}</h1>
                                <div class="flex items-center space-x-2 text-sm text-neutral-400">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $chatRoom->getPriorityColor() }}">
                                        {{ ucfirst($chatRoom->priority) }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $chatRoom->getStatusColor() }}">
                                        {{ ucfirst($chatRoom->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Admin Info (if assigned) -->
                        @if($chatRoom->admin)
                            <div class="flex items-center space-x-2">
                                <div class="text-right">
                                    <p class="text-sm font-medium">{{ $chatRoom->admin->full_name ?? $chatRoom->admin->username }}</p>
                                    <p class="text-xs text-neutral-400">Customer Service</p>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#FE2C55]/20 to-[#25F4EE]/20 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Messages Container -->
        <div class="flex flex-col h-[calc(100vh-120px)]">
            <!-- Messages Area -->
            <div id="messages-container" class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 py-4 space-y-4">
                @forelse($messages as $message)
                    <div class="flex {{ $message->user_id === $user->id ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs lg:max-w-md">
                            <!-- Message Bubble -->
                            <div class="relative {{ $message->user_id === $user->id ? 'bg-gradient-to-r from-[#FE2C55] to-[#FE2C55]/80' : 'bg-neutral-800/60' }} rounded-2xl px-4 py-3 {{ $message->user_id === $user->id ? 'rounded-br-md' : 'rounded-bl-md' }}">
                                <!-- Message Content -->
                                @if($message->message_type === 'file' && $message->attachment_path)
                                    <div class="mb-2">
                                        @if(in_array(pathinfo($message->attachment_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                            <img src="{{ Storage::url($message->attachment_path) }}" alt="Attachment" class="max-w-full h-auto rounded-lg">
                                        @else
                                            <a href="{{ Storage::url($message->attachment_path) }}" target="_blank" class="flex items-center space-x-2 p-2 bg-black/20 rounded-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span class="text-sm">{{ basename($message->attachment_path) }}</span>
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                
                                @if($message->message)
                                    <p class="text-sm {{ $message->user_id === $user->id ? 'text-white' : 'text-neutral-100' }}">{{ $message->message }}</p>
                                @endif
                                
                                <!-- Message Meta -->
                                <div class="flex items-center justify-between mt-2 text-xs {{ $message->user_id === $user->id ? 'text-white/70' : 'text-neutral-400' }}">
                                    <span>{{ $message->sender->username ?? 'System' }}</span>
                                    <span>{{ $message->created_at->format('H:i') }}</span>
                                </div>
                            </div>
                            
                            <!-- Read Status -->
                            @if($message->user_id === $user->id)
                                <div class="text-right mt-1">
                                    @if($message->is_read)
                                        <span class="text-xs text-blue-400">✓✓ Dibaca</span>
                                    @else
                                        <span class="text-xs text-neutral-500">✓ Terkirim</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-neutral-800/50 flex items-center justify-center">
                            <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4-.8L3 20l1.22-2.44A7.793 7.793 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <p class="text-neutral-400">Belum ada pesan dalam chat ini</p>
                    </div>
                @endforelse
                
                <!-- Typing Indicator -->
                <div id="typing-indicator" class="hidden">
                    <div class="flex justify-start">
                        <div class="max-w-xs lg:max-w-md">
                            <div class="bg-neutral-800/60 rounded-2xl rounded-bl-md px-4 py-3">
                                <div class="flex space-x-1">
                                    <div class="w-2 h-2 bg-neutral-400 rounded-full animate-pulse"></div>
                                    <div class="w-2 h-2 bg-neutral-400 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                                    <div class="w-2 h-2 bg-neutral-400 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message Input Area -->
            @if(!$chatRoom->isClosed())
                <div class="border-t border-neutral-800/70 bg-[#1f2226]/95 backdrop-blur">
                    <div class="px-4 sm:px-6 lg:px-8 py-4">
                        <form id="message-form" class="flex items-end space-x-3">
                            @csrf
                            <!-- Attachment Button -->
                            <div class="flex-shrink-0">
                                <input type="file" id="attachment-input" name="attachment" class="hidden" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                <button type="button" id="attachment-button" class="p-3 rounded-full bg-neutral-800/60 hover:bg-neutral-700/60 transition">
                                    <svg class="w-5 h-5 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Message Input -->
                            <div class="flex-1 relative">
                                <textarea 
                                    id="message-input" 
                                    name="message" 
                                    placeholder="Ketik pesan Anda..." 
                                    class="w-full px-4 py-3 rounded-xl bg-neutral-800/60 border border-neutral-700/50 focus:border-[#FE2C55]/50 focus:ring-2 focus:ring-[#FE2C55]/20 text-neutral-100 placeholder-neutral-400 resize-none" 
                                    rows="1"
                                    maxlength="1000"
                                    required
                                ></textarea>
                                <div id="attachment-preview" class="hidden mt-2 p-2 bg-neutral-800/40 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <span id="attachment-name" class="text-sm text-neutral-300"></span>
                                        <button type="button" id="remove-attachment" class="text-red-400 hover:text-red-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Send Button -->
                            <div class="flex-shrink-0">
                                <button type="submit" id="send-button" class="p-3 rounded-full bg-gradient-to-r from-[#FE2C55] to-[#FE2C55]/80 hover:from-[#FE2C55]/90 hover:to-[#FE2C55]/70 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="border-t border-neutral-800/70 bg-[#1f2226]/95 backdrop-blur">
                    <div class="px-4 sm:px-6 lg:px-8 py-4 text-center">
                        <p class="text-neutral-400">Chat ini telah ditutup</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Chat JavaScript -->
    <script>
        const chatRoomId = {{ $chatRoom->id }};
        const currentUserId = {{ $user->id }};
        const csrfToken = '{{ csrf_token() }}';
        
        let typingTimer;
        let isTyping = false;
        
        // DOM elements
        const messagesContainer = document.getElementById('messages-container');
        const messageForm = document.getElementById('message-form');
        const messageInput = document.getElementById('message-input');
        const sendButton = document.getElementById('send-button');
        const attachmentInput = document.getElementById('attachment-input');
        const attachmentButton = document.getElementById('attachment-button');
        const attachmentPreview = document.getElementById('attachment-preview');
        const attachmentName = document.getElementById('attachment-name');
        const removeAttachment = document.getElementById('remove-attachment');
        const typingIndicator = document.getElementById('typing-indicator');

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            scrollToBottom();
            messageInput.focus();
            startPolling();
            
            // Auto-resize textarea
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';
                
                // Handle typing indicator
                handleTyping();
            });
            
            // Attachment handling
            attachmentButton.addEventListener('click', () => attachmentInput.click());
            attachmentInput.addEventListener('change', handleAttachment);
            removeAttachment.addEventListener('click', clearAttachment);
            
            // Form submission
            messageForm.addEventListener('submit', handleSubmit);
            
            // Enter key handling
            messageInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    handleSubmit(e);
                }
            });
        });

        // Scroll to bottom
        function scrollToBottom() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Handle form submission
        async function handleSubmit(e) {
            e.preventDefault();
            
            const message = messageInput.value.trim();
            const attachment = attachmentInput.files[0];
            
            if (!message && !attachment) return;
            
            const formData = new FormData();
            formData.append('_token', csrfToken);
            if (message) formData.append('message', message);
            if (attachment) formData.append('attachment', attachment);
            
            // Disable form
            sendButton.disabled = true;
            messageInput.disabled = true;
            
            try {
                const response = await fetch(`{{ route('user.chat.send', $chatRoom) }}`, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Clear form
                    messageInput.value = '';
                    messageInput.style.height = 'auto';
                    clearAttachment();
                    
                    // Add message to UI
                    addMessageToUI(data.message);
                    scrollToBottom();
                    
                    // Stop typing indicator
                    stopTyping();
                } else {
                    alert('Gagal mengirim pesan. Silakan coba lagi.');
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
            
            // Re-enable form
            sendButton.disabled = false;
            messageInput.disabled = false;
            messageInput.focus();
        }

        // Handle attachment
        function handleAttachment() {
            const file = attachmentInput.files[0];
            if (file) {
                attachmentName.textContent = file.name;
                attachmentPreview.classList.remove('hidden');
            }
        }

        // Clear attachment
        function clearAttachment() {
            attachmentInput.value = '';
            attachmentPreview.classList.add('hidden');
        }

        // Handle typing indicator
        function handleTyping() {
            if (!isTyping) {
                isTyping = true;
                sendTypingStatus(true);
            }
            
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                isTyping = false;
                sendTypingStatus(false);
            }, 1000);
        }

        // Stop typing
        function stopTyping() {
            if (isTyping) {
                isTyping = false;
                clearTimeout(typingTimer);
                sendTypingStatus(false);
            }
        }

        // Send typing status
        async function sendTypingStatus(typing) {
            try {
                await fetch('{{ route("user.chat.typing") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        chat_room_id: chatRoomId,
                        is_typing: typing
                    })
                });
            } catch (error) {
                console.error('Error sending typing status:', error);
            }
        }

        // Add message to UI
        function addMessageToUI(message) {
            // Check if message already exists
            const existingMessage = messagesContainer.querySelector(`[data-message-id="${message.id}"]`);
            if (existingMessage) {
                return;
            }
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `flex ${message.user_id === currentUserId ? 'justify-end' : 'justify-start'}`;
            messageDiv.setAttribute('data-message-id', message.id);
            
            const messageTime = new Date(message.created_at);
            const timeString = messageTime.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
            
            messageDiv.innerHTML = `
                <div class="max-w-xs lg:max-w-md">
                    <div class="relative ${message.user_id === currentUserId ? 'bg-gradient-to-r from-[#FE2C55] to-[#FE2C55]/80' : 'bg-neutral-800/60'} rounded-2xl px-4 py-3 ${message.user_id === currentUserId ? 'rounded-br-md' : 'rounded-bl-md'}">
                        ${message.attachment_path ? `
                            <div class="mb-2">
                                ${message.attachment_path.match(/\.(jpg|jpeg|png|gif)$/i) ? 
                                    `<img src="/storage/${message.attachment_path}" alt="Attachment" class="max-w-full h-auto rounded-lg">` :
                                    `<a href="/storage/${message.attachment_path}" target="_blank" class="flex items-center space-x-2 p-2 bg-black/20 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="text-sm">${message.attachment_path.split('/').pop()}</span>
                                    </a>`
                                }
                            </div>
                        ` : ''}
                        ${message.message ? `<p class="text-sm ${message.user_id === currentUserId ? 'text-white' : 'text-neutral-100'}">${escapeHtml(message.message)}</p>` : ''}
                        <div class="flex items-center justify-between mt-2 text-xs ${message.user_id === currentUserId ? 'text-white/70' : 'text-neutral-400'}">
                            <span>${escapeHtml(message.sender?.full_name || message.sender?.username || 'System')}</span>
                            <span>${timeString}</span>
                        </div>
                    </div>
                    ${message.user_id === currentUserId ? `
                        <div class="text-right mt-1">
                            <span class="text-xs text-neutral-500">✓ Terkirim</span>
                        </div>
                    ` : ''}
                </div>
            `;
            
            messagesContainer.appendChild(messageDiv);
        }

        // Utility function to escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }

        // Polling for new messages
        let lastMessageId = {{ $messages->last()?->id ?? 0 }};
        
        function startPolling() {
            setInterval(async () => {
                try {
                    const response = await fetch(`{{ route('user.chat.poll', $chatRoom) }}?last_message_id=${lastMessageId}`);
                    const data = await response.json();
                    
                    if (data.success && data.messages.length > 0) {
                        data.messages.forEach(message => {
                            addMessageToUI(message);
                            lastMessageId = Math.max(lastMessageId, message.id);
                        });
                        scrollToBottom();
                    }
                } catch (error) {
                    console.error('Polling error:', error);
                }
            }, 3000); // Poll every 3 seconds
        }

        // Mark messages as read when page becomes visible
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                markAsRead();
            }
        });

        // Mark messages as read
        async function markAsRead() {
            try {
                await fetch('{{ route("user.chat.mark-read") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        chat_room_id: chatRoomId
                    })
                });
            } catch (error) {
                console.error('Error marking as read:', error);
            }
        }

        // Initial mark as read
        markAsRead();
    </script>
</x-app-layout>
