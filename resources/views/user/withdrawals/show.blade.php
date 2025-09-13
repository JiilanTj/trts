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
                <a href="{{ route('user.withdrawals.index') }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:bg-white/5 transition focus:outline-none focus:ring-2 focus:ring-amber-500/60" aria-label="Kembali">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-semibold text-white leading-tight">Detail Penarikan</h1>
                    <p class="text-[11px] text-gray-500 mt-0.5">Informasi lengkap tentang permintaan penarikan saldo</p>
                </div>
                @if($withdrawal->canBeCancelled())
                <form action="{{ route('user.withdrawals.cancel', $withdrawal) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('Yakin ingin membatalkan penarikan ini? Saldo akan dikembalikan ke akun Anda.')"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-red-500/20 hover:bg-red-500/30 text-red-300 text-[11px] rounded-lg transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span class="hidden sm:inline">Batal</span>
                    </button>
                </form>
                @endif
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-4">
                    <!-- Status Card -->
                    <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl overflow-hidden">
                        <div class="px-4 py-3 border-b border-white/10">
                            <h2 class="text-sm font-semibold text-white">Status Penarikan</h2>
                        </div>
                        
                        <div class="p-4">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center
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
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="text-lg font-semibold text-white">{{ $withdrawal->status_label }}</h3>
                                        <span class="px-2 py-1 text-[10px] font-medium rounded-full
                                            @if($withdrawal->status === 'pending') bg-yellow-500/20 text-yellow-300
                                            @elseif($withdrawal->status === 'processing') bg-blue-500/20 text-blue-300
                                            @elseif($withdrawal->status === 'completed') bg-green-500/20 text-green-300
                                            @elseif($withdrawal->status === 'rejected') bg-red-500/20 text-red-300
                                            @else bg-gray-500/20 text-gray-300
                                            @endif">
                                            {{ $withdrawal->status }}
                                        </span>
                                    </div>
                                    <p class="text-gray-400 text-sm">
                                        @if($withdrawal->status === 'pending')
                                            Menunggu konfirmasi dari admin
                                        @elseif($withdrawal->status === 'processing')
                                            Sedang diproses oleh tim finance
                                        @elseif($withdrawal->status === 'completed')
                                            Saldo telah ditransfer ke rekening Anda
                                        @elseif($withdrawal->status === 'rejected')
                                            Penarikan ditolak oleh admin
                                        @else
                                            Penarikan dibatalkan
                                        @endif
                                    </p>
                                </div>
                            </div>

                            @if($withdrawal->notes)
                            <div class="bg-white/5 border border-white/10 rounded-lg p-3">
                                <h4 class="text-white font-medium text-xs mb-2">Catatan:</h4>
                                <p class="text-gray-300 text-sm">{{ $withdrawal->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl overflow-hidden">
                        <div class="px-4 py-3 border-b border-white/10">
                            <h2 class="text-sm font-semibold text-white">Informasi Rekening</h2>
                        </div>
                        
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-400 mb-1">Nama Pemilik Rekening</label>
                                    <div class="text-white font-medium">{{ $withdrawal->account_holder_name }}</div>
                                </div>
                                
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-400 mb-1">Nomor Rekening</label>
                                    <div class="text-white font-medium font-mono">{{ $withdrawal->account_number }}</div>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-medium text-gray-400 mb-1">Nama Bank</label>
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <div class="text-white font-medium">{{ $withdrawal->bank_name }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl overflow-hidden">
                        <div class="px-4 py-3 border-b border-white/10">
                            <h2 class="text-sm font-semibold text-white">Timeline</h2>
                        </div>
                        
                        <div class="p-4">
                            <div class="space-y-4">
                                <!-- Created -->
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-white font-medium text-sm">Pengajuan Dibuat</h4>
                                        <p class="text-gray-400 text-xs">{{ $withdrawal->created_at->format('d M Y, H:i:s') }}</p>
                                        <p class="text-gray-500 text-[10px]">{{ $withdrawal->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>

                                @if($withdrawal->status !== 'pending')
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full 
                                        @if($withdrawal->status === 'processing') bg-blue-500/20 
                                        @elseif($withdrawal->status === 'completed') bg-green-500/20 
                                        @elseif($withdrawal->status === 'rejected') bg-red-500/20 
                                        @else bg-gray-500/20 
                                        @endif flex items-center justify-center shrink-0">
                                        @if($withdrawal->status === 'processing')
                                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                        @elseif($withdrawal->status === 'completed')
                                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @elseif($withdrawal->status === 'rejected')
                                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-white font-medium text-sm">
                                            @if($withdrawal->status === 'processing') Mulai Diproses
                                            @elseif($withdrawal->status === 'completed') Selesai
                                            @elseif($withdrawal->status === 'rejected') Ditolak
                                            @else Dibatalkan
                                            @endif
                                        </h4>
                                        <p class="text-gray-400 text-xs">{{ $withdrawal->updated_at->format('d M Y, H:i:s') }}</p>
                                        <p class="text-gray-500 text-[10px]">{{ $withdrawal->updated_at->diffForHumans() }}</p>
                                        @if($withdrawal->processedBy)
                                        <p class="text-gray-500 text-[10px] mt-1">oleh {{ $withdrawal->processedBy->name }}</p>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-4">
                    <!-- Amount Summary -->
                    <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl overflow-hidden">
                        <div class="px-4 py-3 border-b border-white/10">
                            <h2 class="text-sm font-semibold text-white">Rincian Jumlah</h2>
                        </div>
                        
                        <div class="p-4 space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 text-sm">Jumlah Penarikan</span>
                                <span class="text-white font-semibold">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</span>
                            </div>
                            
                            @if($withdrawal->admin_fee > 0)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 text-sm">Biaya Admin</span>
                                <span class="text-yellow-400 font-semibold">Rp {{ number_format($withdrawal->admin_fee, 0, ',', '.') }}</span>
                            </div>
                            
                            <hr class="border-white/10">
                            
                            <div class="flex justify-between items-center">
                                <span class="text-white font-medium">Total Dipotong</span>
                                <span class="text-red-400 font-bold">Rp {{ number_format($withdrawal->total_deducted, 0, ',', '.') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl overflow-hidden">
                        <div class="px-4 py-3 border-b border-white/10">
                            <h2 class="text-sm font-semibold text-white">Aksi Cepat</h2>
                        </div>
                        
                        <div class="p-4 space-y-3">
                            <a href="{{ route('user.withdrawals.create') }}" 
                               class="flex items-center gap-3 p-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg transition group">
                                <div class="w-8 h-8 rounded-lg bg-amber-500/20 flex items-center justify-center group-hover:bg-amber-500/30">
                                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-white font-medium text-sm">Penarikan Baru</h4>
                                    <p class="text-gray-400 text-xs">Ajukan penarikan lainnya</p>
                                </div>
                            </a>
                            
                            <a href="{{ route('user.withdrawals.index') }}" 
                               class="flex items-center gap-3 p-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg transition group">
                                <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center group-hover:bg-blue-500/30">
                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-white font-medium text-sm">Daftar Penarikan</h4>
                                    <p class="text-gray-400 text-xs">Lihat semua penarikan</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Contact Support -->
                    <div class="bg-gradient-to-br from-blue-500/10 to-purple-500/10 border border-blue-500/20 rounded-xl p-4">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-white font-semibold text-sm">Butuh Bantuan?</h3>
                        </div>
                        <p class="text-blue-200/80 text-xs mb-3">
                            Jika ada pertanyaan tentang penarikan ini, hubungi tim support kami.
                        </p>
                        <a href="#" 
                           class="inline-flex items-center gap-2 px-3 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg text-xs font-medium transition w-full justify-center">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Hubungi Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

