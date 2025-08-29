<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
        <!-- Header / Toolbar -->
        <div class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-gray-200">
            <div class="px-4 py-3 flex items-center gap-3">
                <a href="{{ url()->previous() }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200 text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition" aria-label="Kembali">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-semibold text-gray-900 leading-tight">Produk</h1>
                    <p class="text-[11px] text-gray-500 mt-0.5">Daftar produk aktif.</p>
                </div>
                <a href="{{ route('browse.categories.index') }}" class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75h6.5v6.5h-6.5zM13.75 3.75h6.5v6.5h-6.5zM13.75 13.75h6.5v6.5h-6.5zM3.75 13.75h6.5v6.5h-6.5z" /></svg>
                    Kategori
                </a>
            </div>
            <!-- Filter Bar -->
            <div class="px-4 pb-3 border-t border-gray-100">
                <form method="GET" class="flex flex-wrap gap-3 items-center">
                    <div class="flex-1 min-w-[200px] relative">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari produk" class="peer w-full pl-10 pr-3 py-2.5 text-sm rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 bg-gray-50 hover:bg-white transition" aria-label="Pencarian produk">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 peer-focus:text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" /></svg>
                    </div>
                    <select name="category" class="px-3 py-2.5 text-sm rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 bg-gray-50 hover:bg-white">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected($categoryId == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="px-3 py-2.5 text-sm rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 bg-gray-50 hover:bg-white">
                        <option value="active" @selected($status==='active')>Aktif</option>
                        <option value="all" @selected($status==='all')>Semua</option>
                    </select>
                    <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-xl shadow-sm">Terapkan</button>
                    @if($search || $categoryId || $status!=='active')
                        <a href="{{ route('browse.products.index') }}" class="text-[11px] text-gray-500 hover:text-gray-700">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Statistik -->
        <div class="px-4 pt-5">
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Produk Aktif (Total)</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $status==='active' ? $products->total() : $products->total() }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Halaman</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $products->currentPage() }} / {{ $products->lastPage() }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Produk (Halaman Ini)</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $products->count() }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Kategori (Halaman Ini)</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $products->pluck('category_id')->unique()->count() }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Promo (Halaman Ini)</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $products->filter(fn($p)=>$p->promo_price)->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="px-4 mt-6">
            <div class="h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
        </div>

        <!-- Products Grid -->
        <div class="px-4 py-6">
            @if($products->count())
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach($products as $product)
                        <a href="{{ route('browse.products.show', $product) }}" class="group relative bg-white border border-gray-200 rounded-2xl p-4 hover:border-indigo-500 hover:shadow-md transition flex flex-col overflow-hidden">
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-gradient-to-br from-indigo-50 via-transparent to-indigo-100 transition"></div>
                            <div class="relative aspect-square w-full bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center mb-3 overflow-hidden">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="object-cover w-full h-full">
                                @else
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h2l2-3h10l2 3h2v13H3z" /></svg>
                                @endif
                                @if($product->promo_price)
                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-medium">Promo</span>
                                @endif
                                @if(!$product->inStock())
                                    <span class="absolute top-2 right-2 px-2 py-0.5 rounded-full bg-gray-700 text-white text-[10px] font-medium">Habis</span>
                                @elseif($product->stock < 5)
                                    <span class="absolute top-2 right-2 px-2 py-0.5 rounded-full bg-amber-500 text-white text-[10px] font-medium">Menipis</span>
                                @endif
                            </div>
                            <h3 class="relative text-xs font-medium text-gray-800 group-hover:text-indigo-600 mb-1 line-clamp-2 leading-snug">{{ $product->name }}</h3>
                            <div class="mt-auto space-y-0.5 relative">
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
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8V4a1 1 0 00-1-1H7a1 1 0 00-1 1v1m8 0V4" /></svg>
                    <p class="mt-4 text-sm text-gray-500">Tidak ada produk.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
