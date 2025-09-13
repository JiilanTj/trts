<x-app-layout>
    <div class="min-h-screen bg-[#0f1115] text-gray-200 px-4 py-6 space-y-8 relative overflow-hidden">
        <!-- Background accents -->
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_25%_15%,rgba(236,72,153,0.07),transparent_60%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_85%,rgba(59,130,246,0.08),transparent_65%)]"></div>

        <!-- Header -->
        <div class="relative z-10 text-center mb-8">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-[#FE2C55]/20 to-[#25F4EE]/20 border border-[#FE2C55]/30 mb-4">
                <svg class="w-5 h-5 text-[#FE2C55]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                </svg>
                <span class="text-sm font-medium text-white">Etalase Shared</span>
            </div>
            
            <h1 class="text-2xl font-bold text-white mb-2">
                Etalase {{ $sellerInfo ? $sellerInfo->store_name : $seller->full_name }}
            </h1>
            
            <div class="flex items-center justify-center gap-4 text-sm text-gray-400">
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Seller: {{ $seller->full_name }}</span>
                </div>
                
                @if($sellerInfo)
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m4 0v-5a1 1 0 011-1h4a1 1 0 011 1v5m-4-5v5m0-5h4" />
                    </svg>
                    <span>{{ $sellerInfo->store_name }}</span>
                </div>
                @endif
                
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>{{ $showcases->count() }} Produk</span>
                </div>
            </div>
        </div>

        <!-- Seller Info Card -->
        @if($sellerInfo)
        <div class="relative z-10 max-w-4xl mx-auto">
            <div class="bg-[#181d23]/50 border border-white/10 rounded-2xl p-6">
                <div class="flex items-start gap-4">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] flex items-center justify-center text-white text-xl font-bold">
                        {{ substr($sellerInfo->store_name, 0, 2) }}
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white">{{ $sellerInfo->store_name }}</h3>
                        <p class="text-sm text-gray-400 mt-1">{{ $seller->full_name }}</p>
                        @if($sellerInfo->store_description)
                        <p class="text-sm text-gray-300 mt-2">{{ $sellerInfo->store_description }}</p>
                        @endif
                        
                        <div class="flex items-center gap-4 mt-3">
                            <div class="flex items-center gap-1 text-xs text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Bergabung {{ $seller->created_at->format('M Y') }}</span>
                            </div>
                            
                            @if($seller->level > 1)
                            <div class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-gradient-to-r from-yellow-500/20 to-orange-500/20 border border-yellow-500/30">
                                <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="text-xs font-medium text-yellow-400">Level {{ $seller->level }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Products Grid -->
        <div class="relative z-10 max-w-6xl mx-auto">
            @if($showcases->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($showcases as $item)
                    <div class="bg-[#181d23]/50 border border-white/10 rounded-2xl overflow-hidden hover:border-white/20 transition-colors group">
                        <!-- Product Image -->
                        <div class="aspect-square bg-gray-800/50 flex items-center justify-center overflow-hidden">
                            @if($item->product->image_url)
                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-300">
                            @else
                                <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            @endif
                        </div>
                        
                        <!-- Product Info -->
                        <div class="p-4">
                            <h4 class="font-medium text-white text-sm mb-2 line-clamp-2 leading-tight">{{ $item->product->name }}</h4>
                            
                            <!-- Price Section -->
                            <div class="space-y-2">
                                <!-- Price -->
                                <div class="mb-3">
                                    <span class="text-lg font-bold text-[#25F4EE]">
                                        Rp {{ number_format($item->product->harga_jual, 0, ',', '.') }}
                                    </span>
                                </div>
                                
                                <!-- Stock and Category Info -->
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-400">Stok: {{ $item->product->stock }}</span>
                                    
                                    @if($item->product->category)
                                    <span class="text-gray-500">{{ $item->product->category->name }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Featured Badge -->
                            @if($item->is_featured_active)
                            <div class="mt-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-yellow-500/20 border border-yellow-500/30">
                                    <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="text-xs font-medium text-yellow-400">Featured</span>
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="w-20 h-20 mx-auto rounded-full bg-white/5 flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">Etalase Kosong</h3>
                    <p class="text-gray-400 text-sm">Seller belum menambahkan produk ke etalase.</p>
                </div>
            @endif
        </div>

        <!-- Footer Info -->
        <div class="relative z-10 text-center pt-8 pb-4">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10">
                <svg class="w-4 h-4 text-[#FE2C55]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span class="text-xs text-gray-400">Powered by Bangobos</span>
            </div>
        </div>
    </div>
</x-app-layout>
