<x-app-layout>
    @php($user = auth()->user())
    @php($initials = collect(explode(' ', trim($user->full_name ?: $user->username)))->filter()->take(2)->map(fn($p)=> strtoupper(mb_substr($p,0,1)))->implode(''))
    
    <div class="min-h-screen bg-[#1a1d21] text-neutral-100">
        <!-- Header Section with User Info -->
        <div class="sticky top-0 z-40 backdrop-blur bg-[#1f2226]/95 border-b border-neutral-800/70">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <!-- Profile Photo -->
                            <div class="relative">
                                @if($user->photo_url)
                                    <div class="w-12 h-12 rounded-full p-0.5 bg-gradient-to-br from-[#FE2C55] to-[#25F4EE]">
                                        <img src="{{ $user->photo_url }}" alt="Avatar" class="w-full h-full rounded-full object-cover ring-1 ring-black/40" />
                                    </div>
                                @else
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center bg-gradient-to-br from-[#FE2C55] to-[#25F4EE] p-0.5">
                                        <div class="w-full h-full rounded-full bg-black flex items-center justify-center text-sm font-semibold tracking-wide">{{ $initials }}</div>
                                    </div>
                                @endif
                                <!-- Wholesale Badge -->
                                <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] text-white text-xs font-bold shadow-[0_0_0_2px_#000]">📦</div>
                            </div>
                            <div>
                                @if(auth()->user()->isSeller() && auth()->user()->sellerInfo)
                                    <h1 class="text-xl font-semibold">{{ auth()->user()->full_name }}</h1>
                                    <p class="text-sm font-medium bg-clip-text text-transparent bg-gradient-to-r from-[#FE2C55] to-[#25F4EE]">{{ auth()->user()->sellerInfo->store_name }}</p>
                                @else
                                    <h1 class="text-xl font-semibold">{{ auth()->user()->full_name }}</h1>
                                @endif
                                <!-- Page Title -->
                                <div class="flex items-center space-x-6 mt-2">
                                    <div class="text-left">
                                        <span class="text-sm font-semibold">Pusat Wholesale</span>
                                        <p class="text-[10px] uppercase tracking-wide text-neutral-400 mt-0.5">Distribusi & Grosir</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-end space-y-1">
                            <!-- Balance Display -->
                            <div class="flex items-center space-x-1 px-3 py-1.5 rounded-lg bg-gradient-to-r from-emerald-500/15 to-cyan-500/15 border border-emerald-500/20">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                <span class="text-sm font-semibold text-emerald-400">Rp {{ number_format($user->balance, 0, ',', '.') }}</span>
                            </div>
                            <!-- Wholesale Status -->
                            <div class="flex items-center space-x-1 px-2 py-1 rounded-md bg-[#FE2C55]/15 text-[#FE2C55] text-xs font-medium">
                                <div class="w-2 h-2 bg-[#FE2C55] rounded-full animate-pulse"></div>
                                <span>Wholesale Mode</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <!-- Quick Filters - State Changer -->
            <div class="mb-6">
                <div class="flex items-center space-x-1 bg-[#23272b] border border-[#2c3136] rounded-xl p-1">
                    <button id="tabDaftarCepat" class="flex-1 py-2.5 text-sm font-medium bg-[#FE2C55] text-white rounded-lg transition-all">Daftar Cepat</button>
                    <button id="tabPilihanManual" class="flex-1 py-2.5 text-sm font-medium text-neutral-400 hover:text-neutral-200 rounded-lg transition-all">Pilihan Manual</button>
                    <button id="tabPilihanStrategis" class="flex-1 py-2.5 text-sm font-medium text-neutral-400 hover:text-neutral-200 rounded-lg transition-all">Pilihan Strategis</button>
                </div>
            </div>

            <!-- Filter & Selection Section - Only show for "Daftar Cepat" -->
            <div id="filterSection" class="rounded-xl mb-6 border border-[#2c3136] bg-[#23272b] shadow-sm relative overflow-hidden">
                <div class="h-1 w-full bg-gradient-to-r from-[#FE2C55] via-[#FE2C55]/40 to-[#25F4EE]"></div>
                
                <div class="p-5">
                    <!-- Ranking Best Seller -->
                    <div class="mb-4">
                        <p class="text-sm text-neutral-300 mb-2">Ranking Best Seller</p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <button data-ranking-type="best_seller" data-ranking-limit="20" class="ranking-btn px-4 py-2 text-xs font-medium {{ (request('ranking_type') == 'best_seller' && request('ranking_limit') == 20) || (!request('ranking_type') && !request('ranking_limit')) ? 'bg-[#FE2C55] text-white' : 'bg-neutral-700/50 text-neutral-300 hover:bg-neutral-600/50' }} rounded-lg border border-neutral-600">Best 20</button>
                            <button data-ranking-type="best_seller" data-ranking-limit="30" class="ranking-btn px-4 py-2 text-xs font-medium {{ request('ranking_type') == 'best_seller' && request('ranking_limit') == 30 ? 'bg-[#FE2C55] text-white' : 'bg-neutral-700/50 text-neutral-300 hover:bg-neutral-600/50' }} rounded-lg border border-neutral-600">Best 30</button>
                            <button data-ranking-type="best_seller" data-ranking-limit="50" class="ranking-btn px-4 py-2 text-xs font-medium {{ request('ranking_type') == 'best_seller' && request('ranking_limit') == 50 ? 'bg-[#FE2C55] text-white' : 'bg-neutral-700/50 text-neutral-300 hover:bg-neutral-600/50' }} rounded-lg border border-neutral-600">Best 50</button>
                        </div>
                    </div>
                    
                    <!-- Keuntungan Terbaik -->
                    <div class="mb-4">
                        <p class="text-sm text-neutral-300 mb-2">Keuntungan Terbaik</p>
                        <div class="flex flex-wrap gap-2">
                            <button data-profit-type="profit" data-profit-limit="20" class="profit-btn px-4 py-2 text-xs font-medium {{ request('profit_type') == 'profit' && request('profit_limit') == 20 ? 'bg-[#FE2C55] text-white' : 'bg-neutral-700/50 text-neutral-300 hover:bg-neutral-600/50' }} rounded-lg border border-neutral-600">Top 20</button>
                            <button data-profit-type="profit" data-profit-limit="30" class="profit-btn px-4 py-2 text-xs font-medium {{ request('profit_type') == 'profit' && request('profit_limit') == 30 ? 'bg-[#FE2C55] text-white' : 'bg-neutral-700/50 text-neutral-300 hover:bg-neutral-600/50' }} rounded-lg border border-neutral-600">Top 30</button>
                            <button data-profit-type="profit" data-profit-limit="50" class="profit-btn px-4 py-2 text-xs font-medium {{ request('profit_type') == 'profit' && request('profit_limit') == 50 ? 'bg-[#FE2C55] text-white' : 'bg-neutral-700/50 text-neutral-300 hover:bg-neutral-600/50' }} rounded-lg border border-neutral-600">Top 50</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Manual Search Section - Only show for "Pilihan Manual" -->
            <div id="manualFilterSection" class="rounded-xl mb-6 border border-[#2c3136] bg-[#23272b] shadow-sm relative overflow-hidden" style="display: none;">
                <div class="h-1 w-full bg-gradient-to-r from-[#25F4EE] via-[#25F4EE]/40 to-[#FE2C55]"></div>
                
                <div class="p-4">
                    <form id="manualSearchForm" method="GET" action="{{ route('user.wholesale.index') }}">
                        <!-- Row 1: SKU Search with Icon -->
                        <div class="mb-3">
                            <div class="flex items-center gap-2 p-3 bg-neutral-800/30 rounded-lg border border-neutral-700/50">
                                <div class="flex items-center gap-2 text-xs text-neutral-300 min-w-fit">
                                    <i class="fas fa-barcode text-[#FE2C55]"></i>
                                    <span class="whitespace-nowrap">Kode Produk</span>
                                </div>
                                <input type="text" name="sku" value="{{ request('sku') }}" 
                                       class="flex-1 bg-transparent border-0 text-sm text-white placeholder-neutral-400 focus:outline-none focus:ring-0" 
                                       placeholder="Scan atau ketik SKU produk">
                            </div>
                        </div>
                        
                        <!-- Row 2: Category and Price Range in horizontal layout -->
                        <div class="mb-3">
                            <div class="flex items-center gap-2">
                                <!-- Category Filter -->
                                <div class="flex items-center gap-2 p-3 bg-neutral-800/30 rounded-lg border border-neutral-700/50 flex-1">
                                    <i class="fas fa-tag text-[#25F4EE] text-xs"></i>
                                    <select name="category_id" class="bg-transparent border-0 text-sm text-white focus:outline-none focus:ring-0 flex-1">
                                        <option value="">Pilih Kategori</option>
                                        @if(isset($categories) && $categories)
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                
                                <!-- Price Range -->
                                <div class="flex items-center gap-1">
                                    <div class="flex items-center gap-1 p-3 bg-neutral-800/30 rounded-lg border border-neutral-700/50">
                                        <i class="fas fa-dollar-sign text-[#FE2C55] text-xs"></i>
                                        <input type="number" name="price_min" value="{{ request('price_min') }}" 
                                               class="price-input bg-transparent border-0 text-sm text-white placeholder-neutral-400 focus:outline-none focus:ring-0 w-20" 
                                               placeholder="Min">
                                    </div>
                                    <span class="text-neutral-500 text-xs px-1">—</span>
                                    <div class="flex items-center gap-1 p-3 bg-neutral-800/30 rounded-lg border border-neutral-700/50">
                                        <input type="number" name="price_max" value="{{ request('price_max') }}" 
                                               class="price-input bg-transparent border-0 text-sm text-white placeholder-neutral-400 focus:outline-none focus:ring-0 w-20" 
                                               placeholder="Max">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Row 3: Product Name Search with Actions -->
                        <div class="mb-3">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-2 p-3 bg-neutral-800/30 rounded-lg border border-neutral-700/50 flex-1">
                                    <i class="fas fa-search text-[#25F4EE] text-xs"></i>
                                    <input type="text" name="product_name" value="{{ request('product_name') }}" 
                                           class="flex-1 bg-transparent border-0 text-sm text-white placeholder-neutral-400 focus:outline-none focus:ring-0" 
                                           placeholder="Cari nama produk">
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex gap-2">
                                    <button type="submit" class="px-4 py-3 bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] text-white rounded-lg hover:shadow-lg transition-all duration-200 text-xs font-medium whitespace-nowrap">
                                        <i class="fas fa-search mr-1"></i>Cari
                                    </button>
                                    <button type="button" onclick="document.getElementById('manualSearchForm').reset(); loadAllProducts();" 
                                            class="px-3 py-3 bg-neutral-700 text-neutral-300 rounded-lg hover:bg-neutral-600 transition-colors text-xs whitespace-nowrap">
                                        <i class="fas fa-undo mr-1"></i>Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Active Filter Display -->
            @if(request('ranking_type') || request('profit_type') || request('price_min') || request('price_max') || request('category_id') || request('sku') || request('product_name'))
            <div class="mb-4 p-3 bg-gradient-to-r from-[#FE2C55]/10 to-[#25F4EE]/10 border border-[#FE2C55]/20 rounded-lg">
                <div class="flex items-center space-x-2 text-sm">
                    <svg class="w-4 h-4 text-[#FE2C55]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    <span class="text-neutral-300">Filter Aktif:</span>
                    
                    @if(request('ranking_type') == 'best_seller')
                        <span class="px-2 py-1 bg-[#FE2C55]/20 text-[#FE2C55] rounded text-xs font-medium">
                            Penjualan Terbanyak ({{ request('ranking_limit', 20) }} Produk)
                        </span>
                    @endif
                    
                    @if(request('profit_type') == 'profit')
                        <span class="px-2 py-1 bg-[#25F4EE]/20 text-[#25F4EE] rounded text-xs font-medium">
                            Keuntungan Terbaik (Top {{ request('profit_limit', 20) }})
                        </span>
                    @endif
                    
                    @if(request('price_min') || request('price_max'))
                        <span class="px-2 py-1 bg-[#25F4EE]/20 text-[#25F4EE] rounded text-xs font-medium">
                            Harga: 
                            @if(request('price_min'))Rp {{ number_format(request('price_min'), 0, ',', '.') }}@endif
                            @if(request('price_min') && request('price_max')) - @endif
                            @if(request('price_max'))Rp {{ number_format(request('price_max'), 0, ',', '.') }}@endif
                        </span>
                    @endif
                    
                    @if(request('category_id') && isset($categories))
                        @php($selectedCategory = $categories->find(request('category_id')))
                        @if($selectedCategory)
                            <span class="px-2 py-1 bg-[#25F4EE]/20 text-[#25F4EE] rounded text-xs font-medium">
                                Kategori: {{ $selectedCategory->name }}
                            </span>
                        @endif
                    @endif
                    
                    @if(request('sku'))
                        <span class="px-2 py-1 bg-[#25F4EE]/20 text-[#25F4EE] rounded text-xs font-medium">
                            SKU: {{ request('sku') }}
                        </span>
                    @endif
                    
                    @if(request('product_name'))
                        <span class="px-2 py-1 bg-[#25F4EE]/20 text-[#25F4EE] rounded text-xs font-medium">
                            Nama: {{ request('product_name') }}
                        </span>
                    @endif
                    
                    <a href="{{ route('user.wholesale.index') }}" class="ml-auto text-xs text-neutral-400 hover:text-neutral-200 underline">
                        Reset Filter
                    </a>
                </div>
            </div>
            @endif

            <!-- Product Selection Counter -->
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-neutral-200">Produk yang dipilih (<span id="selectedCount">0</span>)</p>
                <button id="clearButton" class="px-3 py-1.5 text-xs font-medium bg-neutral-700 text-white rounded-lg hover:bg-neutral-600">Kosong</button>
            </div>

            <!-- Products Grid -->
            <div id="productsGrid" class="grid grid-cols-2 gap-4 mb-6">
                <!-- Loading State -->
                <div id="loadingState" class="col-span-2 text-center py-12 hidden">
                    <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] flex items-center justify-center mb-4 animate-spin">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-neutral-200 mb-2">Loading...</h4>
                    <p class="text-sm text-neutral-400">Sedang memuat produk terbaik</p>
                </div>

                <!-- Products Container -->
                <div id="productsContainer" class="col-span-2 grid grid-cols-2 gap-4">
                    @forelse($featuredProducts ?? [] as $product)
                    <div class="bg-[#23272b] border border-[#2c3136] rounded-xl overflow-hidden relative">
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
                            
                            <!-- Stock Info -->
                            <p class="text-xs text-neutral-400 mb-3">Stok: {{ $product->stock }}</p>
                            
                            <!-- Distribute Button -->
                            <button class="w-full py-2 text-xs font-medium bg-[#FE2C55] text-white rounded-lg hover:bg-[#FE2C55]/90 transition">
                                Distribusi
                            </button>
                        </div>
                    </div>
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
                </div>
            </div>

            <!-- Bottom Actions - Fixed Button -->
            <div class="fixed bottom-16 left-0 right-0 z-40 bg-[#1a1d21]/95 backdrop-blur-sm border-t border-neutral-800/70 p-4 shadow-lg">
                <div class="max-w-sm mx-auto px-2">
                    <button id="confirmButton" class="w-full py-3 bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200 active:scale-[0.98] text-center">
                        Konfirmasi (0 Produk)
                    </button>
                </div>
            </div>
            
            <!-- Add padding bottom to account for fixed button and navbar -->
            <div class="h-32"></div>
        </div>
    </div>

    <style>
        /* Custom responsive styles for wholesale page */
        @media (max-width: 640px) {
            /* Mobile layout adjustments */
            #manualFilterSection .flex.items-center.gap-2 {
                flex-direction: column;
                gap: 0.75rem;
            }
            
            #manualFilterSection .flex.items-center.gap-2 > .flex-1 {
                width: 100%;
            }
            
            #manualFilterSection .flex.items-center.gap-1 {
                justify-content: space-between;
                width: 100%;
            }
            
            /* Price range inputs on mobile */
            #manualFilterSection .flex.items-center.gap-1 > div {
                flex: 1;
            }
            
            /* Action buttons on mobile */
            #manualFilterSection .flex.gap-2 {
                width: 100%;
                justify-content: stretch;
            }
            
            #manualFilterSection .flex.gap-2 button {
                flex: 1;
            }
            
            /* Product grid responsive */
            .grid.grid-cols-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 0.75rem;
            }
            
            /* Tab buttons responsive - only for main tabs */
            .flex.bg-neutral-800 > button {
                font-size: 0.75rem;
                padding: 0.5rem 0.75rem;
            }
            
            /* Quick filter buttons on mobile */
            .grid.grid-cols-3 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 0.5rem;
            }
        }
        
        @media (max-width: 480px) {
            /* Very small screens */
            #manualFilterSection input[type="number"] {
                width: 100%;
                min-width: auto;
            }
            
            /* Make sure icons don't shrink */
            .fas {
                flex-shrink: 0;
            }
        }
        
        /* Custom focus styles */
        #manualFilterSection input:focus,
        #manualFilterSection select:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(254, 44, 85, 0.2);
        }
        
        /* Better touch targets on mobile - only for manual filter section */
        @media (max-width: 768px) {
            #manualFilterSection button, 
            #manualFilterSection input, 
            #manualFilterSection select {
                min-height: 44px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let selectedProducts = 0;
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            const confirmButton = document.getElementById('confirmButton');
            const clearButton = document.getElementById('clearButton');
            const selectedCount = document.getElementById('selectedCount');
            
            // Quick Filter Tab functionality (State Changer)
            const tabDaftarCepat = document.getElementById('tabDaftarCepat');
            const tabPilihanManual = document.getElementById('tabPilihanManual');
            const tabPilihanStrategis = document.getElementById('tabPilihanStrategis');
            const filterSection = document.getElementById('filterSection');
            const manualFilterSection = document.getElementById('manualFilterSection');
            
            // Filter buttons - Define early to avoid reference errors
            const rankingButtons = document.querySelectorAll('.ranking-btn');
            const profitButtons = document.querySelectorAll('.profit-btn');
            const productsContainer = document.getElementById('productsContainer');
            const loadingState = document.getElementById('loadingState');
            
            // Debug: Check if elements are found
            console.log('Tab elements found:', {
                tabDaftarCepat: !!tabDaftarCepat,
                tabPilihanManual: !!tabPilihanManual,
                tabPilihanStrategis: !!tabPilihanStrategis,
                filterSection: !!filterSection,
                manualFilterSection: !!manualFilterSection,
                rankingButtons: rankingButtons.length,
                profitButtons: profitButtons.length
            });
            
            // Update UI elements
            function updateUI() {
                const text = `Konfirmasi (${selectedProducts} Produk)`;
                if (confirmButton) {
                    confirmButton.textContent = text;
                }
                if (selectedCount) {
                    selectedCount.textContent = selectedProducts;
                }
            }
            
            // Function to set button to active state (PINK!)
            function setButtonActive(button) {
                // Remove all background classes and add pink background
                button.classList.remove('bg-neutral-700/50', 'text-neutral-300', 'hover:bg-neutral-600/50');
                button.style.backgroundColor = '#FE2C55';
                button.style.color = 'white';
                button.classList.add('text-white');
            }
            
            // Function to set button to inactive state
            function setButtonInactive(button) {
                // Remove active styles and add inactive classes
                button.style.backgroundColor = '';
                button.style.color = '';
                button.classList.remove('text-white');
                button.classList.add('bg-neutral-700/50', 'text-neutral-300', 'hover:bg-neutral-600/50');
            }
            
            // Function to reset all filters
            function resetAllFilters() {
                console.log('Resetting all filters');
                
                // Reset ranking buttons
                [...rankingButtons, ...profitButtons].forEach(btn => {
                    setButtonInactive(btn);
                });
                
                // Clear URL parameters
                const url = new URL(window.location);
                url.searchParams.delete('ranking_type');
                url.searchParams.delete('ranking_limit');
                url.searchParams.delete('profit_type');
                url.searchParams.delete('profit_limit');
                url.searchParams.delete('price_min');
                url.searchParams.delete('price_max');
                url.searchParams.delete('category_id');
                url.searchParams.delete('sku');
                url.searchParams.delete('product_name');
                
                // Update URL without reload
                window.history.pushState({}, '', url.toString());
            }
            
            // Function to reset manual form
            function resetManualForm() {
                const form = document.getElementById('manualSearchForm');
                if (form) {
                    form.reset();
                }
            }
            
            // Function to show loading state
            function showLoading() {
                productsContainer.style.display = 'none';
                loadingState.style.display = 'block';
            }
            
            // Function to hide loading state
            function hideLoading() {
                loadingState.style.display = 'none';
                productsContainer.style.display = 'grid';
            }
            
            // Generic function to load products via AJAX
            async function loadProductsAjax(params = {}) {
                const url = new URL(window.location.origin + window.location.pathname);
                
                // Add parameters
                Object.keys(params).forEach(key => {
                    if (params[key]) {
                        url.searchParams.set(key, params[key]);
                    }
                });
                
                url.searchParams.set('ajax', '1');
                
                try {
                    showLoading();
                    
                    const response = await fetch(url.toString(), {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    });
                    
                    if (!response.ok) throw new Error('Network response was not ok');
                    
                    const data = await response.json();
                    
                    // Update products grid
                    productsContainer.innerHTML = data.html;
                    
                    // Re-bind checkbox events
                    bindCheckboxEvents();
                    
                    // Auto-select all products after filter change
                    autoSelectAllProducts();
                    
                } catch (error) {
                    console.error('Error loading products:', error);
                } finally {
                    hideLoading();
                }
            }
            
            // Function to load default products (Best 20)
            async function loadDefaultProducts() {
                console.log('Loading default products (Best 20)');
                
                // Set Best 20 as active
                const defaultBtn = Array.from(rankingButtons).find(btn => 
                    btn.dataset.rankingType === 'best_seller' && btn.dataset.rankingLimit === '20'
                );
                if (defaultBtn) {
                    setButtonActive(defaultBtn);
                }
                
                // Load products via AJAX
                await loadProductsAjax({
                    ranking_type: 'best_seller',
                    ranking_limit: '20'
                });
            }
            
            // Function to load all products for manual search
            async function loadAllProducts() {
                console.log('Loading all products for manual search');
                
                await loadProductsAjax({});
            }
            
            // Function to switch tabs
            function switchTab(activeTab) {
                console.log('Switching to tab:', activeTab.id); // Debug log
                
                // Remove active class from all tabs
                [tabDaftarCepat, tabPilihanManual, tabPilihanStrategis].forEach(tab => {
                    tab.classList.remove('bg-[#FE2C55]', 'text-white');
                    tab.classList.add('text-neutral-400');
                });
                
                // Add active class to clicked tab
                activeTab.classList.remove('text-neutral-400');
                activeTab.classList.add('bg-[#FE2C55]', 'text-white');
                
                // Reset filters when switching tabs
                resetAllFilters();
                
                // Show/hide filter sections based on active tab
                if (activeTab === tabDaftarCepat) {
                    console.log('Showing Daftar Cepat section'); // Debug log
                    filterSection.style.display = 'block';
                    manualFilterSection.style.display = 'none';
                    
                    // Load default best seller products
                    loadDefaultProducts();
                    
                } else if (activeTab === tabPilihanManual) {
                    console.log('Showing Manual Filter section'); // Debug log
                    filterSection.style.display = 'none';
                    manualFilterSection.style.display = 'block';
                    
                    // Reset manual form
                    resetManualForm();
                    // Load all products for manual filtering
                    loadAllProducts();
                    
                } else {
                    console.log('Hiding all sections'); // Debug log
                    filterSection.style.display = 'none';
                    manualFilterSection.style.display = 'none';
                    
                    // Load default products
                    loadDefaultProducts();
                }
            }
            
            // Add event listeners to checkboxes
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        selectedProducts++;
                    } else {
                        selectedProducts--;
                    }
                    updateUI();
                });
            });
            
            // Clear button functionality
            if (clearButton) {
                clearButton.addEventListener('click', function() {
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    selectedProducts = 0;
                    updateUI();
                });
            }
            
            // Check URL params to determine initial tab state
            const initialUrlParams = new URLSearchParams(window.location.search);
            const hasManualFilters = initialUrlParams.get('price_min') || initialUrlParams.get('price_max') || 
                                    initialUrlParams.get('category_id') || initialUrlParams.get('sku') || 
                                    initialUrlParams.get('product_name');
            
            // Set initial tab based on filters
            if (hasManualFilters) {
                switchTab(tabPilihanManual);
            } else {
                switchTab(tabDaftarCepat);
            }
            
            // Add event listeners to tabs with additional safety checks
            if (tabDaftarCepat) {
                tabDaftarCepat.addEventListener('click', (e) => {
                    e.preventDefault();
                    console.log('Daftar Cepat clicked'); // Debug log
                    switchTab(tabDaftarCepat);
                });
            }
            
            if (tabPilihanManual) {
                tabPilihanManual.addEventListener('click', (e) => {
                    e.preventDefault();
                    console.log('Pilihan Manual clicked'); // Debug log
                    switchTab(tabPilihanManual);
                });
            }
            
            if (tabPilihanStrategis) {
                tabPilihanStrategis.addEventListener('click', (e) => {
                    e.preventDefault();
                    console.log('Pilihan Strategis clicked'); // Debug log
                    switchTab(tabPilihanStrategis);
                });
            }
            // Function to initialize button states on page load
            function initializeButtonStates() {
                // Check URL parameters to set correct active states
                const urlParams = new URLSearchParams(window.location.search);
                const rankingType = urlParams.get('ranking_type');
                const rankingLimit = urlParams.get('ranking_limit');
                const profitType = urlParams.get('profit_type');
                const profitLimit = urlParams.get('profit_limit');
                
                // Reset all buttons to inactive state first
                [...rankingButtons, ...profitButtons].forEach(btn => {
                    setButtonInactive(btn);
                });
                
                // Activate the correct button based on URL params
                if (profitType === 'profit') {
                    // Profit filter is active
                    const activeBtn = Array.from(profitButtons).find(btn => 
                        btn.dataset.profitType === profitType && btn.dataset.profitLimit === profitLimit
                    );
                    if (activeBtn) {
                        setButtonActive(activeBtn);
                    }
                } else if (rankingType === 'best_seller') {
                    // Ranking filter is active
                    const activeBtn = Array.from(rankingButtons).find(btn => 
                        btn.dataset.rankingType === rankingType && btn.dataset.rankingLimit === rankingLimit
                    );
                    if (activeBtn) {
                        setButtonActive(activeBtn);
                    }
                } else {
                    // Default: activate Best 20
                    const defaultBtn = Array.from(rankingButtons).find(btn => 
                        btn.dataset.rankingType === 'best_seller' && btn.dataset.rankingLimit === '20'
                    );
                    if (defaultBtn) {
                        setButtonActive(defaultBtn);
                    }
                }
            }
            
            // Initialize button states on page load
            initializeButtonStates();
            
            // Auto-select all products on initial page load (if any filter is active)
            const autoSelectUrlParams = new URLSearchParams(window.location.search);
            if (autoSelectUrlParams.get('ranking_type') || autoSelectUrlParams.get('profit_type') || (!autoSelectUrlParams.get('ranking_type') && !autoSelectUrlParams.get('profit_type'))) {
                // Auto-select all products on page load (including default Best 20)
                setTimeout(() => {
                    autoSelectAllProducts();
                }, 100); // Small delay to ensure DOM is ready
            }
            
            // Function to apply filters with AJAX (Next.js style!)
            async function applyFiltersAjax() {
                const url = new URL(window.location);
                
                // Get active ranking filter
                const activeRankingBtn = Array.from(document.querySelectorAll('.ranking-btn')).find(btn => 
                    btn.style.backgroundColor === 'rgb(254, 44, 85)' || btn.style.backgroundColor === '#FE2C55'
                );
                if (activeRankingBtn) {
                    url.searchParams.set('ranking_type', activeRankingBtn.dataset.rankingType);
                    url.searchParams.set('ranking_limit', activeRankingBtn.dataset.rankingLimit);
                } else {
                    url.searchParams.delete('ranking_type');
                    url.searchParams.delete('ranking_limit');
                }
                
                // Get active profit filter
                const activeProfitBtn = Array.from(document.querySelectorAll('.profit-btn')).find(btn => 
                    btn.style.backgroundColor === 'rgb(254, 44, 85)' || btn.style.backgroundColor === '#FE2C55'
                );
                if (activeProfitBtn) {
                    url.searchParams.set('profit_type', activeProfitBtn.dataset.profitType);
                    url.searchParams.set('profit_limit', activeProfitBtn.dataset.profitLimit);
                } else {
                    url.searchParams.delete('profit_type');
                    url.searchParams.delete('profit_limit');
                }
                
                // Add AJAX flag
                url.searchParams.set('ajax', '1');
                
                try {
                    showLoading();
                    
                    const response = await fetch(url.toString(), {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    });
                    
                    if (!response.ok) throw new Error('Network response was not ok');
                    
                    const data = await response.json();
                    
                    // Update products grid
                    productsContainer.innerHTML = data.html;
                    
                    // Update URL without reload
                    url.searchParams.delete('ajax');
                    window.history.pushState({}, '', url.toString());
                    
                    // Update active filter display
                    updateActiveFilterDisplay(data.filters);
                    
                    // Re-bind checkbox events
                    bindCheckboxEvents();
                    
                    // Auto-select all products after filter change
                    autoSelectAllProducts();
                    
                } catch (error) {
                    console.error('Error loading products:', error);
                    // Fallback to page reload if AJAX fails
                    window.location.href = url.toString().replace('&ajax=1', '');
                } finally {
                    hideLoading();
                }
            }
            
            // Function to update active filter display
            function updateActiveFilterDisplay(filters) {
                const filterDisplay = document.querySelector('.mb-4.p-3.bg-gradient-to-r');
                if (filters.hasFilters && filterDisplay) {
                    filterDisplay.style.display = 'block';
                    // Update filter text dynamically
                } else if (filterDisplay) {
                    filterDisplay.style.display = 'none';
                }
            }
            
            // Function to auto-select all products after filter change
            function autoSelectAllProducts() {
                const checkboxes = document.querySelectorAll('#productsContainer input[type="checkbox"]');
                selectedProducts = 0; // Reset counter
                
                checkboxes.forEach(checkbox => {
                    checkbox.checked = true;
                    selectedProducts++;
                });
                
                updateUI();
            }
            
            // Function to re-bind checkbox events after AJAX
            function bindCheckboxEvents() {
                const newCheckboxes = document.querySelectorAll('input[type="checkbox"]');
                newCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            selectedProducts++;
                        } else {
                            selectedProducts--;
                        }
                        updateUI();
                    });
                });
            }
            
            // Handle ranking buttons
            rankingButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // MUTUALLY EXCLUSIVE: Deactivate ALL profit buttons first
                    profitButtons.forEach(btn => {
                        setButtonInactive(btn);
                    });
                    
                    // Remove active class from all ranking buttons
                    rankingButtons.forEach(btn => {
                        setButtonInactive(btn);
                    });
                    
                    // Add active class to clicked button (PINK!)
                    setButtonActive(this);
                    
                    // Apply filters with AJAX (super fast!)
                    applyFiltersAjax();
                });
            });
            
            // Handle profit buttons
            profitButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // MUTUALLY EXCLUSIVE: Deactivate ALL ranking buttons first
                    rankingButtons.forEach(btn => {
                        setButtonInactive(btn);
                    });
                    
                    // Remove active class from all profit buttons
                    profitButtons.forEach(btn => {
                        setButtonInactive(btn);
                    });
                    
                    // Add active class to clicked button (PINK!)
                    setButtonActive(this);
                    
                    // Apply filters with AJAX (super fast!)
                    applyFiltersAjax();
                });
            });
            
            // Price input formatting (remove dots for submission)
            const priceInputs = document.querySelectorAll('.price-input');
            priceInputs.forEach(input => {
                input.addEventListener('input', function() {
                    // Remove non-numeric characters
                    let value = this.value.replace(/[^0-9]/g, '');
                    
                    // Format with thousand separators for display (optional)
                    if (value) {
                        // Store the raw value for form submission
                        this.setAttribute('data-raw-value', value);
                        // Display formatted value (you can comment this line if you want plain numbers)
                        // this.value = new Intl.NumberFormat('id-ID').format(value);
                        this.value = value; // Keep it simple for now
                    }
                });
            });
        });
    </script>
</x-app-layout>
