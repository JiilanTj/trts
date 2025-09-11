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
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_25%_15%,rgba(255,187,0,0.07),transparent_60%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_85%,rgba(251,146,60,0.08),transparent_65%)]"></div>
        
        <!-- Header -->
        <div class="sticky top-0 z-40 backdrop-blur-md bg-[#0f1115]/70 border-b border-white/10">
            <div class="px-4 py-3 flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:bg-white/5 transition focus:outline-none focus:ring-2 focus:ring-amber-500/60" aria-label="Dashboard">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-semibold text-white leading-tight">Penarikan Saldo</h1>
                    <p class="text-[11px] text-gray-500 mt-0.5">Kelola permintaan penarikan saldo Anda</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="hidden sm:flex items-center gap-1 px-2 py-1 rounded-lg bg-gradient-to-r from-green-500/10 to-emerald-500/10 border border-green-500/20">
                        <svg class="w-3 h-3 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-[10px] text-green-300 font-medium">Rp {{ number_format(auth()->user()->balance ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <a href="{{ route('user.withdrawals.create') }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white rounded-lg text-[11px] font-medium transition shadow-md focus:outline-none focus:ring-2 focus:ring-amber-500/50">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="hidden sm:inline">Ajukan</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="px-4 py-4 space-y-4">
            @if(session('success'))
            <div class="flex items-start gap-3 p-4 bg-green-500/10 border border-green-500/20 rounded-xl">
                <svg class="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-green-300 text-sm">{{ session('success') }}</p>
            </div>
            @endif

            @if(session('error'))
            <div class="flex items-start gap-3 p-4 bg-red-500/10 border border-red-500/20 rounded-xl">
                <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
                <p class="text-red-300 text-sm">{{ session('error') }}</p>
            </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-white">{{ $stats['total'] ?? 0 }}</span>
                    </div>
                    <p class="text-[11px] text-gray-400 font-medium">Total Penarikan</p>
                </div>
                <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-8 h-8 rounded-lg bg-yellow-500/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-white">{{ $stats['pending'] ?? 0 }}</span>
                    </div>
                    <p class="text-[11px] text-gray-400 font-medium">Menunggu</p>
                </div>
                <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-white">{{ $stats['processing'] ?? 0 }}</span>
                    </div>
                    <p class="text-[11px] text-gray-400 font-medium">Diproses</p>
                </div>
                <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-8 h-8 rounded-lg bg-green-500/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-white">{{ $stats['completed'] ?? 0 }}</span>
                    </div>
                    <p class="text-[11px] text-gray-400 font-medium">Selesai</p>
                </div>
            </div>

            <!-- Withdrawals List -->
            @if($withdrawals->count() > 0)
            <div class="space-y-3">
                @foreach($withdrawals as $withdrawal)
                <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-4 hover:bg-white/10 transition">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0
                            @if($withdrawal->status === 'pending') bg-yellow-500/20 text-yellow-400
                            @elseif($withdrawal->status === 'processing') bg-blue-500/20 text-blue-400
                            @elseif($withdrawal->status === 'completed') bg-green-500/20 text-green-400
                            @elseif($withdrawal->status === 'rejected') bg-red-500/20 text-red-400
                            @else bg-gray-500/20 text-gray-400
                            @endif">
                            @if($withdrawal->status === 'pending')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @elseif($withdrawal->status === 'processing')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            @elseif($withdrawal->status === 'completed')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @elseif($withdrawal->status === 'rejected')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="font-semibold text-white text-sm truncate">{{ $withdrawal->bank_name }}</h3>
                                        <span class="px-2 py-0.5 text-[10px] font-medium rounded-full shrink-0
                                            @if($withdrawal->status === 'pending') bg-yellow-500/20 text-yellow-300
                                            @elseif($withdrawal->status === 'processing') bg-blue-500/20 text-blue-300
                                            @elseif($withdrawal->status === 'completed') bg-green-500/20 text-green-300
                                            @elseif($withdrawal->status === 'rejected') bg-red-500/20 text-red-300
                                            @else bg-gray-500/20 text-gray-300
                                            @endif">
                                            {{ $withdrawal->getStatusLabel() }}
                                        </span>
                                    </div>
                                    <p class="text-gray-400 text-xs">{{ $withdrawal->account_name }} • {{ $withdrawal->account_number }}</p>
                                    <p class="text-gray-500 text-[10px] mt-1">{{ $withdrawal->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-sm font-bold text-white">
                                        Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                                    </div>
                                    @if($withdrawal->admin_fee > 0)
                                    <div class="text-[10px] text-yellow-400">
                                        Fee: Rp {{ number_format($withdrawal->admin_fee, 0, ',', '.') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 mt-3">
                                <a href="{{ route('user.withdrawals.show', $withdrawal) }}" 
                                   class="inline-flex items-center gap-1 px-2 py-1 bg-white/10 hover:bg-white/20 text-white text-[10px] rounded-lg transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </a>
                                @if($withdrawal->canBeCancelled())
                                <form action="{{ route('user.withdrawals.cancel', $withdrawal) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Yakin ingin membatalkan penarikan ini?')"
                                            class="inline-flex items-center gap-1 px-2 py-1 bg-red-500/20 hover:bg-red-500/30 text-red-300 text-[10px] rounded-lg transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Batal
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                @if($withdrawals->hasPages())
                <div class="flex justify-center pt-4">
                    {{ $withdrawals->links() }}
                </div>
                @endif
            </div>

            @else
            <div class="text-center py-12">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-white/5 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-white mb-2">Belum Ada Penarikan</h3>
                <p class="text-gray-400 mb-6 text-sm">Anda belum pernah mengajukan penarikan saldo.</p>
                <a href="{{ route('user.withdrawals.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white rounded-lg font-medium text-sm transition shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajukan Penarikan Pertama
                </a>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
