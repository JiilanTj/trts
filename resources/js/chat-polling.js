/**
 * Chat Polling System for Production (No WebSocket)
 * This works on shared hosting like AAPanel
 */

class ChatPollingManager {
    constructor() {
        this.currentChatRoom = null;
        this.pollingInterval = null;
        this.lastMessageId = 0;
        this.lastActivity = null;
        this.isTyping = false;
        
        this.initializePage();
    }

    /**
     * Initialize based on current page
     */
    initializePage() {
        const path = window.location.pathname;
        
        if (path.includes('/admin/chat')) {
            if (path.match(/\/admin\/chat\/\d+/)) {
                // Individual chat room page
                this.initializeChatRoom();
            } else {
                // Chat dashboard
                this.initializeDashboard();
            }
        }
    }

    /**
     * Initialize dashboard with polling updates
     */
    initializeDashboard() {
        console.log('🔄 Initializing chat dashboard with polling...');
        
        // Poll for new chat rooms and updates every 10 seconds
        this.pollingInterval = setInterval(() => {
            this.pollDashboardUpdates();
        }, 10000);

        // Poll statistics every 30 seconds
        setInterval(() => {
            this.pollStatistics();
        }, 30000);
    }

    /**
     * Initialize chat room with polling
     */
    initializeChatRoom() {
        const chatRoomId = this.extractChatRoomId();
        if (!chatRoomId) return;

        this.currentChatRoom = chatRoomId;
        console.log(`🔄 Initializing chat room ${chatRoomId} with polling...`);

        // Poll for new messages every 3 seconds
        this.pollingInterval = setInterval(() => {
            this.pollNewMessages();
        }, 3000);

        // Setup message form
        this.setupMessageForm();
        
        // Setup typing indicator (with debounce)
        this.setupTypingIndicator();

        // Auto-scroll to bottom
        this.scrollToBottom();

        // Get initial last message ID
        this.getLastMessageId();
    }

    /**
     * Poll for dashboard updates
     */
    async pollDashboardUpdates() {
        try {
            const response = await fetch('/admin/api/chat/dashboard-updates', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.updateDashboard(data);
            }
        } catch (error) {
            console.error('Dashboard polling error:', error);
        }
    }

