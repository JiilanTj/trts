@forelse($featuredProducts ?? [] as $product)
@if(isset($product) && is_object($product) && isset($product->id))
<div class="bg-[#23272b] border border-[#2c3136] rounded-xl overflow-hidden relative" 
     data-product-id="{{ $product->id }}" 
     data-product-name="{{ str_replace('"', '&quot;', $product->name ?? '') }}" 
     data-product-price="{{ $product->sell_price ?? 0 }}" 
     data-product-stock="{{ $product->stock ?? 0 }}">
    <!-- Product Image -->
    <div class="aspect-square bg-neutral-800/50 relative">
        @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-12 h-12 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        @endif
        
        <!-- Selection Checkbox -->
        <div class="absolute top-2 right-2">
            <input type="checkbox" class="w-5 h-5 text-[#FE2C55] bg-neutral-800 border-neutral-600 rounded focus:ring-[#FE2C55] focus:ring-2">
        </div>
    </div>
    
    <!-- Product Info -->
    <div class="p-3">
        <h4 class="text-sm font-medium text-white mb-1 truncate">{{ $product->name }}</h4>
        <p class="text-lg font-bold text-[#FE2C55] mb-2">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</p>
        
        <!-- Wholesale Price -->
        @if($product->sell_price > 100000)
        <p class="text-xs text-neutral-400 mb-2">Wholesale: <span class="text-[#25F4EE]">Rp {{ number_format($product->sell_price * 0.85, 0, ',', '.') }}</span></p>
        @endif
        
        <!-- Seller Margin Info -->
        @if(auth()->user()->isSeller())
        @php
            $currentUser = auth()->user();
            $sellerMargin = $product->getSellerMargin($currentUser);
            $marginPercent = $currentUser->getLevelMarginPercent();
        @endphp
        <div class="mb-2 flex items-center gap-1">
            <span class="text-xs text-[#25F4EE] font-medium">Margin:</span>
            <span class="text-xs text-white font-semibold">Rp {{ number_format($sellerMargin, 0, ',', '.') }}</span>
            @if($marginPercent)
            <span class="text-xs px-1.5 py-0.5 bg-[#25F4EE]/20 text-[#25F4EE] rounded font-medium">{{ $marginPercent }}%</span>
            @endif
        </div>
        @endif
        
        <!-- Stock Info -->
        <p class="text-xs text-neutral-400 mb-3">Stok: {{ $product->stock }}</p>
        
        <!-- Action Buttons -->
        <div class="w-full">
            <button onclick="addToEtalase({{ $product->id }}, '{{ addslashes($product->name) }}')" 
                    class="w-full py-2 text-xs font-medium bg-[#25F4EE] text-white rounded-lg hover:bg-[#25F4EE]/90 transition">
                + Etalase
            </button>
        </div>
    </div>
</div>
@endif
@empty
<!-- Empty State -->
<div class="col-span-2 text-center py-12">
    <div class="w-16 h-16 mx-auto rounded-full bg-neutral-700/30 flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>
    </div>
    <h4 class="text-lg font-semibold text-neutral-200 mb-2">Belum Ada Produk</h4>
    <p class="text-sm text-neutral-400">Produk wholesale akan muncul di sini</p>
</div>
@endforelse
