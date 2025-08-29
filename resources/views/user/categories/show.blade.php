<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
        <!-- Header / Toolbar -->
        <div class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-gray-200">
            <div class="px-4 py-3 flex items-center gap-3">
                <a href="{{ route('browse.categories.index') }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200 text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition" aria-label="Kembali">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-semibold text-gray-900 leading-tight line-clamp-1">{{ $category->name }}</h1>
                    <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1">Ringkasan kategori.</p>
                </div>
                <a href="{{ route('browse.products.index', ['category' => $category->id]) }}" class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm transition" aria-label="Lihat semua produk kategori ini">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h4l2 3h6l2-3h1a1 1 0 011 1v15a1 1 0 01-1 1H4a1 1 0 01-1-1V4z" /></svg>
                    Semua Produk
                </a>
            </div>
            <!-- Inline Search -->
            <div class="px-4 pb-3 border-t border-gray-100">
                <div class="flex gap-3 items-center">
                    <div class="flex-1 relative">
                        <input id="product-search" type="text" placeholder="Cari produk dalam kategori" class="peer w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 bg-gray-50 hover:bg-white transition" aria-label="Pencarian produk">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 peer-focus:text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" /></svg>
                    </div>
                    <button id="clear-product-search" type="button" class="hidden shrink-0 px-3 py-2 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg">Reset</button>
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="px-4 pt-5">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Produk Aktif (Total)</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $category->products()->active()->count() }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Produk (Halaman Ini)</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $products->count() }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Dibuat</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $category->created_at?->format('d M Y') ?? '-' }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Diperbarui</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $category->updated_at?->format('d M Y') ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="px-4 py-6">
            <h2 class="text-sm font-medium text-gray-700 mb-4">Produk</h2>
            @if($products->count())
                <div id="products-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach($products as $product)
                        <a href="{{ route('browse.products.show', $product) }}" class="product-card group relative bg-white border border-gray-200 rounded-2xl p-4 hover:border-indigo-500 hover:shadow-md transition flex flex-col overflow-hidden" data-name="{{ Str::lower($product->name) }}" data-desc="{{ Str::lower(Str::limit($product->description,150)) }}">
                            <div class="aspect-square w-full rounded-lg flex items-center justify-center mb-3 overflow-hidden bg-gray-50 border border-gray-100">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="object-cover w-full h-full">
                                @else
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h2l2-3h10l2 3h2v13H3z" /></svg>
                                @endif
                            </div>
                            <h3 class="text-xs font-medium text-gray-800 group-hover:text-indigo-600 mb-1 line-clamp-2 leading-snug">{{ $product->name }}</h3>
                            <div class="mt-auto space-y-0.5">
                                <p class="text-sm font-semibold text-indigo-600">Rp {{ number_format($product->promo_price ?: $product->sell_price, 0, ',', '.') }}</p>
                                @if($product->promo_price)
                                    <p class="text-[10px] text-gray-400 line-through">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</p>
                                @endif
                                <p class="text-[10px] text-gray-400">Stok: {{ $product->stock }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="mt-8">{{ $products->links() }}</div>
            @else
                <div class="text-center py-20 bg-white rounded-2xl border border-gray-200">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18M9 3v18m6-18v18M5 21h14" /></svg>
                    <p class="mt-4 text-sm text-gray-500">Tidak ada produk aktif dalam kategori ini.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        const prodInput = document.getElementById('product-search');
        const prodClear = document.getElementById('clear-product-search');
        const prodCards = document.querySelectorAll('.product-card');
        function filterProducts(){
            const q = prodInput.value.trim().toLowerCase();
            prodCards.forEach(c => {
                const name = c.dataset.name;
                const desc = c.dataset.desc;
                const show = !q || name.includes(q) || (desc && desc.includes(q));
                c.classList.toggle('hidden', !show);
            });
            prodClear.classList.toggle('hidden', !q);
        }
        prodInput?.addEventListener('input', filterProducts);
        prodClear?.addEventListener('click', () => { prodInput.value=''; filterProducts(); prodInput.focus(); });
    </script>
</x-app-layout>
