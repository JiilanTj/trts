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
                    <h1 class="text-base font-semibold text-white leading-tight">Ajukan Menjadi Seller</h1>
                    <p class="text-[11px] text-gray-500 mt-0.5">Isi formulir untuk mengajukan diri menjadi seller.</p>
                </div>
            </div>
        </div>

        <!-- Flash Errors -->
        @if($errors->any())
            <div class="px-4 pt-4">
                <div class="bg-red-500/10 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Form -->
        <div class="px-4 py-6">
            <form method="POST" action="{{ route('seller-requests.store') }}" class="max-w-2xl mx-auto relative z-10">
                @csrf
                <div class="bg-[#181d23] border border-white/10 rounded-2xl p-6 space-y-8 shadow-sm">
                    <div>
                        <h2 class="text-lg font-semibold text-white mb-1">Informasi Toko</h2>
                        <p class="text-sm text-gray-400">Lengkapi data toko yang akan Anda buat.</p>
                    </div>

                    <!-- Store Name -->
                    <div>
                        <label for="store_name" class="block text-sm font-medium text-gray-300 mb-2">Nama Toko <span class="text-red-400">*</span></label>
                        <input type="text" id="store_name" name="store_name" value="{{ old('store_name') }}" placeholder="Masukkan nama toko Anda" required class="w-full px-4 py-3 text-sm rounded-xl border {{ $errors->has('store_name') ? 'border-red-400 bg-red-500/10 focus:border-red-400 focus:ring-red-500/40' : 'border-white/10 bg-[#1b1f25] hover:bg-[#242a32] focus:border-fuchsia-500 focus:ring-fuchsia-500/40' }} focus:ring-2 focus:outline-none text-gray-200 placeholder-gray-500 transition" />
                        @error('store_name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <!-- Invitation Code -->
                    <div>
                        <label for="invite_code" class="block text-sm font-medium text-gray-300 mb-2">Kode Undangan <span class="text-red-400">*</span></label>
                        <input type="text" id="invite_code" name="invite_code" value="{{ old('invite_code') }}" placeholder="Masukkan kode undangan yang valid" required class="w-full px-4 py-3 text-sm font-mono tracking-wide rounded-xl border {{ $errors->has('invite_code') ? 'border-red-400 bg-red-500/10 focus:border-red-400 focus:ring-red-500/40' : 'border-white/10 bg-[#1b1f25] hover:bg-[#242a32] focus:border-fuchsia-500 focus:ring-fuchsia-500/40' }} focus:ring-2 focus:outline-none text-fuchsia-300 placeholder-gray-500 transition uppercase" />
                        @error('invite_code')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                        <p class="mt-1 text-[11px] text-gray-500">Dapatkan kode undangan dari admin atau seller lain.</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Deskripsi Toko</label>
                        <textarea id="description" name="description" rows="4" placeholder="Ceritakan tentang toko yang akan Anda buat..." class="w-full px-4 py-3 text-sm rounded-xl border {{ $errors->has('description') ? 'border-red-400 bg-red-500/10 focus:border-red-400 focus:ring-red-500/40' : 'border-white/10 bg-[#1b1f25] hover:bg-[#242a32] focus:border-fuchsia-500 focus:ring-fuchsia-500/40' }} focus:ring-2 focus:outline-none text-gray-200 placeholder-gray-500 transition resize-none">{{ old('description') }}</textarea>
                        @error('description')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                        <p class="mt-1 text-[11px] text-gray-500">Opsional. Jelaskan jenis produk yang akan dijual.</p>
                    </div>

                    <!-- Info Box -->
                    <div class="rounded-xl p-5 bg-[#1b1f25] border border-white/10 relative overflow-hidden">
                        <div class="absolute inset-0 pointer-events-none opacity-0 group-hover:opacity-100 transition bg-gradient-to-br from-fuchsia-500/10 via-transparent to-cyan-500/10"></div>
                        <div class="flex gap-4">
                            <div class="shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-fuchsia-600/30 to-cyan-500/30 flex items-center justify-center border border-white/10">
                                <svg class="w-5 h-5 text-fuchsia-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-white mb-2">Informasi Penting</h4>
                                <ul class="text-[11px] text-gray-400 space-y-1 leading-relaxed">
                                    <li>• Review 1-3 hari kerja oleh admin</li>
                                    <li>• Kode undangan harus valid & aktif</li>
                                    <li>• Setelah disetujui Anda bisa mulai jualan</li>
                                    <li>• Nama toko harus unik</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="flex-1 rounded-xl py-3 px-6 text-sm font-medium text-white bg-gradient-to-r from-fuchsia-500 via-rose-500 to-cyan-500 hover:from-fuchsia-500/90 hover:via-rose-500/90 hover:to-cyan-500/90 shadow-sm shadow-fuchsia-500/30 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                            Kirim Pengajuan
                        </button>
                        <a href="{{ route('seller-requests.index') }}" class="px-6 py-3 rounded-xl text-sm font-medium bg-[#1b1f25] border border-white/10 text-gray-400 hover:text-white hover:bg-[#242a32] focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60 transition">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
