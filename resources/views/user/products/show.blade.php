<x-app-layout>
    <div class="min-h-screen bg-[#0f1115] text-gray-200 px-4 py-6 space-y-10 relative overflow-hidden">
        <!-- background accents -->
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_25%_15%,rgba(236,72,153,0.07),transparent_60%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_85%,rgba(59,130,246,0.08),transparent_65%)]"></div>

        <!-- Header -->
        <div class="flex items-center gap-3 relative z-10">
            <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:bg-white/5 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60" aria-label="Kembali">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <div class="flex-1 min-w-0">
                <h1 class="text-base font-semibold text-white leading-tight line-clamp-1">Detail Produk</h1>
                <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1">Informasi produk aktif.</p>
            </div>
            <a href="{{ route('browse.products.index') }}" class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-gradient-to-r from-fuchsia-500 via-rose-500 to-cyan-500 text-white hover:from-fuchsia-500/90 hover:via-rose-500/90 hover:to-cyan-500/90 shadow-sm shadow-fuchsia-500/30 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h2l2-3h10l2 3h2v13H3z" /></svg>
                Semua Produk
            </a>
        </div>

        <div class="grid md:grid-cols-2 gap-10 relative z-10">
            <!-- Media -->
            <div class="space-y-4">
                <div class="rounded-2xl border border-white/10 bg-[#181d23] shadow-sm overflow-hidden relative">
                    <div class="aspect-square w-full bg-[#1f252c] flex items-center justify-center overflow-hidden">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="object-cover w-full h-full">
                        @else
                            <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h2l2-3h10l2 3h2v13H3z" /></svg>
                        @endif
                    </div>
                    @if(!$product->inStock())
                        <span class="absolute top-3 right-3 px-2 py-1 text-[10px] font-medium rounded-full bg-gray-800 text-white">Stok Habis</span>
                    @elseif($product->stock < 5)
                        <span class="absolute top-3 right-3 px-2 py-1 text-[10px] font-medium rounded-full bg-amber-500 text-white">Stok Menipis</span>
                    @endif
                    @if($product->promo_price && $product->promo_price < $product->sell_price)
                        @php $disc = round( (1 - ($product->promo_price / $product->sell_price))*100 ); @endphp
                        <span class="absolute top-3 left-3 px-2 py-1 text-[10px] font-semibold rounded-full bg-gradient-to-r from-fuchsia-600 to-rose-600 text-white shadow">-{{ $disc }}%</span>
                    @endif
                </div>
            </div>

            <!-- Detail -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-xl font-semibold text-white mb-1 leading-snug">{{ $product->name }}</h2>
                    <p class="text-xs text-gray-500">SKU: {{ $product->sku }} • Kategori: {{ $product->category->name }}</p>
                </div>

                <!-- Pricing Block -->
                <div class="space-y-3">
                    @php $isSeller = auth()->user()?->isSeller(); @endphp
                    <div class="flex flex-wrap items-end gap-6">
                        <div>
                            <p class="text-[11px] font-medium uppercase tracking-wide text-gray-500">Harga Biasa</p>
                            <div class="flex items-baseline gap-2">
                                <p class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-fuchsia-400 via-rose-400 to-cyan-400">Rp {{ number_format($product->harga_biasa, 0, ',', '.') }}</p>
                                @if($product->promo_price && $product->promo_price < $product->sell_price)
                                    <p class="text-sm text-gray-500 line-through">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</p>
                                @endif
                            </div>
                        </div>
                        @if($isSeller)
                            <div>
                                <p class="text-[11px] font-medium uppercase tracking-wide text-gray-500">Harga Jual</p>
                                <p class="text-xl font-semibold text-cyan-400">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</p>
                            </div>
                            <div class="hidden md:block">
                                @php $potensi = $product->harga_jual - $product->harga_biasa; @endphp
                                <p class="text-[11px] font-medium uppercase tracking-wide text-gray-500">Margin Potensial</p>
                                <p class="text-sm font-semibold {{ $potensi>0 ? 'text-emerald-400' : 'text-gray-400' }}">{{ $potensi>0 ? 'Rp '.number_format($potensi,0,',','.') : '-' }}</p>
                            </div>
                        @endif
                        <div>
                            @if(!$product->inStock())
                                <span class="px-2.5 py-1 text-[11px] bg-red-600/20 text-red-400 rounded-full font-medium border border-red-600/30">Stok Habis</span>
                            @else
                                <span class="px-2.5 py-1 text-[11px] bg-emerald-600/20 text-emerald-400 rounded-full font-medium border border-emerald-600/30">Stok: {{ $product->stock }}</span>
                            @endif
                        </div>
                        @if($product->expiry_date)
                            <div>
                                <span class="px-2.5 py-1 text-[11px] bg-[#1b1f25] text-gray-400 rounded-full font-medium border border-white/10">Exp {{ $product->expiry_date->format('d M Y') }}</span>
                            </div>
                        @endif
                    </div>
                    @if($isSeller)
                        <div id="chosen-price" class="text-[11px] font-medium text-gray-400">
                            Harga terpilih saat ini: <span class="text-fuchsia-300" data-label>Harga Biasa</span> • <span data-price>Rp {{ number_format($product->harga_biasa,0,',','.') }}</span>
                        </div>
                    @endif
                </div>

                <div class="text-sm text-gray-400 leading-relaxed">
                    {{ $product->description ?: 'Tidak ada deskripsi produk.' }}
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="rounded-xl border border-white/10 bg-[#181d23] p-4">
                        <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Berat</p>
                        <p class="mt-1 font-semibold text-gray-200">{{ $product->weight ? $product->weight . ' gr' : '-' }}</p>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-[#181d23] p-4">
                        <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Status</p>
                        <p class="mt-1 font-semibold text-gray-200">{{ $product->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}</p>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-[#181d23] p-4">
                        <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Dibuat</p>
                        <p class="mt-1 font-semibold text-gray-200">{{ $product->created_at?->format('d M Y') }}</p>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-[#181d23] p-4">
                        <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Diperbarui</p>
                        <p class="mt-1 font-semibold text-gray-200">{{ $product->updated_at?->format('d M Y') }}</p>
                    </div>
                </div>

                <div class="pt-2">
                    <form method="POST" action="{{ route('browse.products.buy',$product) }}" class="space-y-4" id="buy-form" data-harga-biasa="{{ $product->harga_biasa }}" data-harga-jual="{{ $product->harga_jual }}">
                        @csrf
                        @if($isSeller)
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2 text-xs text-gray-300">
                                    <input type="radio" name="purchase_type" value="self" class="purchase-type text-fuchsia-500 focus:ring-fuchsia-500/60 bg-[#1b1f25] border-white/10" checked>
                                    <span>Beli Untuk Diri (Harga Biasa)</span>
                                </label>
                                <label class="flex items-center gap-2 text-xs text-gray-300">
                                    <input type="radio" name="purchase_type" value="external" class="purchase-type text-cyan-500 focus:ring-cyan-500/60 bg-[#1b1f25] border-white/10">
                                    <span>Jual ke Pelanggan (Harga Jual)</span>
                                </label>
                            </div>
                        @else
                            <input type="hidden" name="purchase_type" value="self">
                        @endif
                        <button @disabled(!$product->inStock()) class="w-full py-3 rounded-xl text-sm font-medium text-white bg-gradient-to-r from-fuchsia-500 via-rose-500 to-cyan-500 hover:from-fuchsia-500/90 hover:via-rose-500/90 hover:to-cyan-500/90 disabled:from-gray-600 disabled:via-gray-500 disabled:to-gray-400 disabled:cursor-not-allowed shadow-sm shadow-fuchsia-500/30 flex items-center justify-center gap-2 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60"
                            type="button"
                            id="go-order-btn"
                            data-base-url="{{ route('user.orders.create') }}"
                            data-product-id="{{ $product->id }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m5-9v9m4-9v9m4-9l2 9" /></svg>
                            Beli
                        </button>
                        
                        <!-- Add to Showcase Button - Only for Sellers -->
                        @if($isSeller)
                            <button type="button" 
                                class="w-full py-3 rounded-xl text-sm font-medium text-gray-200 bg-gradient-to-r from-gray-700 via-gray-600 to-gray-700 hover:from-gray-600 hover:via-gray-500 hover:to-gray-600 border border-white/10 hover:border-white/20 transition flex items-center justify-center gap-2 focus:outline-none focus:ring-2 focus:ring-gray-500/60"
                                id="add-to-showcase-btn"
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->name }}"
                                data-add-url="{{ route('user.showcases.create') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                Tambah ke Etalase
                            </button>
                        @endif
                        @if (session('success'))
                            <p class="text-[11px] text-emerald-400 font-medium">{{ session('success') }}</p>
                        @endif
                        @if (session('error'))
                            <p class="text-[11px] text-red-400 font-medium">{{ session('error') }}</p>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div>
            <div class="h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
        </div>

        <!-- Related Products -->
        <div class="space-y-4 relative z-10">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-300">Produk Terkait (Kategori Sama)</h3>
                <a href="{{ route('browse.products.index',[ 'category' => $product->category_id ]) }}" class="text-[11px] font-medium bg-clip-text text-transparent bg-gradient-to-r from-fuchsia-400 via-rose-400 to-cyan-400 hover:from-fuchsia-400/80 hover:via-rose-400/80 hover:to-cyan-400/80">Lihat Semua</a>
            </div>
            @if($relatedProducts->count())
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach($relatedProducts as $rp)
                        <a href="{{ route('browse.products.show',$rp) }}" class="group relative rounded-2xl p-3 bg-[#181d23] border border-white/5 hover:border-fuchsia-500/60 transition flex flex-col overflow-hidden focus:outline-none focus:ring-2 focus:ring-fuchsia-500/50">
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-gradient-to-br from-fuchsia-500/10 via-transparent to-cyan-500/10 transition pointer-events-none"></div>
                            <div class="relative aspect-square w-full bg-[#1f252c] border border-white/5 rounded-lg flex items-center justify-center mb-2 overflow-hidden group-hover:border-fuchsia-400/40">
                                @if($rp->image_url)
                                    <img src="{{ $rp->image_url }}" alt="{{ $rp->name }}" class="object-cover w-full h-full">
                                @else
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h2l2-3h10l2 3h2v13H3z" /></svg>
                                @endif
                            </div>
                            <h4 class="relative text-[11px] font-medium text-gray-200 group-hover:text-white mb-0.5 line-clamp-2 leading-snug">{{ $rp->name }}</h4>
                            <p class="relative text-xs font-semibold bg-clip-text text-transparent bg-gradient-to-r from-fuchsia-400 via-rose-400 to-cyan-400">Rp {{ number_format($rp->harga_biasa,0,',','.') }}</p>
                            <p class="relative text-[9px] text-gray-500">Stok: {{ $rp->stock }}</p>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-[11px] text-gray-500">Tidak ada produk terkait lainnya.</p>
            @endif
        </div>
    </div>

    @php $isSeller = auth()->user()?->isSeller(); @endphp
    @if($isSeller)
    <script>
        (function(){
            const form = document.getElementById('buy-form');
            if(!form) return;
            const radios = form.querySelectorAll('.purchase-type');
            const chosen = document.getElementById('chosen-price');
            const hargaBiasa = parseInt(form.dataset.hargaBiasa,10);
            const hargaJual = parseInt(form.dataset.hargaJual,10);
            const rupiah = n => 'Rp ' + (n||0).toLocaleString('id-ID');
            radios.forEach(r=>{
                r.addEventListener('change',()=>{
                    const type = form.querySelector('.purchase-type:checked').value;
                    const price = type==='external'?hargaJual:hargaBiasa;
                    chosen.querySelector('[data-label]').textContent = type==='external' ? 'Harga Jual' : 'Harga Biasa';
                    chosen.querySelector('[data-price]').textContent = rupiah(price);
                    chosen.classList.add('animate-pulse');
                    setTimeout(()=>chosen.classList.remove('animate-pulse'),500);
                });
            });
            const goBtn = document.getElementById('go-order-btn');
            if(goBtn){
                goBtn.addEventListener('click',()=>{
                    const type = form.querySelector('.purchase-type:checked')?.value || 'self';
                    const base = goBtn.dataset.baseUrl;
                    const pid = goBtn.dataset.productId;
                    const url = base + '?product_id=' + encodeURIComponent(pid) + '&purchase_type=' + encodeURIComponent(type);
                    window.location.href = url;
                });
            }
            
            // Handle Add to Showcase button
            const showcaseBtn = document.getElementById('add-to-showcase-btn');
            if(showcaseBtn){
                showcaseBtn.addEventListener('click',()=>{
                    const addUrl = showcaseBtn.dataset.addUrl;
                    const pid = showcaseBtn.dataset.productId;
                    // Just send product_id, let controller handle the rest
                    const url = addUrl + '?product_id=' + encodeURIComponent(pid);
                    window.location.href = url;
                });
            }
        })();
    </script>
    @else
    <script>
        (function(){
            const goBtn = document.getElementById('go-order-btn');
            if(goBtn){
                goBtn.addEventListener('click',()=>{
                    const base = goBtn.dataset.baseUrl;
                    const pid = goBtn.dataset.productId;
                    const url = base + '?product_id=' + encodeURIComponent(pid) + '&purchase_type=self';
                    window.location.href = url;
                });
            }
        })();
    </script>
    @endif
</x-app-layout>
