<x-app-layout>
    <div class="min-h-screen bg-[#0f1115] text-gray-200 px-4 py-6 space-y-10 relative overflow-hidden">
        <!-- background accents -->
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_25%_15%,rgba(236,72,153,0.07),transparent_60%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_85%,rgba(59,130,246,0.08),transparent_65%)]"></div>

        <!-- Header -->
        <div class="flex items-center gap-3 relative z-10">
            <a href="{{ route('user.showcases.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:bg-white/5 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60" aria-label="Kembali">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <div class="flex-1 min-w-0">
                <h1 class="text-base font-semibold text-white leading-tight line-clamp-1">Detail Etalase</h1>
                <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1">Informasi lengkap produk di etalase Anda.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('user.showcases.edit', $showcase) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-white/5 text-gray-300 hover:bg-white/10 border border-white/10 hover:border-white/20 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                    Edit
                </a>
                <form action="{{ route('user.showcases.destroy', $showcase) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus produk dari etalase?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-red-500/10 text-red-400 hover:bg-red-500/20 border border-red-500/20 hover:border-red-500/30 transition focus:outline-none focus:ring-2 focus:ring-red-500/60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        Hapus
                    </button>
                </form>
            </div>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="relative z-10">
                <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-4">
                    <p class="text-sm text-emerald-400 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Main Content -->
        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Product Image and Gallery -->
            <div class="space-y-4">
                <!-- Main Image -->
                <div class="aspect-square w-full bg-[#181d23] border border-white/10 rounded-2xl flex items-center justify-center overflow-hidden group relative">
                    @if($showcase->product->image_url)
                        <img src="{{ $showcase->product->image_url }}" alt="{{ $showcase->product->name }}" class="object-cover w-full h-full">
                    @else
                        <svg class="w-16 h-16 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    @endif
                    
                    <!-- Status Badges -->
                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                        @if($showcase->is_featured && $showcase->is_featured_active)
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-amber-500 text-white shadow-lg">
                                <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                Featured
                            </span>
                        @endif
                        @if($showcase->is_active)
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-emerald-500 text-white shadow-lg">
                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Aktif
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-600 text-white shadow-lg">
                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                Draft
                            </span>
                        @endif
                    </div>

                    <!-- Quick Actions -->
                    <div class="absolute top-4 right-4 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition">
                        <button type="button" onclick="toggleActive({{ $showcase->id }})" class="w-10 h-10 rounded-xl {{ $showcase->is_active ? 'bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30' : 'bg-gray-500/20 text-gray-400 hover:bg-gray-500/30' }} backdrop-blur-sm flex items-center justify-center transition shadow-lg" title="Toggle Active">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </button>
                        <button type="button" onclick="toggleFeatured({{ $showcase->id }})" class="w-10 h-10 rounded-xl {{ $showcase->is_featured ? 'bg-amber-500/20 text-amber-400 hover:bg-amber-500/30' : 'bg-gray-500/20 text-gray-400 hover:bg-gray-500/30' }} backdrop-blur-sm flex items-center justify-center transition shadow-lg" title="Toggle Featured">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Details -->
            <div class="space-y-6">
                <!-- Basic Info Card -->
                <div class="rounded-2xl border border-white/10 bg-[#181d23] p-6">
                    <div class="space-y-4">
                        <div>
                            <h2 class="text-xl font-semibold text-white mb-2">{{ $showcase->product->name }}</h2>
                            <p class="text-sm text-gray-400 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                {{ $showcase->product->category->name }}
                            </p>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between py-2 border-b border-white/5">
                                <span class="text-sm text-gray-400">Harga Modal</span>
                                <span class="text-sm text-gray-300">Rp {{ number_format($showcase->product->harga_beli, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-white/5">
                                <span class="text-sm text-gray-400">Harga Jual</span>
                                <span class="text-lg font-semibold bg-clip-text text-transparent bg-gradient-to-r from-fuchsia-400 via-rose-400 to-cyan-400">
                                    Rp {{ number_format($showcase->product->harga_jual, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-white/5">
                                <span class="text-sm text-gray-400">Margin Seller</span>
                                @php 
                                    $user = auth()->user();
                                    $marginPercent = $user->getLevelMarginPercent();
                                    $levelBadge = $user->getLevelBadge();
                                    
                                    if($marginPercent) {
                                        $sellerMargin = round($showcase->product->harga_jual * ($marginPercent / 100));
                                    } else {
                                        $sellerMargin = max(0, $showcase->product->harga_jual - $showcase->product->harga_biasa);
                                    }
                                @endphp
                                <div class="text-right">
                                    <span class="text-sm font-medium text-emerald-400">
                                        Rp {{ number_format($sellerMargin, 0, ',', '.') }}
                                    </span>
                                    <div class="flex items-center gap-2 justify-end mt-1">
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-600/20 text-emerald-300 border border-emerald-500/30 text-[10px] font-medium">{{ $levelBadge }}</span>
                                        @if($marginPercent)
                                            <span class="text-[10px] text-emerald-400">{{ $marginPercent }}% dari harga jual</span>
                                        @else
                                            <span class="text-[10px] text-emerald-400">Margin admin</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-400">Stok Tersedia</span>
                                <span class="text-sm {{ $showcase->product->stok > 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                    {{ $showcase->product->stok }} unit
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Showcase Settings Card -->
                <div class="rounded-2xl border border-white/10 bg-[#181d23] p-6">
                    <h3 class="text-lg font-medium text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Pengaturan Etalase
                    </h3>
                    
                    <div class="space-y-4">
                        @if($showcase->custom_title)
                            <div class="flex items-start justify-between py-2 border-b border-white/5">
                                <span class="text-sm text-gray-400">Judul Custom</span>
                                <span class="text-sm text-gray-300 text-right max-w-xs">{{ $showcase->custom_title }}</span>
                            </div>
                        @endif

                        @if($showcase->custom_description)
                            <div class="flex items-start justify-between py-2 border-b border-white/5">
                                <span class="text-sm text-gray-400">Deskripsi</span>
                                <span class="text-sm text-gray-300 text-right max-w-xs leading-relaxed">{{ $showcase->custom_description }}</span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between py-2 border-b border-white/5">
                            <span class="text-sm text-gray-400">Urutan</span>
                            <span class="text-sm text-gray-300">{{ $showcase->display_order ?? 'Otomatis' }}</span>
                        </div>

                        <div class="flex items-center justify-between py-2 border-b border-white/5">
                            <span class="text-sm text-gray-400">Status</span>
                            <span class="text-sm {{ $showcase->is_active ? 'text-emerald-400' : 'text-gray-400' }}">
                                {{ $showcase->is_active ? 'Aktif' : 'Non-aktif' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-400">Featured</span>
                            <span class="text-sm {{ $showcase->is_featured ? 'text-amber-400' : 'text-gray-400' }}">
                                {{ $showcase->is_featured ? 'Ya' : 'Tidak' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Timestamps Card -->
                <div class="rounded-2xl border border-white/10 bg-[#181d23] p-6">
                    <h3 class="text-lg font-medium text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Informasi Waktu
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-white/5">
                            <span class="text-sm text-gray-400">Ditambahkan</span>
                            <span class="text-sm text-gray-300">{{ $showcase->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        @if($showcase->updated_at && $showcase->updated_at != $showcase->created_at)
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-400">Terakhir Update</span>
                                <span class="text-sm text-gray-300">{{ $showcase->updated_at->format('d M Y, H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description (if available) -->
        @if($showcase->product->deskripsi)
            <div class="relative z-10">
                <div class="rounded-2xl border border-white/10 bg-[#181d23] p-6">
                    <h3 class="text-lg font-medium text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Deskripsi Produk
                    </h3>
                    <div class="prose prose-invert prose-sm max-w-none">
                        <p class="text-gray-300 leading-relaxed">{{ $showcase->product->deskripsi }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- JavaScript for AJAX actions -->
    <script>
        function toggleActive(showcaseId) {
            fetch(`/etalase/${showcaseId}/toggle-active`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal mengubah status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            });
        }

        function toggleFeatured(showcaseId) {
            fetch(`/etalase/${showcaseId}/toggle-featured`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal mengubah status featured');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            });
        }
    </script>
</x-app-layout>