    /**
     * Poll for new messages in current chat room
     */
    async pollNewMessages() {
        if (!this.currentChatRoom) return;

        try {
            const response = await fetch(`/admin/api/chat/${this.currentChatRoom}/messages?after=${this.lastMessageId}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(message => {
                        this.handleNewMessage(message);
                        this.lastMessageId = Math.max(this.lastMessageId, message.id);
                    });
                }
            }
        } catch (error) {
            console.error('Message polling error:', error);
        }
    }

    /**
     * Poll for statistics updates
     */
    async pollStatistics() {
        try {
            const response = await fetch('/admin/api/chat/statistics', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.updateStatistics(data);
            }
        } catch (error) {
            console.error('Statistics polling error:', error);
        }
    }

    /**
     * Update dashboard with new data
     */
    updateDashboard(data) {
        // Update chat rooms table
        if (data.chat_rooms) {
            // Implementation depends on your table structure
            console.log('Dashboard updated:', data);
        }
    }

    /**
     * Update statistics display
     */
    updateStatistics(data) {
        // Update stat cards
        Object.keys(data).forEach(key => {
            const element = document.querySelector(`[data-stat="${key}"]`);
            if (element) {
                element.textContent = data[key];
            }
        });
    }

    /**
     * Handle new message display
     */
    handleNewMessage(message) {
        const messagesContainer = document.getElementById('messagesContainer');
        if (!messagesContainer) return;

        const messageHtml = this.createMessageHtml(message);
        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
        this.scrollToBottom();

        // Show notification if not from current user
        if (message.user_id !== parseInt(document.querySelector('meta[name="user-id"]').content)) {
            this.showNotification(`New message from ${message.user_name || 'User'}`);
        }
    }

    /**
     * Create HTML for a message
     */
    createMessageHtml(message) {
        const isFromCustomer = message.user_id === parseInt(this.currentChatRoom);
        const alignClass = isFromCustomer ? 'justify-end' : 'justify-start';
        const bgClass = isFromCustomer ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900';
        const textAlign = isFromCustomer ? 'text-right' : 'text-left';

        return `
            <div class="flex ${alignClass}">
                <div class="max-w-xs lg:max-w-md">
                    <div class="px-4 py-3 rounded-lg ${bgClass}">
                        <p class="text-sm">${this.escapeHtml(message.message)}</p>
                    </div>
                    <div class="mt-1 text-xs text-gray-500 ${textAlign}">
                        <span>${message.user_name || 'Unknown'}</span>
                        <span class="mx-1">•</span>
                        <span>${this.formatTime(message.created_at)}</span>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Setup message form
     */
    setupMessageForm() {
        const form = document.getElementById('messageForm');
        const input = document.getElementById('messageInput');
        
        if (!form || !input) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const message = input.value.trim();
            if (!message) return;

            try {
                const response = await fetch(form.action, {
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
                        input.value = '';
                        // Message will be displayed via polling
                    }
                } else {
                    alert('Failed to send message');
                }
            } catch (error) {
                console.error('Send message error:', error);
                alert('Failed to send message');
            }
        });

        // Enter key support
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                form.dispatchEvent(new Event('submit'));
            }
        });
    }

    /**
     * Setup typing indicator (debounced for polling)
     */
    setupTypingIndicator() {
        const input = document.getElementById('messageInput');
        if (!input) return;

        let typingTimer;

        input.addEventListener('input', () => {
            if (!this.isTyping) {
                this.sendTypingIndicator(true);
                this.isTyping = true;
            }

            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                this.sendTypingIndicator(false);
                this.isTyping = false;
            }, 2000);
        });
    }

    /**
     * Send typing indicator (less frequent for polling)
     */
    async sendTypingIndicator(isTyping) {
        if (!this.currentChatRoom) return;

        try {
            await fetch('/admin/api/chat/typing', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    chat_room_id: this.currentChatRoom,
                    is_typing: isTyping
                })
            });
        } catch (error) {
            console.error('Typing indicator error:', error);
        }
    }

    /**
     * Get last message ID for polling
     */
    async getLastMessageId() {
        const messages = document.querySelectorAll('[data-message-id]');
        if (messages.length > 0) {
            const lastMessage = messages[messages.length - 1];
            this.lastMessageId = parseInt(lastMessage.dataset.messageId) || 0;
        }
    }

    /**
     * Extract chat room ID from URL
     */
    extractChatRoomId() {
        const path = window.location.pathname;
        const matches = path.match(/\/admin\/chat\/(\d+)/);
        return matches ? matches[1] : null;
    }

    /**
     * Auto-scroll to bottom
     */
    scrollToBottom() {
        const container = document.getElementById('messagesContainer');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    /**
     * Show browser notification
     */
    showNotification(message) {
        if (Notification.permission === 'granted') {
            new Notification('Chat Update', {
                body: message,
                icon: '/favicon.ico'
            });
        }
    }

    /**
     * Utility functions
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    formatTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
    }

    /**
     * Cleanup when leaving page
     */
    destroy() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
        }
    }
}

// Initialize chat manager when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Check if we should use WebSocket or Polling
    const useWebSocket = window.location.hostname === 'localhost' || 
                        window.location.hostname === '127.0.0.1';
    
    if (useWebSocket && window.Echo) {
        console.log('🔌 Using WebSocket (Development)');
        // Use the existing WebSocket chat manager
        // window.chatManager = new ChatManager();
    } else {
        console.log('🔄 Using Polling (Production)');
        window.chatManager = new ChatPollingManager();
    }

    // Request notification permission
    if (Notification.permission === 'default') {
        Notification.requestPermission();
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (window.chatManager && window.chatManager.destroy) {
        window.chatManager.destroy();
    }
});
