/**
 * Chat Polling System - Pure Polling for Production
 * Works on shared hosting like AAPanel without WebSocket
 */

class ChatManager {
    constructor() {
        this.currentChatRoom = null;
        this.pollingInterval = null;
        this.lastMessageId = 0;
        this.isTyping = false;
        this.typingTimer = null;
        
        this.initializePage();
    }

    /**
     * Initialize based on current page
     */
    initializePage() {
        const path = window.location.pathname;
        console.log('🔄 Initializing Chat Polling System...');
        
        if (path.includes('/admin/chat/')) {
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
        console.log('🔄 Initializing admin chat dashboard with polling...');
        
        // Poll for dashboard updates every 10 seconds
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
        console.log(`🔄 Initializing admin chat room ${chatRoomId} with polling...`);

        // Get initial last message ID
        this.getLastMessageId();

        // Poll for new messages every 3 seconds
        this.pollingInterval = setInterval(() => {
            this.pollNewMessages();
        }, 3000);

        // Don't setup form listeners - let the view handle it
        console.log('✅ Chat room polling initialized');
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
     * Handle new message display
     */
    handleNewMessage(message) {
        const messagesContainer = document.getElementById('messagesContainer');
        if (!messagesContainer) return;

        // Check if message already exists
        if (document.querySelector(`[data-message-id="${message.id}"]`)) {
            return;
        }

        const messageHtml = this.createMessageHtml(message);
        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
        this.scrollToBottom();

        // Show notification if not from current user
        const currentUserId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
        if (message.user_id !== parseInt(currentUserId)) {
            this.showNotification(`New message from ${message.user_name || 'User'}`);
        }
    }

    /**
     * Create HTML for a message
     */
    createMessageHtml(message) {
        const currentUserId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
        const isFromCurrentUser = message.user_id === parseInt(currentUserId);
        const alignClass = isFromCurrentUser ? 'justify-end' : 'justify-start';
        const bgClass = isFromCurrentUser ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900';
        const textAlign = isFromCurrentUser ? 'text-right' : 'text-left';

        return `
            <div class="flex ${alignClass}" data-message-id="${message.id}">
                <div class="max-w-xs lg:max-w-md">
                    <div class="px-4 py-3 rounded-lg ${bgClass}">
                        <p class="text-sm">${this.escapeHtml(message.message)}</p>
                    </div>
                    <div class="mt-1 text-xs text-gray-500 ${textAlign}">
                        <span>${this.escapeHtml(message.user_name || 'Unknown')}</span>
                        <span class="mx-1">•</span>
                        <span>${this.formatTime(message.created_at)}</span>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Update dashboard with new data
     */
    updateDashboard(data) {
        // Update chat rooms table
        if (data.chat_rooms) {
            console.log('Dashboard updated:', data);
            // Implementation depends on your table structure
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
     * Get last message ID for polling
     */
    getLastMessageId() {
        const messages = document.querySelectorAll('[data-message-id]');
        if (messages.length > 0) {
            const lastMessage = messages[messages.length - 1];
            this.lastMessageId = parseInt(lastMessage.dataset.messageId) || 0;
        }
    }

    /**
     * Auto-scroll to bottom
     */
    scrollToBottom() {
        const container = document.getElementById('messagesContainer');
        if (container) {
            setTimeout(() => {
                container.scrollTop = container.scrollHeight;
            }, 100);
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
        div.textContent = text || '';
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
            this.pollingInterval = null;
        }
        if (this.typingTimer) {
            clearTimeout(this.typingTimer);
            this.typingTimer = null;
        }
    }
}

// Initialize chat manager when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    console.log('🔄 Starting Chat Polling System...');
    window.chatManager = new ChatManager();

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

// Export for global access
window.ChatManager = ChatManager;
