/**
 * Chat Real-time Functionality
 * Handles WebSocket connections for real-time chat messaging
 */

class ChatManager {
    constructor() {
        this.currentChatRoom = null;
        this.typingTimer = null;
        this.typingIndicators = new Map();
        this.isTyping = false;
        
        this.initializePage();
    }

    /**
     * Initialize based on current page
     */
    initializePage() {
        const path = window.location.pathname;
        
        if (path.includes('/admin/chat/')) {
            if (path.includes('/admin/chat/') && path !== '/admin/chat/') {
                // Individual chat room page
                this.initializeChatRoom();
            } else {
                // Chat dashboard
                this.initializeDashboard();
            }
        }
    }

    /**
     * Initialize dashboard with real-time updates
     */
    initializeDashboard() {
        console.log('Initializing chat dashboard...');
        
        // Listen for new chat rooms
        window.Echo.private('admin-notifications')
            .listen('ChatRoomCreated', (e) => {
                this.handleNewChatRoom(e.chatRoom);
            })
            .listen('ChatRoomAssigned', (e) => {
                this.handleChatRoomAssigned(e.chatRoom);
            });

        // Auto-refresh statistics
        setInterval(() => {
            this.refreshDashboardStats();
        }, 30000); // Every 30 seconds
    }

    /**
     * Initialize individual chat room
     */
    initializeChatRoom() {
        const chatRoomId = this.extractChatRoomId();
        if (!chatRoomId) return;

        this.currentChatRoom = chatRoomId;
        console.log(`Initializing chat room ${chatRoomId}...`);

        // Join chat room channel
        const channel = window.Echo.private(`chat-room.${chatRoomId}`);
        
        // Listen for new messages
        channel.listen('message.new', (e) => {
            this.handleNewMessage(e.message);
        });

        // Listen for typing indicators
        channel.listen('user.typing', (e) => {
            this.handleTypingIndicator(e);
        });

        // Listen for chat room status changes
        channel.listen('chat.status-changed', (e) => {
            this.handleStatusChange(e);
        });

        // Set up message form
        this.setupMessageForm();
        
        // Set up typing indicator
        this.setupTypingIndicator();

        // Auto-scroll to bottom
        this.scrollToBottom();
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
     * Handle new message received
     */
    handleNewMessage(message) {
        console.log('New message received:', message);
        
        // Add message to chat
        this.addMessageToChat(message);
        
        // Update last activity
        this.updateLastActivity();
        
        // Play notification sound (optional)
        this.playNotificationSound();
        
        // Show browser notification if page is not visible
        if (document.hidden) {
            this.showBrowserNotification(message);
        }
    }

    /**
     * Add message to chat interface
     */
    addMessageToChat(message) {
        const messagesContainer = document.getElementById('messagesContainer');
        if (!messagesContainer) return;

        const isCustomer = message.user_id == this.currentChatRoom;
        const messageHtml = `
            <div class="flex ${isCustomer ? 'justify-end' : 'justify-start'}">
                <div class="max-w-xs lg:max-w-md">
                    <div class="px-4 py-3 rounded-lg ${isCustomer ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900'}">
                        <p class="text-sm">${this.escapeHtml(message.message)}</p>
                    </div>
                    <div class="mt-1 text-xs text-gray-500 ${isCustomer ? 'text-right' : 'text-left'}">
                        <span>${message.user_name || 'Unknown'}</span>
                        <span class="mx-1">•</span>
                        <span>${this.formatTime(message.created_at)}</span>
                    </div>
                </div>
            </div>
        `;
        
        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
        this.scrollToBottom();
    }

    /**
     * Handle typing indicator
     */
    handleTypingIndicator(data) {
        const { user, isTyping } = data;
        
        if (isTyping) {
            this.showTypingIndicator(user);
        } else {
            this.hideTypingIndicator(user.id);
        }
    }

    /**
     * Show typing indicator
     */
    showTypingIndicator(user) {
        this.typingIndicators.set(user.id, user);
        this.updateTypingDisplay();
    }

    /**
     * Hide typing indicator
     */
    hideTypingIndicator(userId) {
        this.typingIndicators.delete(userId);
        this.updateTypingDisplay();
    }

    /**
     * Update typing indicator display
     */
    updateTypingDisplay() {
        const typingContainer = document.getElementById('typingIndicator');
        if (!typingContainer) return;

        if (this.typingIndicators.size === 0) {
            typingContainer.style.display = 'none';
            return;
        }

        const users = Array.from(this.typingIndicators.values());
        const names = users.map(u => u.name).join(', ');
        const verb = users.length > 1 ? 'are' : 'is';
        
        typingContainer.innerHTML = `
            <div class="flex justify-start">
                <div class="max-w-xs lg:max-w-md">
                    <div class="px-4 py-3 rounded-lg bg-gray-50 text-gray-600">
                        <p class="text-sm italic">${names} ${verb} typing...</p>
                    </div>
                </div>
            </div>
        `;
        typingContainer.style.display = 'block';
        this.scrollToBottom();
    }

    /**
     * Setup message form
     */
    setupMessageForm() {
        const form = document.getElementById('messageForm');
        const input = document.getElementById('messageInput');
        
        if (!form || !input) return;

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.sendMessage();
        });

