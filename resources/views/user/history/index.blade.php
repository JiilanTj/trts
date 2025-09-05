<x-app-layout>
    @php
        $user = auth()->user();
        $initials = collect(explode(' ', trim($user->full_name ?: $user->username)))
            ->filter()
            ->take(2)
            ->map(fn($p)=> strtoupper(mb_substr($p,0,1)))
            ->implode('');
        $unreadCount = collect($notifications)->where('read', false)->count();
    @endphp
    
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
                                <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center bg-[#FE2C55] text-white text-xs font-bold shadow-[0_0_0_2px_#000]">
                                    {{ $unreadCount }}
                                </div>
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
                                        <span class="text-sm font-semibold">Riwayat & Notifikasi</span>
                                        <p class="text-[10px] uppercase tracking-wide text-neutral-400 mt-0.5">{{ count($notifications) }} Pesan Sistem</p>
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
            <!-- Notifications List -->
            @php
                $iconColors = [
                    'emerald' => 'text-emerald-400 bg-emerald-500/10',
                    'blue' => 'text-blue-400 bg-blue-500/10',
                    'purple' => 'text-purple-400 bg-purple-500/10',
                    'pink' => 'text-pink-400 bg-pink-500/10',
                    'amber' => 'text-amber-400 bg-amber-500/10',
                    'cyan' => 'text-cyan-400 bg-cyan-500/10',
                ];
            @endphp
            <div class="space-y-1">
                @foreach($notifications as $notification)
                    @php
                        $isUnread = !$notification['read'];
                        $colorClass = $iconColors[$notification['color']] ?? 'text-neutral-400 bg-neutral-600/10';
                    @endphp
                    <div class="flex items-start gap-3 px-3 py-2 rounded-lg border transition
                                {{ $isUnread ? 'border-[#FE2C55]/60 bg-[#2c3136]' : 'border-transparent hover:border-neutral-700 bg-[#23272b]/70 hover:bg-[#2c3136]' }}">
                        <div class="relative">
                            @if($isUnread)
                                <span class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-[#FE2C55]"></span>
                            @endif
                            <div class="w-8 h-8 flex items-center justify-center rounded-md {{ $colorClass }}">
                                @switch($notification['icon'])
                                    @case('check-circle')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @break
                                    @case('credit-card')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                        @break
                                    @case('cog')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.757.426 1.757 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.757-2.924 1.757-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.757-.426-1.757-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.607 2.273.07 2.573-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        @break
                                    @case('gift')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                                        @break
                                    @case('shield-check')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        @break
                                    @case('truck')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                        @break
                                    @default
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM3 3v18l5-5h14V3H3z"/></svg>
                                @endswitch
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-sm font-medium leading-tight {{ $isUnread ? 'text-neutral-100' : 'text-neutral-200' }}">{{ $notification['title'] }}</h3>
                                <span class="text-[10px] text-neutral-500 whitespace-nowrap">{{ $notification['time'] }}</span>
                            </div>
                            <p class="text-xs mt-0.5 text-neutral-400 leading-snug line-clamp-2">{{ $notification['message'] }}</p>
                            @if($isUnread)
                                <button class="mt-1 text-[11px] text-[#FE2C55] hover:underline">Tandai sudah dibaca</button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Summary Card -->
            <div class="mt-5 flex items-center justify-between text-xs px-3 py-2 rounded-md bg-[#23272b] border border-neutral-700/40">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded bg-[#FE2C55]/15 text-[#FE2C55]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-5a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <span class="text-neutral-300 font-medium">{{ count($notifications) }} notifikasi ({{ $unreadCount }} belum dibaca)</span>
                </div>
                @if($unreadCount > 0)
                    <button class="text-[#25F4EE] hover:underline">Tandai semua</button>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
