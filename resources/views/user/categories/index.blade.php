<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
        <!-- Header / Toolbar -->
        <div class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
            <div class="px-4 py-3 flex items-center gap-3">
                <a href="{{ url()->previous() }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200 text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition" aria-label="Kembali">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-semibold text-gray-900 leading-tight">Kategori Produk</h1>
                    <p class="text-[11px] text-gray-500 mt-0.5">Daftar kategori aktif.</p>
                </div>
                <a href="{{ route('browse.products.index') }}" class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h4l2 3h6l2-3h1a1 1 0 011 1v15a1 1 0 01-1 1H4a1 1 0 01-1-1V4z" /></svg>
                    Semua Produk
                </a>
            </div>
            <!-- Search Bar -->
            <div class="px-4 pb-3">
                <div class="flex gap-3 items-center">
                    <div class="flex-1 relative">
                        <input id="category-search" type="text" placeholder="Cari kategori" class="peer w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 bg-gray-50 hover:bg-white transition" aria-label="Pencarian kategori">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 peer-focus:text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" /></svg>
                    </div>
                    <button id="clear-search" class="hidden shrink-0 px-3 py-2 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg" type="button">Reset</button>
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="px-4 pt-5">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Kategori Aktif (Total)</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $categories->total() }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Halaman</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $categories->currentPage() }} / {{ $categories->lastPage() }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Kategori (Halaman Ini)</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $categories->count() }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Produk (Halaman Ini)</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $categories->sum('products_count') }}</p>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="px-4 mt-6">
            <div class="h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
        </div>

        <!-- Categories Grid -->
        <div class="px-4 py-6" x-data="categoryFilter()">
            @if($categories->count())
                <div id="categories-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach($categories as $category)
                        <a href="{{ route('browse.categories.show', $category) }}" class="category-card group relative bg-white border border-gray-200 rounded-2xl p-4 hover:border-indigo-500 hover:shadow-md transition flex flex-col overflow-hidden" data-name="{{ Str::lower($category->name) }}" data-description="{{ Str::lower($category->description) }}">
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-gradient-to-br from-indigo-50 via-transparent to-indigo-100 transition"></div>
                            <div class="relative flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <h2 class="text-sm font-medium text-gray-800 group-hover:text-indigo-600 pr-6 leading-snug line-clamp-2">{{ $category->name }}</h2>
                                    <span class="shrink-0 inline-flex items-center px-2 py-1 text-[10px] rounded-full bg-gray-100 text-gray-600 font-medium group-hover:bg-indigo-50 group-hover:text-indigo-600" aria-label="Jumlah produk">{{ $category->products_count }}</span>
                                </div>
                                <p class="mt-2 text-[11px] text-gray-500 leading-relaxed line-clamp-3">{{ $category->description ?: 'Tidak ada deskripsi.' }}</p>
                            </div>
                            <div class="mt-3 flex items-center justify-between relative z-10">
                                <span class="text-[11px] font-medium text-indigo-600 inline-flex items-center gap-1">
                                    Detail
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                                </span>
                                <span class="text-[10px] text-gray-400">Aktif</span>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="mt-8">{{ $categories->links() }}</div>
            @else
                <div class="px-2">
                    <div class="text-center py-20">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                        <h3 class="mt-4 text-sm font-medium text-gray-900">Tidak ada kategori</h3>
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
            const q = input.value.trim().toLowerCase();
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
