<x-app-layout>
    <div class="min-h-screen bg-[#0f1115] text-gray-200 relative overflow-hidden">
        <!-- subtle gradient / vignette -->
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(59,130,246,0.07),transparent_60%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_70%_80%,rgba(236,72,153,0.06),transparent_65%)]"></div>
        <!-- Header / Toolbar -->
        <div class="sticky top-0 z-30 backdrop-blur-md bg-[#0f1115]/70 border-b border-white/10">
            <div class="px-4 py-3 flex items-center gap-3">
                <a href="{{ url()->previous() }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:bg-white/5 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60" aria-label="Kembali">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-semibold text-white leading-tight">Kategori Produk</h1>
                    <p class="text-[11px] text-gray-500 mt-0.5">Daftar kategori aktif.</p>
                </div>
                <a href="{{ route('browse.products.index') }}" class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-gradient-to-r from-fuchsia-500 via-rose-500 to-cyan-500 text-white hover:from-fuchsia-500/90 hover:via-rose-500/90 hover:to-cyan-500/90 shadow-sm shadow-fuchsia-500/30 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h4l2 3h6l2-3h1a1 1 0 011 1v15a1 1 0 01-1 1H4a1 1 0 01-1-1V4z" /></svg>
                    Semua Produk
                </a>
            </div>
            <!-- Search Bar -->
            <div class="px-4 pb-3 border-t border-white/5">
                <div class="flex gap-3 items-center">
                    <div class="flex-1 relative">
                        <input id="category-search" type="text" placeholder="Cari kategori" class="peer w-full pl-10 pr-4 py-2.5 text-sm rounded-xl bg-[#1b1f25] border border-white/10 focus:border-fuchsia-500 focus:ring-2 focus:ring-fuchsia-500/30 text-gray-200 placeholder-gray-500 hover:bg-[#242a32] transition" aria-label="Pencarian kategori">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 peer-focus:text-fuchsia-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" /></svg>
                    </div>
                    <button id="clear-search" class="hidden shrink-0 px-3 py-2 text-xs font-medium text-gray-300 bg-[#1b1f25] border border-white/10 hover:bg-[#242a32] rounded-lg focus:outline-none focus:ring-2 focus:ring-fuchsia-500/50" type="button">Reset</button>
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="px-4 pt-5">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="rounded-xl p-4 bg-[#181d23] border border-white/5">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Kategori Aktif (Total)</p>
                    <p class="mt-1 text-lg font-semibold text-white">{{ $categories->total() }}</p>
                </div>
                <div class="rounded-xl p-4 bg-[#181d23] border border-white/5">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Halaman</p>
                    <p class="mt-1 text-lg font-semibold text-white">{{ $categories->currentPage() }} / {{ $categories->lastPage() }}</p>
                </div>
                <div class="rounded-xl p-4 bg-[#181d23] border border-white/5">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Kategori (Halaman Ini)</p>
                    <p class="mt-1 text-lg font-semibold text-white">{{ $categories->count() }}</p>
                </div>
                <div class="rounded-xl p-4 bg-[#181d23] border border-white/5">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Produk (Halaman Ini)</p>
                    <p class="mt-1 text-lg font-semibold text-white">{{ $categories->sum('products_count') }}</p>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="px-4 mt-6">
            <div class="h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
        </div>

        <!-- Categories Grid -->
        <div class="px-4 py-6" x-data="categoryFilter()">
            @if($categories->count())
                <div id="categories-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach($categories as $category)
                        <a href="{{ route('browse.categories.show', $category) }}" class="category-card group relative rounded-2xl p-4 bg-[#181d23] border border-white/5 hover:border-fuchsia-500/60 transition flex flex-col overflow-hidden focus:outline-none focus:ring-2 focus:ring-fuchsia-500/50" data-name="{{ Str::lower($category->name) }}" data-description="{{ Str::lower($category->description) }}">
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-gradient-to-br from-fuchsia-500/10 via-transparent to-cyan-500/10 transition pointer-events-none"></div>
                            <div class="relative flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <h2 class="text-sm font-medium text-gray-200 group-hover:text-white pr-6 leading-snug line-clamp-2">{{ $category->name }}</h2>
                                    <span class="shrink-0 inline-flex items-center px-2 py-1 text-[10px] rounded-full bg-white/5 text-gray-400 font-medium group-hover:text-fuchsia-300 group-hover:bg-white/10" aria-label="Jumlah produk">{{ $category->products_count }}</span>
                                </div>
                                <p class="mt-2 text-[11px] text-gray-500 leading-relaxed line-clamp-3">{{ $category->description ?: 'Tidak ada deskripsi.' }}</p>
                            </div>
                            <div class="mt-3 flex items-center justify-between relative z-10">
                                <span class="text-[11px] font-medium inline-flex items-center gap-1 bg-clip-text text-transparent bg-gradient-to-r from-fuchsia-400 via-rose-400 to-cyan-400">
                                    Detail
                                    <svg class="w-3 h-3 text-fuchsia-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                                </span>
                                <span class="text-[10px] text-gray-500">Aktif</span>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="mt-8">{{ $categories->links() }}</div>
            @else
                <div class="px-2">
                    <div class="text-center py-20">
                        <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                        <h3 class="mt-4 text-sm font-medium text-white">Tidak ada kategori</h3>
                        <p class="mt-1 text-sm text-gray-500">Belum ada kategori aktif yang tersedia.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function categoryFilter() { return {}; }
        const input = document.getElementById('category-search');
        const clearBtn = document.getElementById('clear-search');
        const cards = document.querySelectorAll('.category-card');
        function applyFilter() {
            const q = (input.value || '').trim().toLowerCase();
            cards.forEach(c => {
                const name = c.dataset.name;
                const desc = c.dataset.description;
                const show = !q || name.includes(q) || (desc && desc.includes(q));
                c.classList.toggle('hidden', !show);
            });
            clearBtn.classList.toggle('hidden', !q);
        }
        input?.addEventListener('input', applyFilter);
        clearBtn?.addEventListener('click', () => { input.value=''; applyFilter(); input.focus(); });
    </script>
</x-app-layout>
