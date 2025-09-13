<x-app-layout>
    <div class="min-h-screen bg-[#0f1115] text-gray-200 relative overflow-hidden">
        <!-- background accents -->
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_15%,rgba(236,72,153,0.07),transparent_60%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_85%_85%,rgba(59,130,246,0.07),transparent_65%)]"></div>
        <!-- Header / Toolbar -->
        <div class="sticky top-0 z-40 backdrop-blur-md bg-[#0f1115]/70 border-b border-white/10">
            <div class="px-4 py-3 flex items-center gap-3">
                <a href="{{ url()->previous() }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:bg-white/5 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60" aria-label="Kembali">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-semibold text-white leading-tight">Produk</h1>
                    <p class="text-[11px] text-gray-500 mt-0.5">Daftar produk aktif.</p>
                </div>
                <a href="{{ route('browse.categories.index') }}" class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-gradient-to-r from-fuchsia-500 via-rose-500 to-cyan-500 text-white hover:from-fuchsia-500/90 hover:via-rose-500/90 hover:to-cyan-500/90 shadow-sm shadow-fuchsia-500/30 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75h6.5v6.5h-6.5zM13.75 3.75h6.5v6.5h-6.5zM13.75 13.75h6.5v6.5h-6.5zM3.75 13.75h6.5v6.5h-6.5z" /></svg>
                    Kategori
                </a>
            </div>
            <!-- Filter Bar -->
            <div class="px-4 pb-3 border-t border-white/5">
                <form method="GET" class="flex flex-wrap gap-3 items-center">
                    <div class="flex-1 min-w-[200px] relative">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari produk" class="peer w-full pl-10 pr-3 py-2.5 text-sm rounded-xl bg-[#1b1f25] border border-white/10 focus:border-fuchsia-500 focus:ring-2 focus:ring-fuchsia-500/30 text-gray-200 placeholder-gray-500 hover:bg-[#242a32] transition" aria-label="Pencarian produk">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 peer-focus:text-fuchsia-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" /></svg>
                    </div>
                    <select name="category" class="px-3 py-2.5 text-sm rounded-xl bg-[#1b1f25] text-gray-200 border border-white/10 focus:border-fuchsia-500 focus:ring-2 focus:ring-fuchsia-500/30 hover:bg-[#242a32]">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected($categoryId == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="px-3 py-2.5 text-sm rounded-xl bg-[#1b1f25] text-gray-200 border border-white/10 focus:border-fuchsia-500 focus:ring-2 focus:ring-fuchsia-500/30 hover:bg-[#242a32]">
                        <option value="active" @selected($status==='active')>Aktif</option>
                        <option value="all" @selected($status==='all')>Semua</option>
                    </select>
                    <button type="submit" class="px-4 py-2.5 rounded-xl text-xs font-medium bg-gradient-to-r from-fuchsia-500 via-rose-500 to-cyan-500 text-white hover:from-fuchsia-500/90 hover:via-rose-500/90 hover:to-cyan-500/90 shadow-sm shadow-fuchsia-500/30 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60">Terapkan</button>
                    @if($search || $categoryId || $status!=='active')
                        <a href="{{ route('browse.products.index') }}" class="text-[11px] text-gray-500 hover:text-gray-400">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Statistik -->
        <div class="px-4 pt-5">
            @if(auth()->user()?->isSeller())
                <!-- Seller Level Info -->
                <div class="mb-6 p-4 rounded-xl bg-gradient-to-r from-emerald-900/20 to-cyan-900/20 border border-emerald-500/30">
                    <div class="flex items-center gap-3">
                        @php 
                            $user = auth()->user();
                            $marginPercent = $user->getLevelMarginPercent();
                            $levelBadge = $user->getLevelBadge();
                        @endphp
                        <span class="px-3 py-1 rounded-full bg-emerald-600/20 text-emerald-300 border border-emerald-500/40 text-xs font-medium">{{ $levelBadge }}</span>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-emerald-300">
                                @if($marginPercent)
                                    Margin {{ $marginPercent }}% dari harga jual untuk penjualan eksternal
                                @else
                                    Margin sesuai admin per produk untuk penjualan eksternal
                                @endif
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                @if($marginPercent)
                                    Setiap pembelian untuk pelanggan akan memberikan margin {{ $marginPercent }}% dari harga jual
                                @else
                                    Margin dihitung dari selisih harga jual dan harga biasa yang ditetapkan admin
                                @endif
                                @if($user->level < 6)
                                    @php 
                                        $nextLevel = $user->level + 1;
                                        $levelRequirements = \App\Models\User::getLevelRequirements();
                                        $nextLevelData = $levelRequirements[$nextLevel] ?? null;
                                    @endphp
                                    @if($nextLevelData)
                                        • <span class="text-cyan-400">Upgrade ke {{ $nextLevelData['badge'] }}</span> untuk margin {{ $nextLevelData['margin_percent'] }}%
                                    @endif
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                <div class="rounded-xl p-4 bg-[#181d23] border border-white/5">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Produk Aktif (Total)</p>
                    <p class="mt-1 text-lg font-semibold text-white">{{ $status==='active' ? $products->total() : $products->total() }}</p>
                </div>
                <div class="rounded-xl p-4 bg-[#181d23] border border-white/5">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Halaman</p>
                    <p class="mt-1 text-lg font-semibold text-white">{{ $products->currentPage() }} / {{ $products->lastPage() }}</p>
                </div>
                <div class="rounded-xl p-4 bg-[#181d23] border border-white/5">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Produk (Halaman Ini)</p>
                    <p class="mt-1 text-lg font-semibold text-white">{{ $products->count() }}</p>
                </div>
                <div class="rounded-xl p-4 bg-[#181d23] border border-white/5">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Kategori (Halaman Ini)</p>
                    <p class="mt-1 text-lg font-semibold text-white">{{ $products->pluck('category_id')->unique()->count() }}</p>
                </div>
                <div class="rounded-xl p-4 bg-[#181d23] border border-white/5">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Promo (Halaman Ini)</p>
                    <p class="mt-1 text-lg font-semibold text-white">{{ $products->filter(fn($p)=>$p->promo_price)->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="px-4 mt-6">
            <div class="h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
        </div>

        <!-- Products Grid -->
        <div class="px-4 py-6">
            @php $isSeller = auth()->user()?->isSeller(); @endphp
            @if($products->count())
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach($products as $product)
                        <a href="{{ route('browse.products.show', $product) }}" class="group relative rounded-2xl p-4 bg-[#181d23] border border-white/5 hover:border-fuchsia-500/60 transition flex flex-col overflow-hidden focus:outline-none focus:ring-2 focus:ring-fuchsia-500/50">
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-gradient-to-br from-fuchsia-500/10 via-transparent to-cyan-500/10 transition pointer-events-none"></div>
                            <div class="relative aspect-square w-full bg-[#1f252c] border border-white/5 rounded-xl flex items-center justify-center mb-3 overflow-hidden group-hover:border-fuchsia-400/40">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="object-cover w-full h-full">
                                @else
                                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h2l2-3h10l2 3h2v13H3z" /></svg>
                                @endif
                                @php $hasPromo = $product->promo_price && $product->promo_price < $product->sell_price; @endphp
                                @if($hasPromo)
                                    @php $disc = round( (1 - ($product->promo_price / $product->sell_price)) * 100 ); @endphp
                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full bg-gradient-to-r from-fuchsia-600 to-rose-600 text-white text-[10px] font-semibold shadow">-{{ $disc }}%</span>
                                @endif
                                @if(!$product->inStock())
                                    <span class="absolute top-2 right-2 px-2 py-0.5 rounded-full bg-gray-800 text-white text-[10px] font-medium">Habis</span>
                                @elseif($product->stock < 5)
                                    <span class="absolute top-2 right-2 px-2 py-0.5 rounded-full bg-amber-500 text-white text-[10px] font-medium">Menipis</span>
                                @endif
                            </div>
                            <h3 class="relative text-xs font-medium text-gray-200 group-hover:text-white mb-1 line-clamp-2 leading-snug">{{ $product->name }}</h3>
                            <div class="mt-auto space-y-1 relative z-10">
                                <div class="flex flex-col gap-0.5">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-[#1b1f25] text-gray-400 border border-white/5">Biasa</span>
                                        <p class="text-sm font-semibold bg-clip-text text-transparent bg-gradient-to-r from-fuchsia-400 via-rose-400 to-cyan-400">Rp {{ number_format($product->harga_biasa, 0, ',', '.') }}</p>
                                    </div>
                                    @if($isSeller)
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-[#1b1f25] text-cyan-400 border border-cyan-500/30">Jual</span>
                                            <p class="text-xs font-medium text-cyan-400">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</p>
                                        </div>
                                        @php 
                                            $user = auth()->user();
                                            $marginPercent = $user->getLevelMarginPercent();
                                            if($marginPercent) {
                                                $margin = round($product->harga_jual * ($marginPercent / 100));
                                            } else {
                                                $margin = max(0, $product->harga_jual - $product->harga_biasa);
                                            }
                                        @endphp
                                        <div class="flex items-center gap-2 ml-0.5">
                                            <span class="text-[9px] text-emerald-400 {{ $margin<=0 ? 'opacity-0' : '' }}">
                                                @if($marginPercent)
                                                    + {{ $marginPercent }}% (Rp {{ number_format($margin,0,',','.') }})
                                                @else
                                                    + Rp {{ number_format($margin,0,',','.') }} (admin)
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                @if($hasPromo)
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
                    <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8V4a1 1 0 00-1-1H7a1 1 0 00-1 1v1m8 0V4" /></svg>
                    <p class="mt-4 text-sm text-gray-500">Tidak ada produk.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
