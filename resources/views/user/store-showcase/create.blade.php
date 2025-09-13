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
                <h1 class="text-base font-semibold text-white leading-tight line-clamp-1">Tambah ke Etalase</h1>
                <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1">Pilih produk untuk ditambahkan ke etalase toko Anda.</p>
            </div>
        </div>

        <!-- Selected Product (if any) -->
        @if(isset($selectedProduct) && $selectedProduct)
            <div class="relative z-10">
                <div class="rounded-2xl border border-fuchsia-500/20 bg-[#181d23] p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-20 h-20 rounded-xl bg-[#1f252c] border border-white/5 flex items-center justify-center overflow-hidden flex-shrink-0">
                            @if($selectedProduct->image_url)
                                <img src="{{ $selectedProduct->image_url }}" alt="{{ $selectedProduct->name }}" class="object-cover w-full h-full">
                            @else
                                <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-white mb-1">{{ $selectedProduct->name }}</h3>
                            <p class="text-sm text-gray-400 mb-2">{{ $selectedProduct->category->name }}</p>
                            <p class="text-lg font-semibold bg-clip-text text-transparent bg-gradient-to-r from-fuchsia-400 via-rose-400 to-cyan-400">
                                Harga Jual: Rp {{ number_format($selectedProduct->harga_jual, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-fuchsia-500/20 text-fuchsia-300">Terpilih</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form for Selected Product -->
            <div class="relative z-10">
                <form action="{{ route('user.showcases.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $selectedProduct->id }}">
                    
                    <div class="rounded-2xl border border-white/10 bg-[#181d23] p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Featured Toggle -->
                            <div>
                                <label class="flex items-start gap-3">
                                    <input type="checkbox" name="is_featured" value="1" class="mt-1 text-fuchsia-500 focus:ring-fuchsia-500/60 bg-[#1b1f25] border-white/10 rounded">
                                    <div>
                                        <span class="block text-sm font-medium text-gray-200">Produk Featured</span>
                                        <span class="text-xs text-gray-500">Tampilkan produk ini sebagai featured di etalase</span>
                                    </div>
                                </label>
                            </div>

                            <!-- Featured Until -->
                            <div>
                                <label for="featured_until" class="block text-sm font-medium text-gray-200 mb-2">Featured Sampai</label>
                                <input type="datetime-local" 
                                       id="featured_until" 
                                       name="featured_until" 
                                       value="{{ old('featured_until', now()->addDays(30)->format('Y-m-d\TH:i')) }}"
                                       class="w-full px-4 py-2 rounded-lg bg-[#1b1f25] border border-white/10 text-gray-200 focus:border-fuchsia-500/60 focus:ring-fuchsia-500/60 focus:outline-none transition">
                                <p class="text-xs text-gray-500 mt-1">Kosongkan untuk featured tanpa batas waktu</p>
                            </div>
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
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                Tambah ke Etalase
                            </button>
                            <a href="{{ route('user.showcases.index') }}" class="px-6 py-3 rounded-xl text-sm font-medium text-gray-300 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 transition focus:outline-none focus:ring-2 focus:ring-white/20">
                                Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        @endif

        <!-- Available Products -->
        @if($availableProducts->count())
            <div class="relative z-10">
                <h2 class="text-lg font-medium text-white mb-4">
                    @if(isset($selectedProduct) && $selectedProduct)
                        Atau pilih produk lain
                    @else
                        Pilih Produk
                    @endif
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($availableProducts as $product)
                        <div class="group relative rounded-2xl p-4 bg-[#181d23] border border-white/5 hover:border-fuchsia-500/60 transition cursor-pointer" 
                             onclick="selectProduct({{ $product->id }})">
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-gradient-to-br from-fuchsia-500/10 via-transparent to-cyan-500/10 transition pointer-events-none"></div>
                            
                            <!-- Product Image -->
                            <div class="relative aspect-square w-full bg-[#1f252c] border border-white/5 rounded-xl flex items-center justify-center mb-3 overflow-hidden group-hover:border-fuchsia-400/40">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="object-cover w-full h-full">
                                @else
                                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                @endif
                            </div>
                            
                            <!-- Product Info -->
                            <div class="relative">
                                <h3 class="text-sm font-medium text-gray-200 group-hover:text-white mb-1 line-clamp-2 leading-snug">{{ $product->name }}</h3>
                                <p class="text-xs text-gray-500 mb-2">{{ $product->category->name }}</p>
                                <p class="text-sm font-semibold bg-clip-text text-transparent bg-gradient-to-r from-fuchsia-400 via-rose-400 to-cyan-400">
                                    Rp {{ number_format($product->harga_jual, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($availableProducts->hasPages())
                    <div class="mt-6">
                        {{ $availableProducts->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        @else
            @if(!isset($selectedProduct) || !$selectedProduct)
                <!-- Empty State -->
                <div class="relative z-10 text-center py-16">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-gray-500/20 to-slate-500/20 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-300 mb-2">Tidak Ada Produk Tersedia</h3>
                    <p class="text-sm text-gray-500 mb-6">Semua produk sudah ditambahkan ke etalase atau tidak ada produk yang aktif.</p>
                    <a href="{{ route('browse.products.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-gradient-to-r from-fuchsia-500 via-rose-500 to-cyan-500 text-white hover:from-fuchsia-500/90 hover:via-rose-500/90 hover:to-cyan-500/90 shadow-sm shadow-fuchsia-500/30 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60">
                        Lihat Produk
                    </a>
                </div>
            @endif
        @endif
    </div>

    <!-- JavaScript -->
    <script>
        function selectProduct(productId) {
            const url = new URL(window.location);
            url.searchParams.set('product_id', productId);
            window.location.href = url.toString();
        }

        // Auto-hide featured_until when featured is unchecked
        document.addEventListener('DOMContentLoaded', function() {
            const featuredCheckbox = document.querySelector('input[name="is_featured"]');
            const featuredUntilDiv = featuredCheckbox?.closest('form')?.querySelector('input[name="featured_until"]')?.closest('div');
            
            if (featuredCheckbox && featuredUntilDiv) {
                function toggleFeaturedUntil() {
                    featuredUntilDiv.style.display = featuredCheckbox.checked ? 'block' : 'none';
                }
                
                featuredCheckbox.addEventListener('change', toggleFeaturedUntil);
                toggleFeaturedUntil(); // Initial state
            }
        });
    </script>
</x-app-layout>
