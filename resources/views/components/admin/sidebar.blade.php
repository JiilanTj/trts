<!-- Sidebar -->
<div class="w-64 h-screen bg-white text-gray-800 flex flex-col shadow-lg" style="border-right: 1px solid #e5e7eb;">
    <!-- Logo -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center space-x-2">
            @if($globalSetting?->logo_url)
                <img src="{{ $globalSetting->logo_url }}" alt="Logo" class="h-8 w-8 object-cover rounded-lg shadow-lg border" />
            @else
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                    </svg>
                </div>
            @endif
            <div>
                <h1 class="text-lg font-bold text-gray-800">{{ $globalSetting?->site_name ?? config('app.name', 'Laravel') }}</h1>
                <p class="text-xs text-gray-500">Panel Admin</p>
            </div>
        </div>
    </div>

    <!-- Admin Profile Section -->
    <div class="p-4 border-b border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg">
                <span class="text-sm font-bold text-white">{{ strtoupper(substr(auth()->user()->full_name, 0, 2)) }}</span>
            </div>
            <div class="flex-1">
                <p class="font-medium text-gray-800 text-sm">{{ auth()->user()->full_name }}</p>
                <p class="text-xs text-gray-500">Administrator</p>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 p-4 overflow-y-auto">
        <div class="space-y-1">
            <div class="mb-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Utama</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                            </svg>
                            <span class="font-medium">Dasbor</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span class="font-medium">Analitik</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="mb-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Manajemen</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.products.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.products.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <span class="font-medium">Produk</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.categories.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span class="font-medium">Kategori</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a3 3 0 11-6 0 3 3 0 016 0zM9 10a3 3 0 11-6 0 3 3 0 06 0z" />
                            </svg>
                            <span class="font-medium">Pengguna</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.settings.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <span class="font-medium">Pengaturan Situs</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.orders.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.orders.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                            </svg>
                            <span class="font-medium">Orders</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.topup.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.topup.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span class="font-medium">Topup Saldo</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.withdrawals.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.withdrawals.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                            </svg>
                            <span class="font-medium">Penarikan Saldo</span>
                            <span id="withdrawal-pending-count" class="bg-orange-500 text-white text-xs rounded-full px-2 py-1 hidden"></span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.loan-requests.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.loan-requests.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                            <span class="font-medium">Pinjaman</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="mb-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Customer Support</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.chat.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.chat.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4-.8L3 20l1.22-2.44A7.793 7.793 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span class="font-medium">Chat Support</span>
                            <span id="open-chats-count" class="bg-orange-500 text-white text-xs rounded-full px-2 py-1 hidden"></span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.tickets.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.tickets.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                            <span class="font-medium">Tiket Support</span>
                            <span id="open-tickets-count" class="bg-red-500 text-white text-xs rounded-full px-2 py-1 hidden"></span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="mb-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Seller System</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.seller-requests.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.seller-requests.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="font-medium">Permintaan Seller</span>
                            <span id="pending-count" class="bg-red-500 text-white text-xs rounded-full px-2 py-1 hidden"></span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.invitation-codes.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.invitation-codes.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 12H9v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.586l4.707-4.707A1 1 0 0111 3h6a2 2 0 012 2v2z"></path>
                            </svg>
                            <span class="font-medium">Kode Undangan</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="mb-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">KYC</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.kyc.requests.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.kyc.requests.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <span class="font-medium">Permintaan KYC</span>
                    </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.kyc.snapshots.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.kyc.snapshots.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="font-medium">Snapshot KYC</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Sistem</p>
                <ul class="space-y-1">
                    <li>
                        <a href="#" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="font-medium">Pengaturan</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Logout Section - Fixed at bottom -->
    <div class="p-4 border-t border-gray-200 mt-auto">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-700 transition-all duration-200 w-full text-left">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span class="font-medium">Keluar</span>
            </button>
        </form>
    </div>
</div>

<script>
// Update pending seller requests count
function updatePendingCount() {
    fetch('{{ route("admin.seller-requests.count") }}')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('pending-count');
            if (data.count > 0) {
                badge.textContent = data.count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        })
        .catch(error => console.log('Error fetching pending count:', error));
}

// Update pending withdrawal requests count
function updateWithdrawalCount() {
    fetch('/admin/api/withdrawals/count')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('withdrawal-pending-count');
            if (data.count > 0) {
                badge.textContent = data.count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        })
        .catch(error => console.log('Error fetching withdrawal count:', error));
}

// Update open tickets count
function updateTicketsCount() {
    fetch('/admin/api/tickets/count')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('open-tickets-count');
            if (data.count > 0) {
                badge.textContent = data.count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        })
        .catch(error => console.log('Error fetching tickets count:', error));
}

// Update open chats count
function updateChatsCount() {
    fetch('/admin/api/chats/count')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('open-chats-count');
            if (data.count > 0) {
                badge.textContent = data.count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        })
        .catch(error => console.log('Error fetching chats count:', error));
}

// Update counts on page load and every 30 seconds
document.addEventListener('DOMContentLoaded', function() {
    updatePendingCount();
    updateTicketsCount();
    updateChatsCount();
    updateWithdrawalCount();
    setInterval(function() {
        updatePendingCount();
        updateTicketsCount();
        updateChatsCount();
        updateWithdrawalCount();
    }, 30000);
});
</script>
