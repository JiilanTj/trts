<x-app-layout>
    @php($user = auth()->user())
    @php($initials = collect(explode(' ', trim($user->full_name ?: $user->username)))->filter()->take(2)->map(fn($p)=> strtoupper(mb_substr($p,0,1)))->implode(''))
    <div class="min-h-screen bg-[#1a1d21] text-neutral-100">
        <!-- Header Section with User Info -->
        <div class="sticky top-0 z-40 backdrop-blur bg-[#1f2226]/95 border-b border-neutral-800/70">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <!-- Profile Photo with Badge -->
                            <div class="relative">
                                @if($user->photo_url)
                                    <div class="w-12 h-12 rounded-full p-0.5 bg-gradient-to-br from-[#FE2C55] to-[#25F4EE]">
                                        <img src="{{ $user->photo_url }}" alt="Avatar" class="w-full h-full rounded-full object-cover ring-1 ring-black/40" />
                                    </div>
                                @else
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center bg-gradient-to-br from-[#FE2C55] to-[#25F4EE] p-0.5">
                                        <div class="w-full h-full rounded-full bg-black flex items-center justify-center text-sm font-semibold tracking-wide">{{ $initials }}</div>
                                    </div>
                                @endif
                                <!-- Badge on Profile -->
                                <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center bg-[#FE2C55] text-white text-xs font-bold shadow-[0_0_0_2px_#000]">📊</div>
                            </div>
                            <div>
                                @if(auth()->user()->isSeller() && auth()->user()->sellerInfo)
                                    <h1 class="text-xl font-semibold">{{ auth()->user()->full_name }}</h1>
                                    <p class="text-sm font-medium bg-clip-text text-transparent bg-gradient-to-r from-[#FE2C55] to-[#25F4EE]">{{ auth()->user()->sellerInfo->store_name }}</p>
                                @else
                                    <h1 class="text-xl font-semibold">{{ auth()->user()->full_name }}</h1>
                                @endif
                                <!-- Page Title -->
                                <div class="flex items-center space-x-6 mt-2">
                                    <div class="text-left">
                                        <span class="text-sm font-semibold">Analytics Dashboard</span>
                                        <p class="text-[10px] uppercase tracking-wide text-neutral-400 mt-0.5">Evaluasi Performa Bisnis</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-end space-y-1">
                            <!-- Credit Score Badge -->
                            <div class="flex items-center space-x-1 px-2 py-1 rounded-md bg-purple-500/15 text-purple-400 text-xs font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                                <span>{{ $stats['credit_score'] ?? 0 }} Score</span>
                            </div>
                            <!-- Loyalty Badge -->
                            <div class="flex items-center space-x-1 px-2 py-1 rounded-md bg-amber-500/15 text-amber-400 text-xs font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span>{{ $performance['loyalty_score'] ?? 0 }} Loyalty</span>
                            </div>
                            <!-- Level Badge -->
                            <div class="flex items-center space-x-1 px-2 py-1 rounded-md bg-blue-500/15 text-blue-400 text-xs font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <span>Lv {{ auth()->user()->level }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Total Orders -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4 relative overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-blue-500 to-blue-400 absolute top-0 left-0"></div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-neutral-400 mb-1">Total Orders</p>
                            <p class="text-2xl font-bold text-blue-400">{{ $stats['total_orders'] }}</p>
                            @php($growth = $stats['orders_last_month'] > 0 ? (($stats['orders_this_month'] - $stats['orders_last_month']) / $stats['orders_last_month']) * 100 : 0)
                            <p class="text-xs {{ $growth >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                {{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 1) }}% vs last month
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Spent -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4 relative overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-green-500 to-emerald-400 absolute top-0 left-0"></div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-neutral-400 mb-1">Total Spent</p>
                            <p class="text-2xl font-bold text-green-400">Rp {{ number_format($stats['total_spent']) }}</p>
                            @php($spentGrowth = $stats['spent_last_month'] > 0 ? (($stats['spent_this_month'] - $stats['spent_last_month']) / $stats['spent_last_month']) * 100 : 0)
                            <p class="text-xs {{ $spentGrowth >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                {{ $spentGrowth >= 0 ? '+' : '' }}{{ number_format($spentGrowth, 1) }}% vs last month
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Current Balance -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4 relative overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-purple-500 to-pink-400 absolute top-0 left-0"></div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-neutral-400 mb-1">Balance</p>
                            <p class="text-2xl font-bold text-purple-400">Rp {{ number_format($stats['current_balance']) }}</p>
                            <p class="text-xs text-neutral-400">Available funds</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Completion Rate -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4 relative overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-amber-500 to-orange-400 absolute top-0 left-0"></div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-neutral-400 mb-1">Completion Rate</p>
                            <p class="text-2xl font-bold text-amber-400">{{ $performance['completion_rate'] }}%</p>
                            <p class="text-xs text-neutral-400">Order success rate</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-amber-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Orders Over Time Chart -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-6 relative overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-[#fe2c55] via-[#fe2c55]/40 to-[#25f4ee] absolute top-0 left-0"></div>
                    <h3 class="text-lg font-semibold mb-4">Orders Over Time</h3>
                    <div class="h-64">
                        <canvas id="ordersChart"></canvas>
                    </div>
                </div>

                <!-- Order Status Distribution -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-6 relative overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-[#fe2c55] via-[#fe2c55]/40 to-[#25f4ee] absolute top-0 left-0"></div>
                    <h3 class="text-lg font-semibold mb-4">Order Status Distribution</h3>
                    <div class="h-64">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics & Recent Activities -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Performance Metrics -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-6 relative overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-emerald-500 to-green-400 absolute top-0 left-0"></div>
                    <h3 class="text-lg font-semibold mb-4">Performance Metrics</h3>
                    <div class="space-y-4">
                        <!-- Payment Success Rate -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-neutral-300">Payment Success Rate</span>
                                <span class="text-sm font-semibold text-green-400">{{ $performance['payment_success_rate'] }}%</span>
                            </div>
                            <div class="w-full bg-neutral-700 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ $performance['payment_success_rate'] }}%"></div>
                            </div>
                        </div>

                        <!-- Average Order Value -->
                        <div class="flex items-center justify-between p-3 bg-neutral-800/50 rounded-lg">
                            <span class="text-sm text-neutral-300">Avg Order Value</span>
                            <span class="text-sm font-semibold text-blue-400">Rp {{ number_format($performance['avg_order_value']) }}</span>
                        </div>

                        <!-- Total Savings -->
                        <div class="flex items-center justify-between p-3 bg-neutral-800/50 rounded-lg">
                            <span class="text-sm text-neutral-300">Total Savings</span>
                            <span class="text-sm font-semibold text-purple-400">Rp {{ number_format($performance['total_savings']) }}</span>
                        </div>

                        <!-- Loyalty Score -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-neutral-300">Loyalty Score</span>
                                <span class="text-sm font-semibold text-amber-400">{{ $performance['loyalty_score'] }}/1000</span>
                            </div>
                            <div class="w-full bg-neutral-700 rounded-full h-2">
                                <div class="bg-amber-500 h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ ($performance['loyalty_score'] / 1000) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-6 relative overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-indigo-500 to-purple-400 absolute top-0 left-0"></div>
                    <h3 class="text-lg font-semibold mb-4">Recent Activities</h3>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @foreach($recentActivities as $activity)
                            <div class="flex items-start space-x-3 p-3 bg-neutral-800/30 rounded-lg">
                                <div class="w-8 h-8 rounded-full bg-neutral-700 flex items-center justify-center flex-shrink-0">
                                    @if($activity['icon'] === 'shopping-bag')
                                        <svg class="w-4 h-4 {{ $activity['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                    @elseif($activity['icon'] === 'bell')
                                        <svg class="w-4 h-4 {{ $activity['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5z"></path>
                                        </svg>
                                    @elseif($activity['icon'] === 'chat')
                                        <svg class="w-4 h-4 {{ $activity['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4-.8L3 20l1.22-2.44A7.793 7.793 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-neutral-200">{{ $activity['title'] }}</p>
                                    <p class="text-xs text-neutral-400">{{ $activity['description'] }}</p>
                                    <p class="text-xs text-neutral-500">{{ $activity['created_at']->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Top Categories -->
            @if($chartData['top_categories']->count() > 0)
                <div class="mt-6">
                    <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-6 relative overflow-hidden">
                        <div class="h-1 w-full bg-gradient-to-r from-cyan-500 to-blue-400 absolute top-0 left-0"></div>
                        <h3 class="text-lg font-semibold mb-4">Top Categories by Spending</h3>
                        <div class="h-64">
                            <canvas id="categoriesChart"></canvas>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Activity Timeline -->
            <div class="mt-6">
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-6 relative overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-indigo-500 to-purple-400 absolute top-0 left-0"></div>
                    <h3 class="text-lg font-semibold mb-4">Activity Timeline (Last 30 Days)</h3>
                    <div class="h-64">
                        <canvas id="activityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Real-time Updates & Charts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart.js default configuration for dark theme
            Chart.defaults.color = '#9CA3AF';
            Chart.defaults.borderColor = '#374151';
            Chart.defaults.backgroundColor = 'rgba(156, 163, 175, 0.1)';

            // Orders Over Time Chart
            const ordersCtx = document.getElementById('ordersChart').getContext('2d');
            const ordersData = @json($chartData['orders_per_month']);
            
            new Chart(ordersCtx, {
                type: 'line',
                data: {
                    labels: ordersData.map(item => {
                        const date = new Date(item.year, item.month - 1);
                        return date.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
                    }),
                    datasets: [{
                        label: 'Orders',
                        data: ordersData.map(item => item.count),
                        borderColor: '#FE2C55',
                        backgroundColor: 'rgba(254, 44, 85, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#FE2C55',
                        pointBorderColor: '#25F4EE',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                    }, {
                        label: 'Spending (Rp)',
                        data: ordersData.map(item => item.total_spent / 1000), // Divide by 1000 for better scale
                        borderColor: '#25F4EE',
                        backgroundColor: 'rgba(37, 244, 238, 0.1)',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4,
                        pointBackgroundColor: '#25F4EE',
                        pointBorderColor: '#FE2C55',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        yAxisID: 'y1',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: '#D1D5DB',
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(31, 34, 38, 0.9)',
                            titleColor: '#F9FAFB',
                            bodyColor: '#D1D5DB',
                            borderColor: '#FE2C55',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    if (context.datasetIndex === 0) {
                                        return `Orders: ${context.parsed.y}`;
                                    } else {
                                        return `Spending: Rp ${(context.parsed.y * 1000).toLocaleString('id-ID')}`;
                                    }
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(75, 85, 99, 0.3)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#9CA3AF'
                            }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            grid: {
                                color: 'rgba(75, 85, 99, 0.3)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#9CA3AF'
                            },
                            title: {
                                display: true,
                                text: 'Orders',
                                color: '#FE2C55'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false,
                            },
                            ticks: {
                                color: '#9CA3AF',
                                callback: function(value) {
                                    return 'Rp ' + (value * 1000).toLocaleString('id-ID');
                                }
                            },
                            title: {
                                display: true,
                                text: 'Spending (K)',
                                color: '#25F4EE'
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });

            // Order Status Distribution Chart
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            const statusData = @json($chartData['order_status_distribution']);
            
            const statusColors = {
                'pending': '#EAB308',
                'processing': '#3B82F6',
                'shipped': '#8B5CF6',
                'completed': '#10B981',
                'cancelled': '#EF4444'
            };

            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusData.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1)),
                    datasets: [{
                        data: statusData.map(item => item.count),
                        backgroundColor: statusData.map(item => statusColors[item.status] || '#6B7280'),
                        borderColor: '#1F2226',
                        borderWidth: 3,
                        hoverBorderWidth: 5,
                        hoverBorderColor: '#FE2C55'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                color: '#D1D5DB',
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(31, 34, 38, 0.9)',
                            titleColor: '#F9FAFB',
                            bodyColor: '#D1D5DB',
                            borderColor: '#FE2C55',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return `${context.label}: ${context.parsed} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '60%',
                    animation: {
                        animateRotate: true,
                        animateScale: true
                    }
                }
            });

            // Top Categories Chart
            @if($chartData['top_categories']->count() > 0)
            const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
            const categoriesData = @json($chartData['top_categories']);
            
            new Chart(categoriesCtx, {
                type: 'bar',
                data: {
                    labels: categoriesData.map(item => item.name),
                    datasets: [{
                        label: 'Total Spending (Rp)',
                        data: categoriesData.map(item => item.total_spent),
                        backgroundColor: [
                            'rgba(254, 44, 85, 0.8)',
                            'rgba(37, 244, 238, 0.8)',
                            'rgba(139, 92, 246, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(251, 191, 36, 0.8)'
                        ],
                        borderColor: [
                            '#FE2C55',
                            '#25F4EE',
                            '#8B5CF6',
                            '#10B981',
                            '#FBBF24'
                        ],
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(31, 34, 38, 0.9)',
                            titleColor: '#F9FAFB',
                            bodyColor: '#D1D5DB',
                            borderColor: '#FE2C55',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    return `Total: Rp ${context.parsed.y.toLocaleString('id-ID')}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(75, 85, 99, 0.3)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#9CA3AF',
                                maxRotation: 45,
                                minRotation: 0
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(75, 85, 99, 0.3)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#9CA3AF',
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutQuart'
                    }
                }
            });
            @endif

            // Activity Timeline Chart
            const activityCtx = document.getElementById('activityChart').getContext('2d');
            const activityData = @json($chartData['activity_data']);
            
            new Chart(activityCtx, {
                type: 'line',
                data: {
                    labels: activityData.map(item => {
                        const date = new Date(item.date);
                        return date.toLocaleDateString('id-ID', { month: 'short', day: 'numeric' });
                    }),
                    datasets: [{
                        label: 'Orders',
                        data: activityData.map(item => item.orders),
                        borderColor: '#FE2C55',
                        backgroundColor: 'rgba(254, 44, 85, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#FE2C55',
                        pointBorderColor: '#25F4EE',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 8,
                    }, {
                        label: 'Notifications',
                        data: activityData.map(item => item.notifications),
                        borderColor: '#25F4EE',
                        backgroundColor: 'rgba(37, 244, 238, 0.1)',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4,
                        pointBackgroundColor: '#25F4EE',
                        pointBorderColor: '#FE2C55',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: '#D1D5DB',
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(31, 34, 38, 0.9)',
                            titleColor: '#F9FAFB',
                            bodyColor: '#D1D5DB',
                            borderColor: '#FE2C55',
                            borderWidth: 1,
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(75, 85, 99, 0.3)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#9CA3AF',
                                maxTicksLimit: 10
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(75, 85, 99, 0.3)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#9CA3AF',
                                beginAtZero: true
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutQuart'
                    }
                }
            });

            // Auto-refresh stats every 30 seconds
            setInterval(function() {
                fetch('/analytics/stats')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Stats updated:', data.data);
                            // Update DOM elements with new data if needed
                        }
                    })
                    .catch(error => console.error('Error updating stats:', error));
            }, 30000);

            // Add loading states for charts
            const charts = document.querySelectorAll('canvas');
            charts.forEach(chart => {
                chart.style.opacity = '0';
                setTimeout(() => {
                    chart.style.opacity = '1';
                    chart.style.transition = 'opacity 0.5s ease-in-out';
                }, 100);
            });
        });
    </script>
</x-app-layout>
