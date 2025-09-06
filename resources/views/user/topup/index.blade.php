<x-app-layout>
    <style>
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>
    <div class="min-h-screen bg-[#0f1115] text-gray-200 relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_25%_15%,rgba(6,182,212,0.07),transparent_60%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_85%,rgba(59,130,246,0.08),transparent_65%)]"></div>
        
        <!-- Header -->
        <div class="sticky top-0 z-40 backdrop-blur-md bg-[#0f1115]/70 border-b border-white/10">
            <div class="px-4 py-3 flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:bg-white/5 transition focus:outline-none focus:ring-2 focus:ring-cyan-500/60" aria-label="Dashboard">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-semibold text-white leading-tight">Topup Saldo</h1>
                    <p class="text-[11px] text-gray-500 mt-0.5">Kelola dan pantau permintaan topup saldo Anda.</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="hidden sm:flex items-center gap-1 px-2 py-1 rounded-lg bg-gradient-to-r from-cyan-500/10 to-blue-500/10 border border-cyan-500/20">
                        <svg class="w-3 h-3 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-[10px] text-cyan-300 font-medium">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</span>
                    </div>
                    @if(!$hasPendingTopup)
                        <a href="{{ route('user.topup.create') }}" class="sm:hidden inline-flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-r from-cyan-500 via-blue-500 to-indigo-500 text-white hover:from-cyan-500/90 hover:via-blue-500/90 hover:to-indigo-500/90 shadow-sm shadow-cyan-500/30 transition focus:outline-none focus:ring-2 focus:ring-cyan-500/60" aria-label="Topup Baru">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        </a>
                        <a href="{{ route('user.topup.create') }}" class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-gradient-to-r from-cyan-500 via-blue-500 to-indigo-500 text-white hover:from-cyan-500/90 hover:via-blue-500/90 hover:to-indigo-500/90 shadow-sm shadow-cyan-500/30 transition focus:outline-none focus:ring-2 focus:ring-cyan-500/60">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                            Topup Baru
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="px-3 sm:px-4 py-4 sm:py-6 max-w-5xl mx-auto space-y-6 sm:space-y-8">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-emerald-600/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-600/10 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
            @endif

            <!-- Pending Topup Warning -->
            @if($hasPendingTopup)
                <div class="bg-amber-600/10 border border-amber-500/30 text-amber-300 px-4 py-3 rounded-xl text-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        <span class="font-medium">Anda memiliki permintaan topup yang sedang diproses. Tunggu hingga disetujui atau ditolak sebelum membuat permintaan baru.</span>
                    </div>
                </div>
            @endif

            <!-- Topup Requests List -->
            <div class="space-y-3">
                @forelse($topupRequests as $topupRequest)
                    <div class="bg-[#181d23] border border-white/10 rounded-xl overflow-hidden hover:bg-white/5 transition">
                        <!-- Header section -->
                        <div class="p-4 pb-3">
                            <div class="flex items-center justify-between mb-2">
                                <p class="font-semibold text-white text-sm">Permintaan Topup #{{ $topupRequest->id }}</p>
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-600/20 text-amber-300',
                                        'approved' => 'bg-emerald-600/20 text-emerald-300',
                                        'rejected' => 'bg-red-600/20 text-red-300',
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Pending',
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-[10px] rounded-md font-medium {{ $statusColors[$topupRequest->status] }}">
                                    {{ $statusLabels[$topupRequest->status] }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400">{{ $topupRequest->created_at->format('d-m-Y H:i') }}</p>
                        </div>
                        
                        <!-- Amount section -->
                        <div class="px-4 py-3 bg-[#1a1f25]/50">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs text-gray-500">Jumlah Topup</span>
                                <span class="text-xs text-gray-500">Bank Tujuan</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-white">{{ $topupRequest->formatted_amount }}</span>
                                <div class="text-right">
                                    <div class="text-sm text-gray-300">{{ $topupRequest->bank_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $topupRequest->bank_account }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Transfer Info -->
                        <div class="px-4 py-3 border-b border-white/5">
                            <div class="grid grid-cols-2 gap-4 text-xs">
                                <div>
                                    <span class="text-gray-500">Tanggal Transfer:</span>
                                    <div class="text-gray-300 mt-0.5">{{ $topupRequest->transfer_date ? $topupRequest->transfer_date->format('d M Y H:i') : '-' }}</div>
                                </div>
                                <div>
                                    <span class="text-gray-500">Status Proses:</span>
                                    <div class="text-gray-300 mt-0.5">
                                        @if($topupRequest->status === 'pending')
                                            <span class="text-amber-400">Menunggu review admin</span>
                                        @elseif($topupRequest->status === 'approved')
                                            <span class="text-emerald-400">{{ $topupRequest->approved_at->format('d M Y H:i') }}</span>
                                        @else
                                            <span class="text-red-400">{{ $topupRequest->rejected_at->format('d M Y H:i') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Notes section -->
                        @if($topupRequest->notes || $topupRequest->admin_notes)
                            <div class="px-4 py-3 border-b border-white/5">
                                @if($topupRequest->notes)
                                    <div class="mb-2">
                                        <span class="text-xs text-gray-500">Catatan Anda:</span>
                                        <div class="text-xs text-gray-300 mt-1 bg-[#1a1f25]/40 rounded-lg p-2">{{ $topupRequest->notes }}</div>
                                    </div>
                                @endif
                                @if($topupRequest->admin_notes)
                                    <div>
                                        <span class="text-xs text-gray-500">Catatan Admin:</span>
                                        <div class="text-xs text-gray-300 mt-1 bg-[#1a1f25]/40 rounded-lg p-2">{{ $topupRequest->admin_notes }}</div>
                                    </div>
                                @endif
                            </div>
                        @endif
                        
                        <!-- Action section -->
                        <div class="px-4 py-3 bg-[#1a1f25]/30">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    @if($topupRequest->payment_proof)
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span>Bukti transfer tersedia</span>
                                    @endif
                                </div>
                                <a href="{{ route('user.topup.show', $topupRequest) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-cyan-400 bg-cyan-500/10 border border-cyan-500/20 rounded-lg hover:bg-cyan-500/20 transition">
                                    <span>Detail</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-cyan-500/20 to-blue-500/20 border border-cyan-500/30 mb-4">
                            <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">Belum ada permintaan topup</h3>
                        <p class="text-gray-400 text-sm mb-6">Buat permintaan topup pertama Anda untuk menambah saldo.</p>
                        @if(!$hasPendingTopup)
                            <a href="{{ route('user.topup.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-gradient-to-r from-cyan-500 via-blue-500 to-indigo-500 text-white hover:from-cyan-500/90 hover:via-blue-500/90 hover:to-indigo-500/90 shadow-sm shadow-cyan-500/30 transition focus:outline-none focus:ring-2 focus:ring-cyan-500/60">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                Buat Permintaan Topup
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($topupRequests->hasPages())
                <div class="flex justify-center">
                    {{ $topupRequests->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
