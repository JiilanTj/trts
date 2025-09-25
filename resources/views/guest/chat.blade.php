<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Support Chat - {{ config('app.name', 'Laravel') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-gray-100">
    @php
        $setting = \App\Models\Setting::first();
    @endphp

    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-4xl mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        @if($setting && $setting->logo_url)
                            <img src="{{ $setting->logo_url }}" alt="Logo" class="w-10 h-10">
                        @else
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <h1 class="text-xl font-semibold text-gray-800">Customer Support</h1>
                            <p class="text-sm text-gray-600">We're here to help you</p>
                        </div>
                    </div>
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        ← Back to Login
                    </a>
                </div>
            </div>
        </div>

        <!-- Chat Container -->
        <div class="max-w-4xl mx-auto px-4 py-6">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                
                <!-- Chat Status -->
                <div id="chatStatus" class="px-6 py-3 bg-blue-50 border-b hidden">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-sm text-gray-700">Connected to support</span>
                    </div>
                </div>

                <!-- Chat Messages Area -->
                <div class="h-96 overflow-y-auto p-6 space-y-4" id="messagesContainer">
                    <!-- Welcome Message -->
                    <div class="flex justify-start">
                        <div class="max-w-xs lg:max-w-md">
                            <div class="px-4 py-3 rounded-lg bg-gray-100 text-gray-900">
                                <p class="text-sm">👋 Halo! Selamat datang di layanan customer support kami. Bagaimana kami bisa membantu Anda hari ini?</p>
                            </div>
                            <div class="mt-1 text-xs text-gray-500 text-left">
                                <span>Support Team</span>
                                <span class="mx-1">•</span>
                                <span id="currentTime"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Start Chat Form (Initially shown) -->
                <div id="startChatForm" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <form id="chatStartForm" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="guest_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Anda *</label>
                                <input 
                                    type="text" 
                                    id="guest_name" 
                                    name="guest_name" 
                                    required 
                                    placeholder="Masukkan nama Anda"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" />
                            </div>
                            <div>
                                <label for="guest_email" class="block text-sm font-medium text-gray-700 mb-1">Email (Opsional)</label>
                                <input 
                                    type="email" 
                                    id="guest_email" 
                                    name="guest_email" 
                                    placeholder="Masukkan email Anda"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" />
                            </div>
                        </div>
                        <div>
                            <label for="initial_message" class="block text-sm font-medium text-gray-700 mb-1">Pesan Anda *</label>
                            <textarea 
                                id="initial_message" 
                                name="initial_message" 
                                rows="3" 
                                required 
                                placeholder="Jelaskan masalah atau pertanyaan Anda..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm resize-none"></textarea>
                        </div>
                        <button 
                            type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                            Mulai Chat dengan Support
                        </button>
                    </form>
                </div>

                <!-- Message Input (Hidden initially) -->
                <div id="messageInputForm" class="px-6 py-4 border-t border-gray-200 hidden">
                    <form id="messageForm">
                        @csrf
                        <div class="flex items-end space-x-4">
                            <div class="flex-1">
                                <textarea 
                                    name="message" 
                                    id="messageInput"
                                    rows="3" 
                                    placeholder="Ketik pesan Anda..."
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
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

                <!-- Chat Ended Notice (Hidden initially) -->
                <div id="chatEndedNotice" class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-center hidden">
                    <p class="text-gray-600 text-sm">Chat telah berakhir. Terima kasih telah menghubungi kami!</p>
                    <button onclick="location.reload()" class="mt-2 text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Mulai Chat Baru
                    </button>
                </div>
            </div>

            <!-- Chat Info -->
            <div class="mt-4 text-center text-sm text-gray-600">
                <p>💬 Chat ini tidak memerlukan pendaftaran akun</p>
                <p class="mt-1">⏰ Tim support kami tersedia 24/7 untuk membantu Anda</p>
            </div>
        </div>
    </div>

    <!-- JavaScript for Chat -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatStartForm = document.getElementById('chatStartForm');
        const messageForm = document.getElementById('messageForm');
        const messagesContainer = document.getElementById('messagesContainer');
        const startChatDiv = document.getElementById('startChatForm');
        const messageInputDiv = document.getElementById('messageInputForm');
        const chatStatus = document.getElementById('chatStatus');
        const chatEndedNotice = document.getElementById('chatEndedNotice');
        const messageInput = document.getElementById('messageInput');
        
        let chatRoomId = null;
        let pollInterval = null;
        let lastMessageId = 0;

        // Set current time
        document.getElementById('currentTime').textContent = new Date().toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });

        // Start chat form submission
        chatStartForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Validate required fields
            const guestName = document.getElementById('guest_name').value.trim();
            const initialMessage = document.getElementById('initial_message').value.trim();
            
            if (!guestName || !initialMessage) {
                alert('Nama dan pesan wajib diisi');
                return;
            }
            
            const submitBtn = chatStartForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Memulai Chat...';
            
            const formData = new FormData();
            formData.append('guest_name', guestName);
            formData.append('guest_email', document.getElementById('guest_email').value.trim());
            formData.append('initial_message', initialMessage);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            
            try {
                const response = await fetch('/guest/chat/start', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Server error:', errorText);
                    throw new Error(`Server error: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    chatRoomId = data.data.chat_room_id;
                    
                    // Hide start form, show message input
                    startChatDiv.classList.add('hidden');
                    messageInputDiv.classList.remove('hidden');
                    chatStatus.classList.remove('hidden');
                    
                    // Add user message to chat
                    addMessage(formData.get('initial_message'), data.data.guest_name, true, new Date());
                    
                    // Start polling for messages
                    startPolling();
                    
                    // Focus on message input
                    messageInput.focus();
                } else {
                    alert(data.message || 'Gagal memulai chat');
                }
            } catch (error) {
                console.error('Start chat error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Mulai Chat dengan Support';
            }
        });

        // Message form submission
        messageForm?.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!messageInput.value.trim() || !chatRoomId) return;
            
            const message = messageInput.value.trim();
            const submitBtn = messageForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            
            try {
                const response = await fetch('/guest/chat/send-message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        message: message,
                        chat_room_id: chatRoomId
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        messageInput.value = '';
                        // Message will be added via polling
                    }
                } else {
                    const errorData = await response.json();
                    alert(errorData.message || 'Gagal mengirim pesan');
                }
            } catch (error) {
                console.error('Send message error:', error);
                alert('Gagal mengirim pesan');
            } finally {
                submitBtn.disabled = false;
            }
        });

        // Enter key handler - allow Shift+Enter for new line, Enter alone to send
        messageInput?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                messageForm.dispatchEvent(new Event('submit'));
            }
            // Let Shift+Enter pass through for new line (don't prevent default)
        });

        // Start polling for new messages
        function startPolling() {
            pollInterval = setInterval(async () => {
                try {
                    const response = await fetch(`/guest/chat/poll?chat_room_id=${chatRoomId}&last_message_id=${lastMessageId}`, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        if (data.success && data.data.new_messages.length > 0) {
                            data.data.new_messages.forEach(message => {
                                addMessage(
                                    message.message, 
                                    message.sender_name, 
                                    message.is_from_guest,
                                    new Date(message.created_at)
                                );
                                lastMessageId = Math.max(lastMessageId, message.id);
                            });
                        }
                        
                        // Check if chat is closed
                        if (data.data.chat_status === 'closed') {
                            endChat();
                        }
                    }
                } catch (error) {
                    console.error('Polling error:', error);
                }
            }, 3000); // Poll every 3 seconds
        }

        // Add message to chat
        function addMessage(message, senderName, isFromGuest, timestamp) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `flex ${isFromGuest ? 'justify-end' : 'justify-start'}`;
            
            const time = timestamp.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });
            
            messageDiv.innerHTML = `
                <div class="max-w-xs lg:max-w-md">
                    <div class="px-4 py-3 rounded-lg ${isFromGuest ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900'}">
                        <p class="text-sm whitespace-pre-wrap">${message}</p>
                    </div>
                    <div class="mt-1 text-xs text-gray-500 ${isFromGuest ? 'text-right' : 'text-left'}">
                        <span>${senderName}</span>
                        <span class="mx-1">•</span>
                        <span>${time}</span>
                    </div>
                </div>
            `;
            
            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // End chat
        function endChat() {
            if (pollInterval) {
                clearInterval(pollInterval);
            }
            messageInputDiv.classList.add('hidden');
            chatEndedNotice.classList.remove('hidden');
        }

        // Auto-scroll to bottom on load
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    });
    </script>
</body>
</html>
