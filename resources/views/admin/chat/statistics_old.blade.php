@extends('layouts.admin')

@section('title', 'Chat Statistics')

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
                        <span class="font-medium">Back to Chat</span>
                    </a>
                    
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Chat Statistics</h1>
                        <p class="text-gray-600 mt-1">Analisis performa customer service</p>
                    </div>
                </div>

                <!-- Date Range Selector -->
                <div class="flex items-center space-x-4">
                    <select id="dateRange" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="today">Today</option>
                        <option value="week" selected>This Week</option>
                        <option value="month">This Month</option>
                        <option value="quarter">This Quarter</option>
                        <option value="year">This Year</option>
                    </select>
                    
                    <button onclick="exportReport()" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Content -->
    <div class="px-6 py-6">
        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Conversations -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_conversations'] ?? 0 }}</p>
                        <p class="text-gray-600 text-sm">Total Conversations</p>
                        <p class="text-xs {{ ($statistics['conversations_change'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ ($statistics['conversations_change'] ?? 0) >= 0 ? '+' : '' }}{{ $statistics['conversations_change'] ?? 0 }}% from last period
                        </p>
                    </div>
                </div>
            </div>

            <!-- Avg Response Time -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['avg_response_time'] ?? '0m' }}</p>
                        <p class="text-gray-600 text-sm">Avg Response Time</p>
                        <p class="text-xs {{ ($statistics['response_time_change'] ?? 0) <= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ ($statistics['response_time_change'] ?? 0) >= 0 ? '+' : '' }}{{ $statistics['response_time_change'] ?? 0 }}% from last period
                        </p>
                    </div>
                </div>
            </div>

            <!-- Resolution Rate -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['resolution_rate'] ?? '0' }}%</p>
                        <p class="text-gray-600 text-sm">Resolution Rate</p>
                        <p class="text-xs {{ ($statistics['resolution_rate_change'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ ($statistics['resolution_rate_change'] ?? 0) >= 0 ? '+' : '' }}{{ $statistics['resolution_rate_change'] ?? 0 }}% from last period
                        </p>
                    </div>
                </div>
            </div>

            <!-- Customer Satisfaction -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['customer_satisfaction'] ?? '0' }}/5</p>
                        <p class="text-gray-600 text-sm">Customer Satisfaction</p>
                        <p class="text-xs {{ ($statistics['satisfaction_change'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ ($statistics['satisfaction_change'] ?? 0) >= 0 ? '+' : '' }}{{ $statistics['satisfaction_change'] ?? 0 }} from last period
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Detailed Statistics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Messages Over Time Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Messages Over Time</h3>
                <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                    <div class="text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="text-gray-500">Chart will be implemented with Chart.js</p>
                    </div>
                </div>
            </div>

            <!-- Response Time Distribution -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Response Time Distribution</h3>
                <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                    <div class="text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="text-gray-500">Chart will be implemented with Chart.js</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Admin Performance -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Admin Performance</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($adminStats ?? [] as $admin)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-sm">
                                        <span class="text-sm font-bold text-white">
                                            {{ strtoupper(substr($admin['name'], 0, 2)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $admin['name'] }}</h4>
                                        <p class="text-sm text-gray-600">{{ $admin['conversations'] ?? 0 }} conversations</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ $admin['avg_response_time'] ?? '0m' }}</p>
                                    <p class="text-xs text-gray-500">Avg Response</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <p class="text-gray-500">No admin statistics available</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($recentActivity ?? [] as $activity)
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900">{{ $activity['description'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <p class="text-gray-500">No recent activity</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Priority Distribution -->
        <div class="mt-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Priority Distribution</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <div class="text-2xl font-bold text-red-600">{{ $statistics['urgent_count'] ?? 0 }}</div>
                        <div class="text-sm text-red-800">Urgent</div>
                    </div>
                    <div class="text-center p-4 bg-orange-50 rounded-lg">
                        <div class="text-2xl font-bold text-orange-600">{{ $statistics['high_count'] ?? 0 }}</div>
                        <div class="text-sm text-orange-800">High</div>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-600">{{ $statistics['medium_count'] ?? 0 }}</div>
                        <div class="text-sm text-yellow-800">Medium</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ $statistics['low_count'] ?? 0 }}</div>
                        <div class="text-sm text-green-800">Low</div>
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
    const dateRangeSelect = document.getElementById('dateRange');
    
    dateRangeSelect.addEventListener('change', function() {
        const selectedRange = this.value;
        // Reload page with new date range
        const url = new URL(window.location);
        url.searchParams.set('range', selectedRange);
        window.location.href = url.toString();
    });
    
    // Set selected value from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const currentRange = urlParams.get('range');
    if (currentRange) {
        dateRangeSelect.value = currentRange;
    }
});

function exportReport() {
    const dateRange = document.getElementById('dateRange').value;
    
    // Show loading
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = `
        <svg class="animate-spin w-4 h-4 mr-2 inline" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Exporting...
    `;
    button.disabled = true;
    
    // Create download link (will be implemented later)
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
        alert('Export functionality will be implemented soon.');
    }, 2000);
}

// Initialize charts (placeholder for Chart.js implementation)
function initializeCharts() {
    console.log('Charts will be implemented with Chart.js');
}

// Call when page loads
// initializeCharts();
</script>
@endsection
