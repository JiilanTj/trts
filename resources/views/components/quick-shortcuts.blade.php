<div class="rounded-2xl mb-6 bg-neutral-900/80 border border-neutral-800 backdrop-blur p-5">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold tracking-wide text-neutral-200 uppercase">Pintasan Cepat</h3>
    </div>
    <!-- Komponen khusus user (role=user), jadi langsung pakai route browse.* -->
    <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-3">
        <!-- Kategori -->
        <a href="{{ route('browse.categories.index') }}" class="group relative flex flex-col items-center gap-1.5 p-3 rounded-xl bg-neutral-800/60 border border-neutral-700 hover:border-[#FE2C55] transition shadow-sm">
            <div class="relative">
                <div class="absolute -inset-1 rounded-full opacity-0 group-hover:opacity-100 transition blur-sm bg-gradient-to-r from-[#FE2C55]/70 to-[#25F4EE]/70"></div>
                <div class="w-10 h-10 rounded-full flex items-center justify-center relative bg-neutral-900 text-neutral-200 group-hover:text-white">
                    <div class="absolute inset-0 rounded-full ring-1 ring-neutral-700/80 group-hover:ring-transparent"></div>
                    <svg class="w-5 h-5 relative" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75h6.5v6.5h-6.5zM13.75 3.75h6.5v6.5h-6.5zM13.75 13.75h6.5v6.5h-6.5zM3.75 13.75h6.5v6.5h-6.5z" />
                    </svg>
                </div>
            </div>
            <span class="text-[11px] font-medium tracking-wide text-neutral-400 group-hover:text-white">Kategori</span>
        </a>
        <!-- Produk -->
        <a href="{{ route('browse.products.index') }}" class="group relative flex flex-col items-center gap-1.5 p-3 rounded-xl bg-neutral-800/60 border border-neutral-700 hover:border-[#25F4EE] transition shadow-sm">
            <div class="relative">
                <div class="absolute -inset-1 rounded-full opacity-0 group-hover:opacity-100 transition blur-sm bg-gradient-to-r from-[#25F4EE]/70 to-[#FE2C55]/70"></div>
                <div class="w-10 h-10 rounded-full flex items-center justify-center relative bg-neutral-900 text-neutral-200 group-hover:text-white">
                    <div class="absolute inset-0 rounded-full ring-1 ring-neutral-700/80 group-hover:ring-transparent"></div>
                    <svg class="w-5 h-5 relative" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5L12 3 3 7.5m18 0L12 12m9-4.5v9L12 21m0-9v9m0-9L3 7.5m9 4.5L3 7.5m0 0v9L12 21" />
                    </svg>
                </div>
            </div>
            <span class="text-[11px] font-medium tracking-wide text-neutral-400 group-hover:text-white">Produk</span>
        </a>
        @if(auth()->user()->isSeller())
            <!-- Toko Saya -->
            <a href="{{ route('seller-requests.index') }}" class="group relative flex flex-col items-center gap-1.5 p-3 rounded-xl bg-neutral-800/60 border border-neutral-700 hover:border-green-500/70 transition shadow-sm">
                <div class="relative">
                    <div class="absolute -inset-1 rounded-full opacity-0 group-hover:opacity-100 transition blur-sm bg-gradient-to-r from-green-400/60 to-green-600/60"></div>
                    <div class="w-10 h-10 rounded-full flex items-center justify-center relative bg-neutral-900 text-neutral-200 group-hover:text-white">
                        <div class="absolute inset-0 rounded-full ring-1 ring-neutral-700/80 group-hover:ring-transparent"></div>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-2.836A3 3 0 008.25 4.5c0-1.41.6-2.68 1.56-3.62a3.001 3.001 0 011.94.6 3 3 0 013.7 2.75c0 1.41.6 2.68 1.56 3.62.75.75 1.92.75 2.67 0 .96-.94 1.56-2.21 1.56-3.62a3 3 0 013.75-2.836A3.001 3.001 0 0116.5 2.85M8.25 9.75h7.5" />
                        </svg>
                    </div>
                </div>
                <span class="text-[11px] font-medium tracking-wide text-neutral-400 group-hover:text-white">Toko Saya</span>
            </a>
            <!-- Etalase Saya -->
            <a href="{{ route('user.showcases.index') }}" class="group relative flex flex-col items-center gap-1.5 p-3 rounded-xl bg-neutral-800/60 border border-neutral-700 hover:border-amber-500/70 transition shadow-sm">
                <div class="relative">
                    <div class="absolute -inset-1 rounded-full opacity-0 group-hover:opacity-100 transition blur-sm bg-gradient-to-r from-amber-400/60 to-yellow-500/60"></div>
                    <div class="w-10 h-10 rounded-full flex items-center justify-center relative bg-neutral-900 text-neutral-200 group-hover:text-white">
                        <div class="absolute inset-0 rounded-full ring-1 ring-neutral-700/80 group-hover:ring-transparent"></div>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v2.25A2.25 2.25 0 006 10.5zm0 9.75h2.25A2.25 2.25 0 0010.5 18v-2.25a2.25 2.25 0 00-2.25-2.25H6a2.25 2.25 0 00-2.25 2.25V18A2.25 2.25 0 006 20.25zm9.75-9.75H18a2.25 2.25 0 002.25-2.25V6A2.25 2.25 0 0018 3.75h-2.25A2.25 2.25 0 0013.5 6v2.25a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                </div>
                <span class="text-[11px] font-medium tracking-wide text-neutral-400 group-hover:text-white">Etalase Saya</span>
            </a>
        @else
            <!-- Jadi Seller -->
            <a href="{{ route('seller-requests.index') }}" class="group relative flex flex-col items-center gap-1.5 p-3 rounded-xl bg-neutral-800/60 border border-neutral-700 hover:border-green-500/70 transition shadow-sm">
                <div class="relative">
                    <div class="absolute -inset-1 rounded-full opacity-0 group-hover:opacity-100 transition blur-sm bg-gradient-to-r from-green-400/60 to-green-600/60"></div>
                    <div class="w-10 h-10 rounded-full flex items-center justify-center relative bg-neutral-900 text-neutral-200 group-hover:text-white">
                        <div class="absolute inset-0 rounded-full ring-1 ring-neutral-700/80 group-hover:ring-transparent"></div>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-2.836A3 3 0 008.25 4.5c0-1.41.6-2.68 1.56-3.62a3.001 3.001 0 011.94.6 3 3 0 013.7 2.75c0 1.41.6 2.68 1.56 3.62.75.75 1.92.75 2.67 0 .96-.94 1.56-2.21 1.56-3.62a3 3 0 013.75-2.836A3.001 3.001 0 0116.5 2.85M8.25 9.75h7.5" />
                        </svg>
                    </div>
                </div>
                <span class="text-[11px] font-medium tracking-wide text-neutral-400 group-hover:text-white">Jadi Seller</span>
            </a>
        @endif
        <!-- Topup Saldo -->
        <a href="{{ route('user.topup.index') }}" class="group relative flex flex-col items-center gap-1.5 p-3 rounded-xl bg-neutral-800/60 border border-neutral-700 hover:border-cyan-500/70 transition shadow-sm">
            <div class="relative">
                <div class="absolute -inset-1 rounded-full opacity-0 group-hover:opacity-100 transition blur-sm bg-gradient-to-r from-cyan-400/60 to-blue-500/60"></div>
                <div class="w-10 h-10 rounded-full flex items-center justify-center relative bg-neutral-900 text-neutral-200 group-hover:text-white">
                    <div class="absolute inset-0 rounded-full ring-1 ring-neutral-700/80 group-hover:ring-transparent"></div>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <span class="text-[11px] font-medium tracking-wide text-neutral-400 group-hover:text-white">Topup</span>
        </a>
        <!-- Penarikan Saldo -->
        <a href="{{ route('user.withdrawals.index') }}" class="group relative flex flex-col items-center gap-1.5 p-3 rounded-xl bg-neutral-800/60 border border-neutral-700 hover:border-orange-500/70 transition shadow-sm">
            <div class="relative">
                <div class="absolute -inset-1 rounded-full opacity-0 group-hover:opacity-100 transition blur-sm bg-gradient-to-r from-orange-400/60 to-red-500/60"></div>
                <div class="w-10 h-10 rounded-full flex items-center justify-center relative bg-neutral-900 text-neutral-200 group-hover:text-white">
                    <div class="absolute inset-0 rounded-full ring-1 ring-neutral-700/80 group-hover:ring-transparent"></div>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21l-7.5-7.5M12 21V7.5" />
                    </svg>
                </div>
            </div>
            <span class="text-[11px] font-medium tracking-wide text-neutral-400 group-hover:text-white">Tarik Saldo</span>
        </a>
        <!-- Order Saya -->
        <a href="{{ route('user.orders.index') }}" class="group relative flex flex-col items-center gap-1.5 p-3 rounded-xl bg-neutral-800/60 border border-neutral-700 hover:border-purple-500/70 transition shadow-sm">
            <div class="relative">
                <div class="absolute -inset-1 rounded-full opacity-0 group-hover:opacity-100 transition blur-sm bg-gradient-to-r from-purple-400/60 to-pink-500/60"></div>
                <div class="w-10 h-10 rounded-full flex items-center justify-center relative bg-neutral-900 text-neutral-200 group-hover:text-white">
                    <div class="absolute inset-0 rounded-full ring-1 ring-neutral-700/80 group-hover:ring-transparent"></div>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </div>
            </div>
            <span class="text-[11px] font-medium tracking-wide text-neutral-400 group-hover:text-white">Order Saya</span>
        </a>
        <!-- History -->
        <a href="{{ route('user.history.index') }}" class="group relative flex flex-col items-center gap-1.5 p-3 rounded-xl bg-neutral-800/60 border border-neutral-700 hover:border-fuchsia-500/70 transition shadow-sm">
            <div class="relative">
                <div class="absolute -inset-1 rounded-full opacity-0 group-hover:opacity-100 transition blur-sm bg-gradient-to-r from-fuchsia-500/60 to-cyan-400/60"></div>
                <div class="w-10 h-10 rounded-full flex items-center justify-center relative bg-neutral-900 text-neutral-200 group-hover:text-white">
                    <div class="absolute inset-0 rounded-full ring-1 ring-neutral-700/80 group-hover:ring-transparent"></div>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <span class="text-[11px] font-medium tracking-wide text-neutral-400 group-hover:text-white">History Saya</span>
        </a>
    </div>
</div>
