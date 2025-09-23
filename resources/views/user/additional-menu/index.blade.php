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
                                <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center bg-[#FE2C55] text-white text-xs font-bold shadow-[0_0_0_2px_#000]">+</div>
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
                                        <span class="text-sm font-semibold">Menu Tambahan</span>
                                        <p class="text-[10px] uppercase tracking-wide text-neutral-400 mt-0.5">Fitur & Pengaturan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-end space-y-1">
                            <!-- Status Badge -->
                            <div class="flex items-center space-x-1 px-2 py-1 rounded-md bg-emerald-500/15 text-emerald-400 text-xs font-medium">
                                <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                                <span>Active</span>
                            </div>
                            @if(auth()->user()->isSeller())
                                <!-- Seller Badge -->
                                <div class="flex items-center space-x-1 px-2 py-1 rounded-md bg-orange-500/15 text-orange-400 text-xs font-medium">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    <span>Seller</span>
                                </div>
                            @endif
                            <!-- Level Badge -->
                            @php($userLevel = auth()->user()->level ?? 1)
                            <div class="flex items-center space-x-1 px-2 py-1 rounded-md 
                                {{ $userLevel == 6 ? 'bg-gradient-to-r from-purple-500/20 via-pink-500/20 to-yellow-500/20 text-purple-300' : 
                                   ($userLevel == 10 ? 'bg-red-500/15 text-red-400' : 'bg-blue-500/15 text-blue-400') }}
                                text-xs font-medium">
                                @if($userLevel == 6)
                                    <!-- Special icon for level 6 -->
                                    <svg class="w-3 h-3 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z" />
                                    </svg>
                                    <span class="font-bold bg-gradient-to-r from-purple-400 via-pink-400 to-yellow-400 bg-clip-text text-transparent">Toko dari Mulut ke Mulut</span>
                                @elseif($userLevel == 10)
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    <span>Admin</span>
                                @else
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    <span>Bintang {{ $userLevel }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <!-- Menu List Card -->
            <div class="rounded-xl mb-6 border border-[#2c3136] bg-[#23272b] shadow-sm relative overflow-hidden">
                <div class="h-1 w-full bg-gradient-to-r from-[#fe2c55] via-[#fe2c55]/40 to-[#25f4ee]"></div>
                
                <!-- Alat Dasar Section -->
                <div class="p-5 pb-0">
                    <h3 class="text-xs font-semibold mb-4 text-neutral-400 uppercase tracking-wide">Alat Dasar</h3>
                </div>
                
                <div class="px-5 space-y-1">
                    <!-- Pusat Wholase -->
                    <a href="{{ route('user.wholesale.index') }}" class="flex items-center justify-between py-3 px-4 rounded-lg bg-neutral-800/40 border border-neutral-700/50 hover:border-[#FE2C55]/30 hover:bg-neutral-800/60 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#FE2C55]/20 to-[#25F4EE]/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-neutral-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-100 group-hover:text-white">Pusat Wholase</p>
                                <p class="text-xs text-neutral-400">Manajemen pusat distribusi</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-neutral-400 group-hover:text-[#FE2C55]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <!-- Manajemen Produk -->
                    <a href="{{ route('browse.products.index') }}" class="flex items-center justify-between py-3 px-4 rounded-lg bg-neutral-800/40 border border-neutral-700/50 hover:border-[#25F4EE]/30 hover:bg-neutral-800/60 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#25F4EE]/20 to-[#FE2C55]/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-neutral-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-100 group-hover:text-white">Manajemen Produk</p>
                                <p class="text-xs text-neutral-400">Kelola katalog produk</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-neutral-400 group-hover:text-[#25F4EE]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <!-- Manajemen Status -->
                    <a href="{{ route('user.orders-by-admin.index') }}" class="flex items-center justify-between py-3 px-4 rounded-lg bg-neutral-800/40 border border-neutral-700/50 hover:border-emerald-500/30 hover:bg-neutral-800/60 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500/20 to-blue-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-neutral-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-100 group-hover:text-white">Manajemen Status</p>
                                <p class="text-xs text-neutral-400">Kelola status order & transaksi</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-neutral-400 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <!-- Manajemen Evaluasi -->
                    <a href="{{ route('user.analytics.index') }}" class="flex items-center justify-between py-3 px-4 rounded-lg bg-neutral-800/40 border border-neutral-700/50 hover:border-amber-500/30 hover:bg-neutral-800/60 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-500/20 to-orange-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-neutral-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-100 group-hover:text-white">Manajemen Evaluasi</p>
                                <p class="text-xs text-neutral-400">Analytics & performa bisnis</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-neutral-400 group-hover:text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>

                <!-- Divider -->
                <div class="mx-5 my-6">
                    <div class="border-t border-neutral-700/60"></div>
                </div>

                <!-- Pemutakhiran Bisnis Section -->
                <div class="px-5 pb-0">
                    <h3 class="text-xs font-semibold mb-4 text-neutral-400 uppercase tracking-wide">Pemutakhiran Bisnis</h3>
                </div>

                <div class="px-5 pb-5 space-y-1">
                    <!-- Layanan Online -->
                    <a href="{{ route('user.chat.index') }}" class="flex items-center justify-between py-3 px-4 rounded-lg bg-neutral-800/40 border border-neutral-700/50 hover:border-purple-500/30 hover:bg-neutral-800/60 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500/20 to-pink-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-neutral-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4-.8L3 20l1.22-2.44A7.793 7.793 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-100 group-hover:text-white">Layanan Online</p>
                                <p class="text-xs text-neutral-400">Customer Service & Live Chat</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-neutral-400 group-hover:text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <!-- Loan Request -->
                    <a href="{{ route('user.loan-requests.index') }}" class="flex items-center justify-between py-3 px-4 rounded-lg bg-neutral-800/40 border border-neutral-700/50 hover:border-green-500/30 hover:bg-neutral-800/60 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-500/20 to-emerald-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-neutral-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-100 group-hover:text-white">Loan Request</p>
                                <p class="text-xs text-neutral-400">Business Financing & Capital</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-neutral-400 group-hover:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
