<x-app-layout>
    <div class="min-h-screen bg-[#0f1115] text-gray-200 relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_25%_15%,rgba(6,182,212,0.07),transparent_60%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_85%,rgba(59,130,246,0.08),transparent_65%)]"></div>
        
        <!-- Header -->
        <div class="sticky top-0 z-40 backdrop-blur-md bg-[#0f1115]/70 border-b border-white/10">
            <div class="px-4 py-3 flex items-center gap-3">
                <a href="{{ route('user.topup.index') }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:bg-white/5 transition focus:outline-none focus:ring-2 focus:ring-cyan-500/60" aria-label="Kembali">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-semibold text-white leading-tight">Detail Topup #{{ $topupRequest->id }}</h1>
                    <p class="text-[11px] text-gray-500 mt-0.5">{{ $topupRequest->created_at->format('d M Y H:i') }}</p>
                </div>
                @php
                    $statusColors = [
                        'pending' => 'bg-amber-600/20 text-amber-300 border-amber-500/30',
                        'approved' => 'bg-emerald-600/20 text-emerald-300 border-emerald-500/30',
                        'rejected' => 'bg-red-600/20 text-red-300 border-red-500/30',
                    ];
                    $statusLabels = [
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ];
                @endphp
                <div class="px-2 py-1 text-[10px] rounded-lg font-medium border {{ $statusColors[$topupRequest->status] }}">
                    {{ $statusLabels[$topupRequest->status] }}
                </div>
            </div>
        </div>

        <div class="px-3 sm:px-4 py-4 sm:py-6 max-w-4xl mx-auto space-y-6">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-emerald-600/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-600/10 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
            @endif

            <!-- Status Info -->
            <div class="bg-[#181d23] border border-white/10 rounded-xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    @if($topupRequest->status === 'pending')
                        <div class="w-8 h-8 rounded-lg bg-amber-600/20 border border-amber-500/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    @elseif($topupRequest->status === 'approved')
                        <div class="w-8 h-8 rounded-lg bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    @else
                        <div class="w-8 h-8 rounded-lg bg-red-600/20 border border-red-500/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    @endif
                    <div>
                        <h3 class="text-lg font-semibold text-white">Status Permintaan</h3>
                        <p class="text-sm text-gray-400">
                            @if($topupRequest->status === 'pending')
                                Permintaan Anda sedang dalam proses review admin
                            @elseif($topupRequest->status === 'approved')
                                Permintaan topup telah disetujui pada {{ $topupRequest->approved_at->format('d M Y H:i') }}
                            @else
                                Permintaan topup ditolak pada {{ $topupRequest->rejected_at->format('d M Y H:i') }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Details Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Transfer Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Amount Info -->
                    <div class="bg-[#181d23] border border-white/10 rounded-xl p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-cyan-500/20 to-blue-500/20 border border-cyan-500/30 flex items-center justify-center">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">Informasi Topup</h3>
                                <p class="text-xs text-gray-400">Detail permintaan topup saldo</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Jumlah Topup</label>
                                <div class="text-2xl font-bold text-white">{{ $topupRequest->formatted_amount }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                                <div class="inline-flex px-2 py-1 text-xs font-medium rounded-lg {{ $statusColors[$topupRequest->status] }}">
                                    {{ $statusLabels[$topupRequest->status] }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Permintaan</label>
                                <div class="text-sm text-gray-300">{{ $topupRequest->created_at->format('d M Y H:i') }}</div>
                            </div>
                            @if($topupRequest->status === 'approved')
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Disetujui Pada</label>
                                    <div class="text-sm text-emerald-400">{{ $topupRequest->approved_at->format('d M Y H:i') }}</div>
                                </div>
                            @elseif($topupRequest->status === 'rejected')
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Ditolak Pada</label>
                                    <div class="text-sm text-red-400">{{ $topupRequest->rejected_at->format('d M Y H:i') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Bank Details -->
                    <div class="bg-[#181d23] border border-white/10 rounded-xl p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500/20 to-indigo-500/20 border border-blue-500/30 flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">Informasi Bank</h3>
                                <p class="text-xs text-gray-400">Detail bank tujuan transfer</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Bank Tujuan</label>
                                <div class="text-sm font-medium text-white">{{ $topupRequest->bank_name }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Nomor Rekening</label>
                                <div class="text-sm text-gray-300 font-mono">{{ $topupRequest->bank_account }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Transfer</label>
                                <div class="text-sm text-gray-300">{{ $topupRequest->transfer_date ? $topupRequest->transfer_date->format('d M Y H:i') : '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($topupRequest->notes || $topupRequest->admin_notes)
                        <div class="bg-[#181d23] border border-white/10 rounded-xl p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500/20 to-pink-500/20 border border-purple-500/30 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-white">Catatan</h3>
                                    <p class="text-xs text-gray-400">Catatan dan keterangan tambahan</p>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                @if($topupRequest->notes)
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Catatan Anda</label>
                                        <div class="text-sm text-gray-300 bg-[#1a1f25] rounded-lg p-3">{{ $topupRequest->notes }}</div>
                                    </div>
                                @endif
                                @if($topupRequest->admin_notes)
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Catatan Admin</label>
                                        <div class="text-sm text-gray-300 bg-[#1a1f25] rounded-lg p-3">{{ $topupRequest->admin_notes }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column: Payment Proof -->
                <div class="lg:col-span-1">
                    <div class="bg-[#181d23] border border-white/10 rounded-xl p-6 sticky top-24">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-green-500/20 to-emerald-500/20 border border-green-500/30 flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">Bukti Transfer</h3>
                                <p class="text-xs text-gray-400">File yang Anda upload</p>
                            </div>
                        </div>
                        
                        @if($topupRequest->payment_proof)
                            <div class="space-y-4">
                                @php
                                    $fileExtension = pathinfo($topupRequest->payment_proof, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']);
                                @endphp
                                
                                @if($isImage)
                                    <div class="relative group">
                                        <img src="{{ Storage::url($topupRequest->payment_proof) }}" 
                                             alt="Bukti Transfer" 
                                             class="w-full h-auto rounded-lg border border-white/10 cursor-pointer"
                                             onclick="openImageModal(this.src)">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 rounded-lg cursor-pointer flex items-center justify-center">
                                            <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center justify-center h-32 bg-[#1a1f25] rounded-lg border border-white/10">
                                        <div class="text-center">
                                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <div class="text-sm text-gray-400">File PDF</div>
                                        </div>
                                    </div>
                                @endif
                                
                                <a href="{{ Storage::url($topupRequest->payment_proof) }}" 
                                   target="_blank" 
                                   class="block w-full bg-gradient-to-r from-cyan-500/20 to-blue-500/20 border border-cyan-500/30 text-cyan-300 text-center py-3 px-4 rounded-lg text-sm font-medium hover:from-cyan-500/30 hover:to-blue-500/30 hover:border-cyan-500/50 transition">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download Bukti
                                </a>
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-400">
                                <svg class="w-12 h-12 text-gray-500 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <p class="text-sm">Tidak ada bukti transfer</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50" onclick="closeImageModal()">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative max-w-4xl max-h-full">
                <img id="modalImage" src="" alt="Bukti Transfer" class="max-w-full max-h-full object-contain rounded-lg">
                <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 bg-black bg-opacity-50 rounded-full p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
</x-app-layout>
