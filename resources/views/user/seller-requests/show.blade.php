<x-app-layout>
    <div class="min-h-screen bg-[#0f1115] text-gray-200 relative overflow-hidden">
        <!-- background accents -->
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_25%_15%,rgba(236,72,153,0.07),transparent_60%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_85%,rgba(59,130,246,0.08),transparent_65%)]"></div>
        <!-- Header / Toolbar -->
        <div class="sticky top-0 z-40 backdrop-blur-md bg-[#0f1115]/70 border-b border-white/10">
            <div class="px-4 py-3 flex items-center gap-3">
                <a href="{{ route('seller-requests.index') }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:bg-white/5 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60" aria-label="Kembali">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-semibold text-white leading-tight">Detail Permintaan Seller</h1>
                    <p class="text-[11px] text-gray-500 mt-0.5">Detail pengajuan menjadi seller.</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 py-6">
            <div class="max-w-3xl mx-auto space-y-8">
                <!-- Status Card -->
                <div class="bg-[#181d23] border border-white/10 rounded-2xl p-6 relative overflow-hidden">
                    <div class="absolute inset-0 pointer-events-none opacity-0 hover:opacity-100 transition bg-gradient-to-br from-fuchsia-500/10 via-transparent to-cyan-500/10"></div>
                    <div class="flex items-start justify-between gap-8 relative z-10">
                        <div class="flex-1 space-y-5">
                            <div>
                                <h2 class="text-lg font-semibold text-white mb-1">Informasi Pengajuan</h2>
                                <p class="text-[11px] text-gray-500">Status & data lengkap pengajuan Anda.</p>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center gap-4 text-sm">
                                    <span class="text-gray-400 w-28">Status:</span>
                                    @if($sellerRequest->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[11px] font-semibold rounded-full bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                            Menunggu Review
                                        </span>
                                    @elseif($sellerRequest->status === 'approved')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[11px] font-semibold rounded-full bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            Disetujui
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[11px] font-semibold rounded-full bg-red-500/15 text-red-300 border border-red-500/30">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                            Ditolak
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-4 text-sm">
                                    <span class="text-gray-400 w-28">Nama Toko:</span>
                                    <span class="font-medium text-gray-200">{{ $sellerRequest->store_name }}</span>
                                </div>
                                <div class="flex items-center gap-4 text-sm">
                                    <span class="text-gray-400 w-28">Kode Undangan:</span>
                                    <span class="font-mono bg-[#1f252c] border border-white/5 px-2 py-0.5 rounded text-fuchsia-300 tracking-wide">{{ $sellerRequest->invite_code }}</span>
                                </div>
                                <div class="flex items-start gap-4 text-sm">
                                    <span class="text-gray-400 w-28">Deskripsi:</span>
                                    <span class="text-gray-300 flex-1 leading-relaxed">{{ $sellerRequest->description ?: 'Tidak ada deskripsi' }}</span>
                                </div>
                                <div class="flex items-center gap-4 text-sm">
                                    <span class="text-gray-400 w-28">Tanggal:</span>
                                    <span class="text-gray-300">{{ $sellerRequest->created_at->format('d M Y H:i') }}</span>
                                </div>
                                @if($sellerRequest->admin_notes)
                                    <div class="p-4 rounded-xl bg-[#1f252c] border border-white/5">
                                        <p class="text-[11px] font-semibold text-gray-400 mb-1">Catatan Admin:</p>
                                        <p class="text-sm text-gray-300 leading-relaxed">{{ $sellerRequest->admin_notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="shrink-0">
                            @if($sellerRequest->status === 'pending')
                                <div class="w-14 h-14 bg-amber-500/15 border border-amber-500/30 rounded-full flex items-center justify-center">
                                    <svg class="w-7 h-7 text-amber-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                </div>
                            @elseif($sellerRequest->status === 'approved')
                                <div class="w-14 h-14 bg-emerald-500/15 border border-emerald-500/30 rounded-full flex items-center justify-center">
                                    <svg class="w-7 h-7 text-emerald-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                </div>
                            @else
                                <div class="w-14 h-14 bg-red-500/15 border border-red-500/30 rounded-full flex items-center justify-center">
                                    <svg class="w-7 h-7 text-red-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Retry -->
                @if($sellerRequest->status === 'rejected')
                    <div class="bg-[#181d23] border border-white/10 rounded-2xl p-6">
                        <h3 class="text-lg font-semibold text-white mb-2">Ajukan Ulang</h3>
                        <p class="text-sm text-gray-400 mb-5 leading-relaxed">Pengajuan Anda ditolak. Anda dapat mengajukan ulang dengan memperbaiki data yang diperlukan.</p>
                        <a href="{{ route('seller-requests.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium bg-gradient-to-r from-fuchsia-500 via-rose-500 to-cyan-500 text-white hover:from-fuchsia-500/90 hover:via-rose-500/90 hover:to-cyan-500/90 shadow-sm shadow-fuchsia-500/30 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                            Ajukan Ulang
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
