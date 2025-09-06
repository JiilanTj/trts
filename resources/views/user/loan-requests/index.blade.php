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
                                <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center bg-[#FE2C55] text-white text-xs font-bold shadow-[0_0_0_2px_#000]">💰</div>
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
                                        <span class="text-sm font-semibold">Pengajuan Pinjaman</span>
                                        <p class="text-[10px] uppercase tracking-wide text-neutral-400 mt-0.5">Layanan Keuangan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <!-- New Loan Request Button -->
                            <a href="{{ route('user.loan-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] text-white text-sm font-medium rounded-lg hover:shadow-lg transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Ajukan Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <!-- Total Requests -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4 relative overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-blue-500 to-blue-400 absolute top-0 left-0"></div>
                    <div class="text-center">
                        <p class="text-xs text-neutral-400 mb-1">Total Pengajuan</p>
                        <p class="text-2xl font-bold text-blue-400">{{ $stats['total_requests'] }}</p>
                    </div>
                </div>

                <!-- Pending -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4 relative overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-yellow-500 to-amber-400 absolute top-0 left-0"></div>
                    <div class="text-center">
                        <p class="text-xs text-neutral-400 mb-1">Menunggu</p>
                        <p class="text-2xl font-bold text-yellow-400">{{ $stats['pending_requests'] }}</p>
                    </div>
                </div>

                <!-- Approved -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4 relative overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-green-500 to-emerald-400 absolute top-0 left-0"></div>
                    <div class="text-center">
                        <p class="text-xs text-neutral-400 mb-1">Disetujui</p>
                        <p class="text-2xl font-bold text-green-400">{{ $stats['approved_requests'] }}</p>
                    </div>
                </div>

                <!-- Active Loans -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4 relative overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-purple-500 to-pink-400 absolute top-0 left-0"></div>
                    <div class="text-center">
                        <p class="text-xs text-neutral-400 mb-1">Pinjaman Aktif</p>
                        <p class="text-2xl font-bold text-purple-400">{{ $stats['active_loans'] }}</p>
                    </div>
                </div>

                <!-- Total Borrowed -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4 relative overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-indigo-500 to-cyan-400 absolute top-0 left-0"></div>
                    <div class="text-center">
                        <p class="text-xs text-neutral-400 mb-1">Total Dipinjam</p>
                        <p class="text-lg font-bold text-indigo-400">Rp {{ number_format($stats['total_borrowed']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Filter and Search -->
            <div class="mb-6">
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4">
                    <form method="GET" action="{{ route('user.loan-requests.index') }}" class="flex items-center space-x-4">
                        <div class="flex-1">
                            <select name="status" class="w-full px-3 py-2 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-100 focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                                <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>Sedang Ditinjau</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                <option value="disbursed" {{ request('status') === 'disbursed' ? 'selected' : '' }}>Dicairkan</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-[#FE2C55] text-white rounded-lg hover:bg-[#FE2C55]/80 transition">
                            Filter
                        </button>
                        @if(request()->hasAny(['status']))
                            <a href="{{ route('user.loan-requests.index') }}" class="px-4 py-2 bg-neutral-700 text-neutral-300 rounded-lg hover:bg-neutral-600 transition">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Loan Requests List -->
            <div class="rounded-xl border border-[#2c3136] bg-[#23272b] overflow-hidden">
                <div class="h-1 w-full bg-gradient-to-r from-[#fe2c55] via-[#fe2c55]/40 to-[#25f4ee]"></div>
                
                @if($loanRequests->count() > 0)
                    <div class="divide-y divide-neutral-700">
                        @foreach($loanRequests as $loanRequest)
                            <div class="p-6 hover:bg-neutral-800/30 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-4 mb-3">
                                            <div class="flex items-center space-x-2">
                                                <h3 class="text-lg font-semibold text-neutral-100">
                                                    {{ $loanRequest->formatted_amount }}
                                                </h3>
                                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $loanRequest->status_color }}">
                                                    {{ $loanRequest->status_label }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                            <div>
                                                <p class="text-neutral-400">Tujuan</p>
                                                <p class="text-neutral-200 font-medium">{{ $loanRequest->purpose_label }}</p>
                                            </div>
                                            <div>
                                                <p class="text-neutral-400">Durasi</p>
                                                <p class="text-neutral-200 font-medium">{{ $loanRequest->duration_months }} bulan</p>
                                            </div>
                                            <div>
                                                <p class="text-neutral-400">Cicilan Bulanan</p>
                                                <p class="text-neutral-200 font-medium">{{ $loanRequest->formatted_monthly_payment }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3 text-xs text-neutral-400">
                                            Diajukan pada {{ $loanRequest->created_at->format('d M Y \p\u\k\u\l H:i') }}
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-3 ml-6">
                                        <a href="{{ route('user.loan-requests.show', $loanRequest) }}" 
                                           class="inline-flex items-center px-3 py-2 bg-neutral-700 text-neutral-300 text-sm rounded-lg hover:bg-neutral-600 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Lihat
                                        </a>
                                        
                                        @if($loanRequest->status === 'pending')
                                            <a href="{{ route('user.loan-requests.edit', $loanRequest) }}" 
                                               class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="p-6 border-t border-neutral-700">
                        {{ $loanRequests->links() }}
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-neutral-800 flex items-center justify-center">
                            <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-neutral-300 mb-2">Belum ada pengajuan pinjaman</h3>
                        <p class="text-neutral-400 mb-6">Mulai dengan membuat pengajuan pinjaman pertama Anda untuk mengakses pembiayaan usaha.</p>
                        <a href="{{ route('user.loan-requests.create') }}" 
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] text-white font-medium rounded-lg hover:shadow-lg transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Buat Pengajuan Pinjaman
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