        // Handle Enter key
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                form.dispatchEvent(new Event('submit'));
            }
        });
    }

    /**
     * Setup typing indicator
     */
    setupTypingIndicator() {
        const input = document.getElementById('messageInput');
        if (!input) return;

        input.addEventListener('input', () => {
            this.handleTyping();
        });

        input.addEventListener('blur', () => {
            this.stopTyping();
        });
    }

    /**
     * Handle typing event
     */
    handleTyping() {
        if (!this.isTyping) {
            this.isTyping = true;
            this.sendTypingIndicator(true);
        }

        // Clear existing timer
        clearTimeout(this.typingTimer);

        // Set timer to stop typing after 3 seconds
        this.typingTimer = setTimeout(() => {
            this.stopTyping();
        }, 3000);
    }

    /**
     * Stop typing
     */
    stopTyping() {
        if (this.isTyping) {
            this.isTyping = false;
            this.sendTypingIndicator(false);
        }
        clearTimeout(this.typingTimer);
    }

    /**
     * Send typing indicator
     */
    sendTypingIndicator(isTyping) {
        if (!this.currentChatRoom) return;

        window.axios.post('/admin/api/chat/typing', {
            chat_room_id: this.currentChatRoom,
            is_typing: isTyping
        }).catch(error => {
            console.error('Failed to send typing indicator:', error);
        });
    }

    /**
     * Send message
     */
    async sendMessage() {
        const input = document.getElementById('messageInput');
        const form = document.getElementById('messageForm');
        
        if (!input || !form || !input.value.trim()) return;

        const message = input.value.trim();
        const submitBtn = form.querySelector('button[type="submit"]');
        
        // Disable form
        submitBtn.disabled = true;
        input.disabled = true;

        try {
            const response = await window.axios.post(form.action, {
                message: message
            });

            if (response.data.success) {
                input.value = '';
                // Note: Message will be added via WebSocket event
            } else {
                this.showError('Failed to send message');
            }
        } catch (error) {
            console.error('Send message error:', error);
            this.showError('Failed to send message');
        } finally {
            // Re-enable form
            submitBtn.disabled = false;
            input.disabled = false;
            input.focus();
        }
    }

    /**
     * Handle new chat room (dashboard)
     */
    handleNewChatRoom(chatRoom) {
        console.log('New chat room created:', chatRoom);
        
        // Add to dashboard list
        this.addChatRoomToList(chatRoom);
        
        // Show notification
        this.showNotification(`New chat room from ${chatRoom.user.name}`);
        
        // Update statistics
        this.refreshDashboardStats();
    }

    /**
     * Handle chat room assignment
     */
    handleChatRoomAssigned(chatRoom) {
        console.log('Chat room assigned:', chatRoom);
        
        // Update room status in list
        this.updateChatRoomStatus(chatRoom);
        
        // Show notification if assigned to current user
        const currentUser = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
        if (chatRoom.assigned_admin_id == currentUser) {
            this.showNotification(`Chat room assigned to you: ${chatRoom.subject}`);
        }
    }

    /**
     * Refresh dashboard statistics
     */
    async refreshDashboardStats() {
        try {
            const response = await window.axios.get('/admin/api/chat/statistics');
            const stats = response.data;
            
            // Update statistics cards
            this.updateStatisticsCards(stats);
        } catch (error) {
            console.error('Failed to refresh stats:', error);
        }
    }

    /**
     * Update statistics cards
     */
    updateStatisticsCards(stats) {
        const elements = {
            'open_chats': document.querySelector('[data-stat="open_chats"]'),
            'assigned_chats': document.querySelector('[data-stat="assigned_chats"]'),
            'my_chats': document.querySelector('[data-stat="my_chats"]'),
            'closed_today': document.querySelector('[data-stat="closed_today"]')
        };

        Object.entries(elements).forEach(([key, element]) => {
            if (element && stats[key] !== undefined) {
                element.textContent = stats[key];
            }
        });
    }

    /**
     * Utility methods
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

    scrollToBottom() {
        const container = document.getElementById('messagesContainer');
        if (container) {
            setTimeout(() => {
                container.scrollTop = container.scrollHeight;
            }, 100);
        }
    }

    showError(message) {
        // You can implement a toast notification system here
        alert(message);
    }

    showNotification(message) {
        // You can implement a toast notification system here
        console.log('Notification:', message);
    }

    showBrowserNotification(message) {
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('New Chat Message', {
                body: message.message,
                icon: '/favicon.ico'
            });
        }
    }

    playNotificationSound() {
        // You can add a sound notification here
        // const audio = new Audio('/sounds/notification.mp3');
        // audio.play().catch(e => console.log('Could not play sound'));
    }

    updateLastActivity() {
        const elements = document.querySelectorAll('[data-last-activity]');
        elements.forEach(el => {
            el.textContent = 'Just now';
        });
    }

    addChatRoomToList(chatRoom) {
        // Implementation depends on your table structure
        console.log('Adding chat room to list:', chatRoom);
    }

    updateChatRoomStatus(chatRoom) {
        // Implementation depends on your table structure
        console.log('Updating chat room status:', chatRoom);
    }

    handleStatusChange(data) {
        console.log('Chat status changed:', data);
        
        // Update UI based on status change
        const statusElement = document.querySelector('.chat-status');
        if (statusElement) {
            statusElement.textContent = data.status;
            statusElement.className = `chat-status status-${data.status}`;
        }
    }
}

// Initialize chat manager when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Request notification permission
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
    
    // Initialize chat manager
    window.chatManager = new ChatManager();
});

// Export for global access
window.ChatManager = ChatManager;
