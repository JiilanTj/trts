<x-admin-layout>
    <x-slot name="title">Chat Support</x-slot>

    <!-- Meta tags for JavaScript -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!-- Total Rooms -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900" data-stat="total_rooms">{{ $statistics['total_rooms'] ?? 0 }}</p>
                    <p class="text-gray-600 text-sm">Total Rooms</p>
                </div>
            </div>
        </div>

        <!-- Open Rooms -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900" data-stat="open_rooms">{{ $statistics['open_rooms'] ?? 0 }}</p>
                    <p class="text-gray-600 text-sm">Open Rooms</p>
                </div>
            </div>
        </div>

        <!-- Assigned Rooms -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900" data-stat="assigned_rooms">{{ $statistics['assigned_rooms'] ?? 0 }}</p>
                    <p class="text-gray-600 text-sm">Assigned Rooms</p>
                </div>
            </div>
        </div>

        <!-- Avg Response Time -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['avg_response_time'] ?? '0m' }}</p>
                    <p class="text-gray-600 text-sm">Avg Response</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Rooms Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Chat Rooms</h2>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.chat.statistics') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Statistics
                    </a>
                    <div class="bg-green-100 text-green-800 px-3 py-2 rounded-lg text-sm font-medium">
                        <span class="w-2 h-2 bg-green-400 rounded-full inline-block mr-2"></span>
                        Online
                    </div>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="mt-4">
                <form method="GET" action="{{ route('admin.chat.index') }}" class="flex gap-3">
                    <div class="flex-1">
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Status</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <select name="priority" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Priority</option>
                            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    <button type="submit" 
                            class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                        Filter
                    </button>
                    @if(request('status') || request('priority'))
                        <a href="{{ route('admin.chat.index') }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Messages</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Activity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($chatRooms as $room)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ strtoupper(substr($room->user->full_name ?? $room->user->username ?? 'U', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $room->user->full_name ?? $room->user->username ?? 'Unknown' }}</div>
                                        <div class="text-sm text-gray-500">{{ $room->user->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ Str::limit($room->subject, 40) }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    Room #{{ $room->id }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'open' => 'bg-orange-100 text-orange-800',
                                        'assigned' => 'bg-blue-100 text-blue-800',
                                        'closed' => 'bg-gray-100 text-gray-800',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$room->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($room->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $priorityClasses = [
                                        'urgent' => 'bg-red-100 text-red-800',
                                        'high' => 'bg-orange-100 text-orange-800',
                                        'medium' => 'bg-yellow-100 text-yellow-800',
                                        'low' => 'bg-green-100 text-green-800',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityClasses[$room->priority] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($room->priority) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($room->admin)
                                    <div class="text-sm font-medium text-gray-900">{{ $room->admin->full_name ?? $room->admin->username }}</div>
                                @else
                                    <span class="text-sm text-gray-500">Not assigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $room->messages_count ?? 0 }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $room->updated_at ? $room->updated_at->diffForHumans() : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('admin.chat.show', $room) }}" 
                                   class="text-blue-600 hover:text-blue-900">
                                    View
                                </a>
                                @if($room->status === 'open')
                                    <form method="POST" action="{{ route('admin.chat.assign', $room) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900">
                                            Assign
                                        </button>
                                    </form>
                                @endif
                                @if($room->status !== 'closed')
                                    <form method="POST" action="{{ route('admin.chat.close', $room) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            Close
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                No chat rooms found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(isset($chatRooms) && $chatRooms->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $chatRooms->links() }}
            </div>
        @endif
    </div>

    <!-- JavaScript for real-time updates -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if we're in development (localhost) or production
        const isLocalhost = window.location.hostname === 'localhost' || 
                           window.location.hostname === '127.0.0.1';
        
        if (isLocalhost && window.Echo) {
            console.log('🔌 Using WebSocket (Development)');
            initializeWebSocketUpdates();
        } else {
            console.log('🔄 Using Polling (Production)');
            initializePollingUpdates();
        }

        // Form handling
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Processing...';
                }
            });
        });
    });

    // WebSocket implementation (for development)
    function initializeWebSocketUpdates() {
        // Listen for new chat rooms
        window.Echo.private('admin-notifications')
            .listen('NewChatMessage', (e) => {
                updateDashboardStats();
                showNotification('New chat message received');
            })
            .listen('ChatRoomAssigned', (e) => {
                updateDashboardStats();
            });

        // Auto-refresh statistics every 30 seconds
        setInterval(updateDashboardStats, 30000);
    }

    // Polling implementation (for production)
    function initializePollingUpdates() {
        // Poll for dashboard updates every 15 seconds
        setInterval(async () => {
            try {
                const response = await fetch('/admin/api/chat/dashboard-updates', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    updateStatisticsDisplay(data.statistics);
                }
            } catch (error) {
                console.error('Dashboard polling error:', error);
            }
        }, 15000);

        // Poll for statistics every 30 seconds
        setInterval(updateDashboardStats, 30000);
    }

    // Update statistics display
    function updateStatisticsDisplay(stats) {
        Object.keys(stats).forEach(key => {
            const element = document.querySelector(`[data-stat="${key}"]`);
            if (element && stats[key] !== undefined) {
                element.textContent = stats[key];
            }
        });
    }

    // Update dashboard statistics
    async function updateDashboardStats() {
        try {
            const response = await fetch('/admin/api/chat/statistics', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                updateStatisticsDisplay(data);
            }
        } catch (error) {
            console.error('Statistics update error:', error);
        }
    }

    // Show notification
    function showNotification(message) {
        if (Notification.permission === 'granted') {
            new Notification('Chat Update', {
                body: message,
                icon: '/favicon.ico'
            });
        }
    }

    // Request notification permission
    if (Notification.permission === 'default') {
        Notification.requestPermission();
    }
    </script>
</x-admin-layout>
