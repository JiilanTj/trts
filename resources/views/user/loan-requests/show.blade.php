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
                                        <span class="text-sm font-semibold">Detail Pinjaman #{{ $loanRequest->id }}</span>
                                        <p class="text-[10px] uppercase tracking-wide text-neutral-400 mt-0.5">Status: {{ $loanRequest->status_label }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <!-- Back Button -->
                            <a href="{{ route('user.loan-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-neutral-700 text-neutral-300 text-sm font-medium rounded-lg hover:bg-neutral-600 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Kembali ke Daftar
                            </a>
                            
                            @if($loanRequest->status === 'pending')
                                <a href="{{ route('user.loan-requests.edit', $loanRequest) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="max-w-4xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Information -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Loan Status Card -->
                        <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-6">
                            <div class="h-1 w-full bg-gradient-to-r from-[#fe2c55] via-[#fe2c55]/40 to-[#25f4ee] absolute top-0 left-0 -mt-6 rounded-t-xl"></div>
                            
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-semibold text-neutral-100">Status Pinjaman</h2>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $loanRequest->status_color }}">
                                    {{ $loanRequest->status_label }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-neutral-400 mb-2">Jumlah Pinjaman</label>
                                    <p class="text-2xl font-bold text-white">{{ $loanRequest->formatted_amount }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-neutral-400 mb-2">Cicilan Bulanan</label>
                                    <p class="text-2xl font-bold text-[#25F4EE]">{{ $loanRequest->formatted_monthly_payment }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-neutral-400 mb-2">Jangka Waktu</label>
                                    <p class="text-lg font-medium text-white">{{ $loanRequest->duration_months }} bulan</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-neutral-400 mb-2">Suku Bunga</label>
                                    <p class="text-lg font-medium text-white">{{ number_format($loanRequest->interest_rate, 2) }}% per tahun</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-neutral-400 mb-2">Tujuan Pinjaman</label>
                                    <p class="text-lg font-medium text-white">{{ $loanRequest->purpose_label }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-neutral-400 mb-2">Tanggal Pengajuan</label>
                                    <p class="text-lg font-medium text-white">{{ $loanRequest->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                            
                            @if($loanRequest->due_date)
                                <div class="mt-6 p-4 bg-gradient-to-r from-orange-500/10 to-red-500/10 border border-orange-500/20 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-orange-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-orange-400">Jatuh Tempo</p>
                                            <p class="text-lg font-bold text-white">{{ $loanRequest->due_date->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Loan Details -->
                        <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-6">
                            <h3 class="text-xl font-semibold text-white mb-6">Detail Pengajuan</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-neutral-400 mb-2">Deskripsi Tujuan</label>
                                <div class="bg-neutral-800 rounded-lg p-4">
                                    <p class="text-neutral-300">{{ $loanRequest->purpose_description }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Documents -->
                        @if($loanRequest->documents && count($loanRequest->documents) > 0)
                            <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-6">
                                <h3 class="text-xl font-semibold text-white mb-6">Dokumen yang Diunggah</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($loanRequest->documents as $index => $document)
                                        <div class="bg-neutral-800 rounded-lg p-4 flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center mr-3">
                                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-white">{{ $document['name'] }}</p>
                                                    <p class="text-xs text-neutral-400">{{ number_format($document['size'] / 1024) }} KB</p>
                                                </div>
                                            </div>
                                            <a href="{{ Storage::url($document['path']) }}" target="_blank"
                                               class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                Lihat
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Timeline -->
                        <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-6">
                            <h3 class="text-xl font-semibold text-white mb-6">Timeline Pinjaman</h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <div class="w-3 h-3 bg-blue-400 rounded-full mt-2 mr-4"></div>
                                    <div>
                                        <p class="text-sm font-medium text-white">Pengajuan dibuat</p>
                                        <p class="text-xs text-neutral-400">{{ $loanRequest->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                </div>
                                
                                @if($loanRequest->status !== 'pending')
                                    <div class="flex items-start">
                                        <div class="w-3 h-3 bg-yellow-400 rounded-full mt-2 mr-4"></div>
                                        <div>
                                            <p class="text-sm font-medium text-white">Status diperbarui: {{ $loanRequest->status_label }}</p>
                                            <p class="text-xs text-neutral-400">{{ $loanRequest->updated_at->format('d M Y H:i') }}</p>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($loanRequest->approved_at)
                                    <div class="flex items-start">
                                        <div class="w-3 h-3 bg-green-400 rounded-full mt-2 mr-4"></div>
                                        <div>
                                            <p class="text-sm font-medium text-white">Pengajuan disetujui</p>
                                            <p class="text-xs text-neutral-400">{{ $loanRequest->approved_at->format('d M Y H:i') }}</p>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($loanRequest->disbursed_at)
                                    <div class="flex items-start">
                                        <div class="w-3 h-3 bg-purple-400 rounded-full mt-2 mr-4"></div>
                                        <div>
                                            <p class="text-sm font-medium text-white">Dana dicairkan</p>
                                            <p class="text-xs text-neutral-400">{{ $loanRequest->disbursed_at->format('d M Y H:i') }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Quick Actions -->
                        @if($loanRequest->status === 'pending')
                            <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-6">
                                <h3 class="text-lg font-semibold text-white mb-4">Aksi Tersedia</h3>
                                
                                <div class="space-y-3">
                                    <a href="{{ route('user.loan-requests.edit', $loanRequest) }}" 
                                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit Pengajuan
                                    </a>
                                    
                                    <form method="POST" action="{{ route('user.loan-requests.destroy', $loanRequest) }}" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan pinjaman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Batalkan Pengajuan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif

                        <!-- Admin Notes -->
                        @if($loanRequest->admin_notes || $loanRequest->rejection_reason)
                            <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-6">
                                <h3 class="text-lg font-semibold text-white mb-4">Catatan dari Tim</h3>
                                
                                @if($loanRequest->admin_notes)
                                    <div class="bg-neutral-800 rounded-lg p-4 mb-4">
                                        <p class="text-neutral-300 text-sm">{{ $loanRequest->admin_notes }}</p>
                                    </div>
                                @endif
                                
                                @if($loanRequest->rejection_reason)
                                    <div class="bg-red-900/20 border border-red-600/30 rounded-lg p-4">
                                        <h4 class="text-sm font-medium text-red-400 mb-2">Alasan Penolakan:</h4>
                                        <p class="text-red-300 text-sm">{{ $loanRequest->rejection_reason }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Loan Calculator Summary -->
                        <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-6">
                            <h3 class="text-lg font-semibold text-white mb-4">Ringkasan Perhitungan</h3>
                            
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-neutral-400">Pokok Pinjaman:</span>
                                    <span class="text-white font-medium">{{ $loanRequest->formatted_amount }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-400">Suku Bunga:</span>
                                    <span class="text-white font-medium">{{ number_format($loanRequest->interest_rate, 2) }}%/tahun</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-400">Jangka Waktu:</span>
                                    <span class="text-white font-medium">{{ $loanRequest->duration_months }} bulan</span>
                                </div>
                                <hr class="border-neutral-700">
                                <div class="flex justify-between">
                                    <span class="text-neutral-400">Cicilan/bulan:</span>
                                    <span class="text-[#25F4EE] font-bold">{{ $loanRequest->formatted_monthly_payment }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-400">Total Pembayaran:</span>
                                    <span class="text-white font-medium">Rp {{ number_format($loanRequest->monthly_payment * $loanRequest->duration_months) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Help & Support -->
                        <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-6">
                            <h3 class="text-lg font-semibold text-white mb-4">Butuh Bantuan?</h3>
                            
                            <p class="text-neutral-400 text-sm mb-4">
                                Jika Anda memiliki pertanyaan mengenai status pengajuan pinjaman, silakan hubungi tim customer service kami.
                            </p>
                            
                            <a href="#" class="inline-flex items-center text-[#25F4EE] text-sm hover:text-[#25F4EE]/80 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.405L3 21l1.405-5.094A8.959 8.959 0 013 12a8 8 0 1118 0z"></path>
                                </svg>
                                Chat dengan Customer Service
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
