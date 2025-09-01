<x-app-layout>
    <div class="min-h-screen bg-[#0f1115] text-gray-200 relative overflow-hidden">
        <!-- subtle background accents -->
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_25%_20%,rgba(236,72,153,0.07),transparent_60%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_75%,rgba(59,130,246,0.08),transparent_65%)]"></div>

        <!-- Header / Toolbar -->
        <div class="sticky top-0 z-40 backdrop-blur-md bg-[#0f1115]/70 border-b border-white/10">
            <div class="px-4 py-3 flex items-center gap-3">
                <a href="{{ route('browse.categories.index') }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:bg-white/5 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60" aria-label="Kembali">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-semibold text-white leading-tight line-clamp-1">{{ $category->name }}</h1>
                    <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1">Ringkasan kategori.</p>
                </div>
                <a href="{{ route('browse.products.index', ['category' => $category->id]) }}" class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-gradient-to-r from-fuchsia-500 via-rose-500 to-cyan-500 text-white hover:from-fuchsia-500/90 hover:via-rose-500/90 hover:to-cyan-500/90 shadow-sm shadow-fuchsia-500/30 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60" aria-label="Lihat semua produk kategori ini">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h4l2 3h6l2-3h1a1 1 0 011 1v15a1 1 0 01-1 1H4a1 1 0 01-1-1V4z" /></svg>
                    Semua Produk
                </a>
            </div>
            <!-- Inline Search -->
            <div class="px-4 pb-3 border-t border-white/5">
                <div class="flex gap-3 items-center">
                    <div class="flex-1 relative">
                        <input id="product-search" type="text" placeholder="Cari produk dalam kategori" class="peer w-full pl-10 pr-4 py-2.5 text-sm rounded-xl bg-[#1b1f25] border border-white/10 focus:border-fuchsia-500 focus:ring-2 focus:ring-fuchsia-500/30 text-gray-200 placeholder-gray-500 hover:bg-[#242a32] transition" aria-label="Pencarian produk">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 peer-focus:text-fuchsia-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" /></svg>
                    </div>
                    <button id="clear-product-search" type="button" class="hidden shrink-0 px-3 py-2 text-xs font-medium text-gray-300 bg-[#1b1f25] border border-white/10 hover:bg-[#242a32] rounded-lg focus:outline-none focus:ring-2 focus:ring-fuchsia-500/50">Reset</button>
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="px-4 pt-5">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="rounded-xl p-4 bg-[#181d23] border border-white/5">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Produk Aktif (Total)</p>
                    <p class="mt-1 text-lg font-semibold text-white">{{ $category->products()->active()->count() }}</p>
                </div>
                <div class="rounded-xl p-4 bg-[#181d23] border border-white/5">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Produk (Halaman Ini)</p>
                    <p class="mt-1 text-lg font-semibold text-white">{{ $products->count() }}</p>
                </div>
                <div class="rounded-xl p-4 bg-[#181d23] border border-white/5">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Dibuat</p>
                    <p class="mt-1 text-lg font-semibold text-white">{{ $category->created_at?->format('d M Y') ?? '-' }}</p>
                </div>
                <div class="rounded-xl p-4 bg-[#181d23] border border-white/5">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Diperbarui</p>
                    <p class="mt-1 text-lg font-semibold text-white">{{ $category->updated_at?->format('d M Y') ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="px-4 mt-6">
            <div class="h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
        </div>

        <!-- Products Grid -->
        <div class="px-4 py-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-medium text-gray-300">Produk</h2>
                @if($products->count())
                    <span class="text-[11px] text-gray-500">Menampilkan {{ $products->firstItem() }} - {{ $products->lastItem() }}</span>
                @endif
            </div>
            @if($products->count())
                <div id="products-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach($products as $product)
                        <a href="{{ route('browse.products.show', $product) }}" class="product-card group relative rounded-2xl p-4 bg-[#181d23] border border-white/5 hover:border-fuchsia-500/60 transition flex flex-col overflow-hidden focus:outline-none focus:ring-2 focus:ring-fuchsia-500/50" data-name="{{ Str::lower($product->name) }}" data-desc="{{ Str::lower(Str::limit($product->description,150)) }}">
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-gradient-to-br from-fuchsia-500/10 via-transparent to-cyan-500/10 transition pointer-events-none"></div>
                            <div class="relative">
                                <div class="aspect-square w-full rounded-xl flex items-center justify-center mb-3 overflow-hidden bg-[#1f252c] border border-white/5 group-hover:border-fuchsia-400/40">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="object-cover w-full h-full">
                                    @else
                                        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h2l2-3h10l2 3h2v13H3z" /></svg>
                                    @endif
                                </div>
                                <h3 class="text-xs font-medium text-gray-200 group-hover:text-white mb-1 line-clamp-2 leading-snug relative">{{ $product->name }}</h3>
                            </div>
                            <div class="mt-auto space-y-0.5 relative z-10">
                                <p class="text-sm font-semibold bg-clip-text text-transparent bg-gradient-to-r from-fuchsia-400 via-rose-400 to-cyan-400">Rp {{ number_format($product->promo_price ?: $product->sell_price, 0, ',', '.') }}</p>
                                @if($product->promo_price)
                                    <p class="text-[10px] text-gray-500 line-through">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</p>
                                @endif
                                <p class="text-[10px] text-gray-500">Stok: {{ $product->stock }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="mt-8">{{ $products->links() }}</div>
            @else
                <div class="text-center py-20 rounded-2xl border border-white/10 bg-[#181d23]">
                    <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18M9 3v18m6-18v18M5 21h14" /></svg>
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
            const q = (prodInput.value || '').trim().toLowerCase();
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
