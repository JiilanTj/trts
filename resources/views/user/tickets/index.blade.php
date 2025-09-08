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
                                <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center bg-[#FE2C55] text-white text-xs font-bold shadow-[0_0_0_2px_#000]">🎫</div>
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
                                        <span class="text-sm font-semibold">Tiket Support</span>
                                        <p class="text-[10px] uppercase tracking-wide text-neutral-400 mt-0.5">Pusat Bantuan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <!-- New Ticket Button -->
                            <a href="{{ route('user.tickets.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] text-white text-sm font-medium rounded-lg hover:shadow-lg transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Buat Tiket Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Total Tickets -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4 relative overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-blue-500 to-blue-400 absolute top-0 left-0"></div>
                    <div class="text-center">
                        <p class="text-xs text-neutral-400 mb-1">Total Tiket</p>
                        <p class="text-2xl font-bold text-blue-400">{{ $tickets->total() }}</p>
                    </div>
                </div>

                <!-- Open Tickets -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4 relative overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-yellow-500 to-amber-400 absolute top-0 left-0"></div>
                    <div class="text-center">
                        <p class="text-xs text-neutral-400 mb-1">Terbuka</p>
                        <p class="text-2xl font-bold text-yellow-400">{{ $tickets->where('status', 'open')->count() }}</p>
                    </div>
                </div>

                <!-- In Progress -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4 relative overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-purple-500 to-pink-400 absolute top-0 left-0"></div>
                    <div class="text-center">
                        <p class="text-xs text-neutral-400 mb-1">Diproses</p>
                        <p class="text-2xl font-bold text-purple-400">{{ $tickets->where('status', 'in_progress')->count() }}</p>
                    </div>
                </div>

                <!-- Resolved -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4 relative overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-green-500 to-emerald-400 absolute top-0 left-0"></div>
                    <div class="text-center">
                        <p class="text-xs text-neutral-400 mb-1">Selesai</p>
                        <p class="text-2xl font-bold text-green-400">{{ $tickets->where('status', 'resolved')->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Filter and Search -->
            <div class="mb-6">
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4">
                    <form method="GET" action="{{ route('user.tickets.index') }}" class="flex flex-wrap gap-4">
                        <div class="flex-1 min-w-64">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Cari tiket..." 
                                   class="w-full px-3 py-2 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-100 focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent">
                        </div>
                        <div>
                            <select name="status" class="px-3 py-2 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-100 focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent">
                                <option value="">Semua Status</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Terbuka</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Diproses</option>
                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Selesai</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Ditutup</option>
                            </select>
                        </div>
                        <div>
                            <select name="category" class="px-3 py-2 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-100 focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent">
                                <option value="">Semua Kategori</option>
                                <option value="technical" {{ request('category') == 'technical' ? 'selected' : '' }}>Teknis</option>
                                <option value="billing" {{ request('category') == 'billing' ? 'selected' : '' }}>Penagihan</option>
                                <option value="general" {{ request('category') == 'general' ? 'selected' : '' }}>Umum</option>
                                <option value="account" {{ request('category') == 'account' ? 'selected' : '' }}>Akun</option>
                                <option value="loan" {{ request('category') == 'loan' ? 'selected' : '' }}>Pinjaman</option>
                                <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-[#FE2C55] text-white rounded-lg hover:bg-[#FE2C55]/80 transition">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'status', 'category']))
                            <a href="{{ route('user.tickets.index') }}" class="px-4 py-2 bg-neutral-700 text-neutral-300 rounded-lg hover:bg-neutral-600 transition">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Tickets List -->
            <div class="rounded-xl border border-[#2c3136] bg-[#23272b] overflow-hidden">
                <div class="h-1 w-full bg-gradient-to-r from-[#fe2c55] via-[#fe2c55]/40 to-[#25f4ee]"></div>
                
                @if($tickets->count() > 0)
                    <div class="divide-y divide-neutral-700">
                        @foreach($tickets as $ticket)
                            <div class="p-6 hover:bg-neutral-800/30 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-4 mb-3">
                                            <div class="flex items-center space-x-2">
                                                <h3 class="text-lg font-semibold text-neutral-100">
                                                    #{{ $ticket->ticket_number }}
                                                </h3>
                                                @if($ticket->status === 'open')
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-500/15 text-blue-300 border border-blue-600/30">
                                                        Terbuka
                                                    </span>
                                                @elseif($ticket->status === 'in_progress')
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-500/15 text-purple-300 border border-purple-600/30">
                                                        Diproses
                                                    </span>
                                                @elseif($ticket->status === 'resolved')
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-500/15 text-green-300 border border-green-600/30">
                                                        Selesai
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-500/15 text-gray-300 border border-gray-600/30">
                                                        Ditutup
                                                    </span>
                                                @endif
                                                
                                                @if($ticket->priority === 'urgent')
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-500/15 text-red-300 border border-red-600/30">
                                                        Mendesak
                                                    </span>
                                                @elseif($ticket->priority === 'high')
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-orange-500/15 text-orange-300 border border-orange-600/30">
                                                        Tinggi
                                                    </span>
                                                @elseif($ticket->priority === 'medium')
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-500/15 text-yellow-300 border border-yellow-600/30">
                                                        Sedang
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-500/15 text-green-300 border border-green-600/30">
                                                        Rendah
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="space-y-2">
                                            <h4 class="text-base font-medium text-neutral-100">{{ $ticket->title }}</h4>
                                            <p class="text-sm text-neutral-400 line-clamp-2">{{ Str::limit($ticket->description, 120) }}</p>
                                            <div class="flex items-center space-x-4 text-xs text-neutral-500">
                                                <span class="flex items-center space-x-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                    </svg>
                                                    <span>{{ ucfirst($ticket->category) }}</span>
                                                </span>
                                                <span class="flex items-center space-x-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span>{{ $ticket->created_at->diffForHumans() }}</span>
                                                </span>
                                                @if($ticket->assignedTo)
                                                    <span class="flex items-center space-x-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                        </svg>
                                                        <span>{{ $ticket->assignedTo->full_name }}</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="ml-4 flex-shrink-0">
                                        <a href="{{ route('user.tickets.show', $ticket) }}" class="inline-flex items-center px-3 py-2 bg-neutral-700 text-neutral-300 text-sm font-medium rounded-lg hover:bg-neutral-600 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Lihat
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-neutral-700">
                        {{ $tickets->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-[#FE2C55]/20 to-[#25F4EE]/20 flex items-center justify-center">
                            <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-neutral-100 mb-2">Belum ada tiket</h3>
                        <p class="text-sm text-neutral-400 mb-6">Buat tiket support pertama Anda untuk mendapatkan bantuan.</p>
                        <a href="{{ route('user.tickets.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] text-white text-sm font-medium rounded-lg hover:shadow-lg transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Buat Tiket Baru
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
