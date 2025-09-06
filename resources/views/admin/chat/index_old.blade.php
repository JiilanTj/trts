<x-admin-layout>
    <x-slot name="title">Chat Support</x-slot>

    <!-- Statistics Cards -->
    <div class="mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Rooms -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_rooms'] ?? 0 }}</p>
                        <p class="text-gray-600 text-sm">Total Rooms</p>
                    </div>
                </div>
            </div>

            <!-- Open Rooms -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['open_rooms'] ?? 0 }}</p>
                        <p class="text-gray-600 text-sm">Open Rooms</p>
                    </div>
                </div>
            </div>

            <!-- Assigned to Me -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['my_rooms'] ?? 0 }}</p>
                        <p class="text-gray-600 text-sm">Assigned to Me</p>
                    </div>
                </div>
            </div>

            <!-- Avg Response Time -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Chat Rooms List -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">Chat Rooms</h2>
                            <div class="flex items-center space-x-2">
                                <!-- Filter Buttons -->
                                <select id="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Status</option>
                                    <option value="open">Open</option>
                                    <option value="assigned">Assigned</option>
                                    <option value="closed">Closed</option>
                                </select>
                                <select id="priorityFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Priority</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="divide-y divide-gray-200" id="chatRoomsList">
                        @forelse($chatRooms as $room)
                            <div class="p-6 hover:bg-gray-50 transition-colors duration-200 cursor-pointer chat-room-item" 
                                 data-room-id="{{ $room->id }}" 
                                 data-status="{{ $room->status }}" 
                                 data-priority="{{ $room->priority }}">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <!-- User Avatar -->
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-sm">
                                                <span class="text-sm font-bold text-white">
                                                    {{ strtoupper(substr($room->user->full_name, 0, 2)) }}
                                                </span>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-gray-900">{{ $room->user->full_name }}</h3>
                                                <p class="text-sm text-gray-600">{{ $room->subject ?? 'Chat Support' }}</p>
                                            </div>
                                        </div>
                                        
                                        <!-- Last Message Preview -->
                                        @if($room->lastMessage)
                                            <p class="text-sm text-gray-600 mb-2 line-clamp-2">
                                                {{ Str::limit($room->lastMessage->message, 100) }}
                                            </p>
                                        @endif

                                        <!-- Room Meta -->
                                        <div class="flex items-center space-x-4 text-xs text-gray-500">
                                            <span>{{ $room->created_at->diffForHumans() }}</span>
                                            @if($room->assigned_admin_id)
                                                <span class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path>
                                                    </svg>
                                                    {{ $room->assignedAdmin->full_name ?? 'Assigned' }}
                                                </span>
                                            @endif
                                            <span>{{ $room->messages_count ?? 0 }} pesan</span>
                                        </div>
                                    </div>

                                    <!-- Status & Priority Badges -->
                                    <div class="flex flex-col items-end space-y-2">
                                        <!-- Status Badge -->
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @if($room->status === 'open') bg-orange-100 text-orange-800
                                            @elseif($room->status === 'assigned') bg-blue-100 text-blue-800  
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($room->status) }}
                                        </span>

                                        <!-- Priority Badge -->
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @if($room->priority === 'urgent') bg-red-100 text-red-800
                                            @elseif($room->priority === 'high') bg-orange-100 text-orange-800
                                            @elseif($room->priority === 'medium') bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800
                                            @endif">
                                            {{ ucfirst($room->priority) }}
                                        </span>

                                        <!-- Unread Count -->
                                        @if($room->unread_count > 0)
                                            <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full min-w-[20px] text-center">
                                                {{ $room->unread_count }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada chat room</h3>
                                <p class="text-gray-600">Chat room akan muncul ketika pelanggan memulai percakapan.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($chatRooms->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $chatRooms->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.chat.statistics') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <svg class="w-5 h-5 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">View Statistics</span>
                        </a>
                        
                        <button onclick="refreshRooms()" class="w-full flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <svg class="w-5 h-5 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Refresh Rooms</span>
                        </button>
                    </div>
                </div>

                <!-- Online Admins -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Admin Online</h3>
                    <div class="space-y-3" id="onlineAdmins">
                        <!-- This will be populated via JavaScript -->
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center">
                                <span class="text-xs font-bold text-white">{{ strtoupper(substr(auth()->user()->full_name, 0, 2)) }}</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->full_name }}</p>
                                <p class="text-xs text-green-600">Online (You)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                    <div class="space-y-3" id="recentActivity">
                        @forelse($recentMessages as $message)
                            <div class="text-sm">
                                <p class="text-gray-900 font-medium">
                                    {{ $message->user->full_name }}
                                </p>
                                <p class="text-gray-600 line-clamp-2">
                                    {{ Str::limit($message->message, 60) }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $message->created_at->diffForHumans() }}
                                </p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Belum ada aktivitas.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 shadow-xl">
        <div class="flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700">Loading...</span>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const statusFilter = document.getElementById('statusFilter');
    const priorityFilter = document.getElementById('priorityFilter');
    const chatRoomItems = document.querySelectorAll('.chat-room-item');

    function filterRooms() {
        const statusValue = statusFilter.value;
        const priorityValue = priorityFilter.value;

        chatRoomItems.forEach(item => {
            const itemStatus = item.dataset.status;
            const itemPriority = item.dataset.priority;
            
            const statusMatch = !statusValue || itemStatus === statusValue;
            const priorityMatch = !priorityValue || itemPriority === priorityValue;
            
            if (statusMatch && priorityMatch) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    statusFilter.addEventListener('change', filterRooms);
    priorityFilter.addEventListener('change', filterRooms);

    // Room click handler
    chatRoomItems.forEach(item => {
        item.addEventListener('click', function() {
            const roomId = this.dataset.roomId;
            window.location.href = `/admin/chat/rooms/${roomId}`;
        });
    });

    // Auto-refresh functionality (every 30 seconds)
    setInterval(refreshRooms, 30000);
});

function refreshRooms() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    loadingOverlay.classList.remove('hidden');
    
    // Refresh the page to get latest data
    setTimeout(() => {
        window.location.reload();
    }, 500);
}

// Real-time updates with Laravel Reverb (to be implemented)
function initializeRealtimeUpdates() {
    // This will be implemented when we add WebSocket functionality
    console.log('Initializing real-time updates...');
}

// Call this when page loads
// initializeRealtimeUpdates();
</script>
</x-admin-layout>
