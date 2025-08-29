<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 px-4 py-6 space-y-8">
        <div class="flex items-center gap-3">
            <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200 text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition" aria-label="Kembali">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <div class="flex-1 min-w-0">
                <h1 class="text-base font-semibold text-gray-900 leading-tight line-clamp-1">Detail Produk</h1>
                <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1">Informasi produk aktif.</p>
            </div>
            <a href="{{ route('browse.products.index') }}" class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h2l2-3h10l2 3h2v13H3z" /></svg>
                Semua Produk
            </a>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <!-- Media -->
            <div class="space-y-4">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden relative">
                    <div class="aspect-square w-full bg-gray-100 flex items-center justify-center overflow-hidden">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="object-cover w-full h-full">
                        @else
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h2l2-3h10l2 3h2v13H3z" /></svg>
                        @endif
                    </div>
                    @if($product->promo_price)
                        <span class="absolute top-3 left-3 px-2 py-1 text-[10px] font-medium rounded-full bg-rose-600 text-white">Promo</span>
                    @endif
                    @if(!$product->inStock())
                        <span class="absolute top-3 right-3 px-2 py-1 text-[10px] font-medium rounded-full bg-gray-800 text-white">Stok Habis</span>
                    @elseif($product->stock < 5)
                        <span class="absolute top-3 right-3 px-2 py-1 text-[10px] font-medium rounded-full bg-amber-500 text-white">Stok Menipis</span>
                    @endif
                </div>
            </div>

            <!-- Detail -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-1 leading-snug">{{ $product->name }}</h2>
                    <p class="text-xs text-gray-500">SKU: {{ $product->sku }} • Kategori: {{ $product->category->name }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-baseline gap-2">
                        <p class="text-2xl font-bold text-indigo-600">Rp {{ number_format($product->promo_price ?: $product->sell_price, 0, ',', '.') }}</p>
                        @if($product->promo_price)
                            <p class="text-sm text-gray-400 line-through">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</p>
                        @endif
                    </div>
                    @if(!$product->inStock())
                        <span class="px-2.5 py-1 text-[11px] bg-red-50 text-red-600 rounded-full font-medium">Stok Habis</span>
                    @else
                        <span class="px-2.5 py-1 text-[11px] bg-green-50 text-green-600 rounded-full font-medium">Stok: {{ $product->stock }}</span>
                    @endif
                    @if($product->expiry_date)
                        <span class="px-2.5 py-1 text-[11px] bg-gray-100 text-gray-600 rounded-full font-medium">Exp {{ $product->expiry_date->format('d M Y') }}</span>
                    @endif
                </div>
                <div class="text-sm text-gray-700 leading-relaxed">
                    {{ $product->description ?: 'Tidak ada deskripsi produk.' }}
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="bg-white rounded-xl border border-gray-200 p-4">
                        <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Berat</p>
                        <p class="mt-1 font-semibold text-gray-900">{{ $product->weight ? $product->weight . ' gr' : '-' }}</p>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-4">
                        <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Status</p>
                        <p class="mt-1 font-semibold text-gray-900">{{ $product->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}</p>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-4">
                        <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Dibuat</p>
                        <p class="mt-1 font-semibold text-gray-900">{{ $product->created_at?->format('d M Y') }}</p>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-4">
                        <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Diperbarui</p>
                        <p class="mt-1 font-semibold text-gray-900">{{ $product->updated_at?->format('d M Y') }}</p>
                    </div>
                </div>

                <div class="pt-2">
                    <form method="POST" action="{{ route('browse.products.buy',$product) }}" class="space-y-3">
                        @csrf
                        <button @disabled(!$product->inStock()) class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-medium rounded-xl text-sm shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m5-9v9m4-9v9m4-9l2 9" /></svg>
                            Beli
                        </button>
                        @if (session('success'))
                            <p class="text-[11px] text-green-600 font-medium">{{ session('success') }}</p>
                        @endif
                        @if (session('error'))
                            <p class="text-[11px] text-red-600 font-medium">{{ session('error') }}</p>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="mt-10">
            <div class="h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
        </div>

        <!-- Related Products -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800">Produk Terkait (Kategori Sama)</h3>
                <a href="{{ route('browse.products.index',[ 'category' => $product->category_id ]) }}" class="text-[11px] text-indigo-600 hover:text-indigo-700 font-medium">Lihat Semua</a>
            </div>
            @if($relatedProducts->count())
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach($relatedProducts as $rp)
                        <a href="{{ route('browse.products.show',$rp) }}" class="group relative bg-white border border-gray-200 rounded-2xl p-3 hover:border-indigo-500 hover:shadow-md transition flex flex-col overflow-hidden">
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-gradient-to-br from-indigo-50 via-transparent to-indigo-100 transition"></div>
                            <div class="relative aspect-square w-full bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center mb-2 overflow-hidden">
                                @if($rp->image_url)
                                    <img src="{{ $rp->image_url }}" alt="{{ $rp->name }}" class="object-cover w-full h-full">
                                @else
                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h2l2-3h10l2 3h2v13H3z" /></svg>
                                @endif
                                @if($rp->promo_price)
                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full bg-rose-500 text-white text-[9px] font-medium">Promo</span>
                                @endif
                            </div>
                            <h4 class="relative text-[11px] font-medium text-gray-800 group-hover:text-indigo-600 mb-0.5 line-clamp-2 leading-snug">{{ $rp->name }}</h4>
                            <p class="relative text-xs font-semibold text-indigo-600">Rp {{ number_format($rp->promo_price ?: $rp->sell_price,0,',','.') }}</p>
                            <p class="relative text-[9px] text-gray-400">Stok: {{ $rp->stock }}</p>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-[11px] text-gray-500">Tidak ada produk terkait lainnya.</p>
            @endif
        </div>
    </div>
</x-app-layout>
