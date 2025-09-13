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
                <h1 class="text-base font-semibold text-white leading-tight line-clamp-1">Edit Showcase</h1>
                <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1">Ubah pengaturan produk di etalase Anda.</p>
            </div>
        </div>

        <!-- Product Info -->
        <div class="relative z-10">
            <div class="rounded-2xl border border-white/10 bg-[#181d23] p-6">
                <div class="flex items-start gap-4">
                    <div class="w-24 h-24 rounded-xl bg-[#1f252c] border border-white/5 flex items-center justify-center overflow-hidden flex-shrink-0">
                        @if($showcase->product->image_url)
                            <img src="{{ $showcase->product->image_url }}" alt="{{ $showcase->product->name }}" class="object-cover w-full h-full">
                        @else
                            <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-white mb-1">{{ $showcase->product->name }}</h3>
                        <p class="text-sm text-gray-400 mb-2">{{ $showcase->product->category->name }}</p>
                        <p class="text-lg font-semibold bg-clip-text text-transparent bg-gradient-to-r from-fuchsia-400 via-rose-400 to-cyan-400">
                            Harga Jual: Rp {{ number_format($showcase->product->harga_jual, 0, ',', '.') }}
                        </p>
                        <div class="flex items-center gap-4 mt-3">
                            <span class="px-3 py-1 text-xs font-medium rounded-full {{ $showcase->is_active ? 'bg-emerald-500/20 text-emerald-300' : 'bg-gray-500/20 text-gray-400' }}">
                                {{ $showcase->is_active ? 'Aktif' : 'Draft' }}
                            </span>
                            @if($showcase->is_featured)
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-amber-500/20 text-amber-300">
                                    Featured
                                    @if($showcase->featured_until)
                                        sampai {{ $showcase->featured_until->format('d M Y') }}
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="relative z-10">
            <form action="{{ route('user.showcases.update', $showcase) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="rounded-2xl border border-white/10 bg-[#181d23] p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Active Toggle -->
                        <div>
                            <label class="flex items-start gap-3">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $showcase->is_active) ? 'checked' : '' }}
                                       class="mt-1 text-fuchsia-500 focus:ring-fuchsia-500/60 bg-[#1b1f25] border-white/10 rounded">
                                <div>
                                    <span class="block text-sm font-medium text-gray-200">Aktif</span>
                                    <span class="text-xs text-gray-500">Tampilkan produk ini di etalase publik</span>
                                </div>
                            </label>
                        </div>

                        <!-- Featured Toggle -->
                        <div>
                            <label class="flex items-start gap-3">
                                <input type="checkbox" 
                                       name="is_featured" 
                                       value="1" 
                                       {{ old('is_featured', $showcase->is_featured) ? 'checked' : '' }}
                                       class="mt-1 text-amber-500 focus:ring-amber-500/60 bg-[#1b1f25] border-white/10 rounded">
                                <div>
                                    <span class="block text-sm font-medium text-gray-200">Produk Featured</span>
                                    <span class="text-xs text-gray-500">Tampilkan produk ini sebagai featured</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Featured Until -->
                    <div class="featured-until-section">
                        <label for="featured_until" class="block text-sm font-medium text-gray-200 mb-2">Featured Sampai</label>
                        <input type="datetime-local" 
                               id="featured_until" 
                               name="featured_until" 
                               value="{{ old('featured_until', $showcase->featured_until?->format('Y-m-d\TH:i')) }}"
                               class="w-full px-4 py-2 rounded-lg bg-[#1b1f25] border border-white/10 text-gray-200 focus:border-amber-500/60 focus:ring-amber-500/60 focus:outline-none transition">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan untuk featured tanpa batas waktu</p>
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-200 mb-2">Urutan Tampil</label>
                        <input type="number" 
                               id="sort_order" 
                               name="sort_order" 
                               min="1"
                               value="{{ old('sort_order', $showcase->sort_order) }}"
                               class="w-full px-4 py-2 rounded-lg bg-[#1b1f25] border border-white/10 text-gray-200 focus:border-fuchsia-500/60 focus:ring-fuchsia-500/60 focus:outline-none transition">
                        <p class="text-xs text-gray-500 mt-1">Angka yang lebih kecil akan tampil di urutan atas</p>
                    </div>

                    @if ($errors->any())
                        <div class="rounded-xl border border-red-500/20 bg-red-500/10 p-4">
                            <div class="text-sm text-red-400">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center gap-4">
                        <button type="submit" class="flex-1 py-3 rounded-xl text-sm font-medium text-white bg-gradient-to-r from-fuchsia-500 via-rose-500 to-cyan-500 hover:from-fuchsia-500/90 hover:via-rose-500/90 hover:to-cyan-500/90 shadow-sm shadow-fuchsia-500/30 flex items-center justify-center gap-2 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('user.showcases.index') }}" class="px-6 py-3 rounded-xl text-sm font-medium text-gray-300 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 transition focus:outline-none focus:ring-2 focus:ring-white/20">
                            Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Danger Zone -->
        <div class="relative z-10">
            <div class="rounded-2xl border border-red-500/20 bg-red-500/5 p-6">
                <h3 class="text-lg font-medium text-red-400 mb-2">Hapus dari Etalase</h3>
                <p class="text-sm text-gray-400 mb-4">Produk ini akan dihapus dari etalase Anda. Produk akan tetap tersedia di katalog utama.</p>
                <form action="{{ route('user.showcases.destroy', $showcase) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini dari etalase?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium text-red-400 bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 hover:border-red-500/30 transition focus:outline-none focus:ring-2 focus:ring-red-500/60">
                        Hapus dari Etalase
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const featuredCheckbox = document.querySelector('input[name="is_featured"]');
            const featuredUntilSection = document.querySelector('.featured-until-section');
            
            function toggleFeaturedUntil() {
                if (featuredUntilSection) {
                    featuredUntilSection.style.display = featuredCheckbox.checked ? 'block' : 'none';
                }
            }
            
            if (featuredCheckbox) {
                featuredCheckbox.addEventListener('change', toggleFeaturedUntil);
                toggleFeaturedUntil(); // Initial state
            }
        });
    </script>
</x-app-layout>
