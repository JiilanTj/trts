<x-admin-layout>
    <x-slot name="title">Toko {{ $user->sellerInfo->store_name ?? $user->full_name }}</x-slot>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
                                                  <button type="button" 
                                                    class="w-full bg-gradient-to-r from-yellow-400 to-yellow-500 hover:from-yellow-500 hover:to-yellow-600 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 transform hover:scale-105"
                                                    onclick="showProductDetail({{ json_encode([
                                                        'id' => $showcase->product->id ?? 0,
                                                        'name' => $showcase->product->name ?? 'Produk Tidak Ditemukan',
                                                        'image' => $showcase->product->image_url ?? null,
                                                        'category' => $showcase->product->category->name ?? 'Tanpa Kategori',
                                                        'description' => $showcase->description ?? $showcase->product->description ?? 'Tidak ada deskripsi',
                                                        'price' => $showcase->price,
                                                        'original_price' => $showcase->original_price,
                                                        'is_featured' => $showcase->is_featured,
                                                        'stock' => $showcase->product->stock ?? 0,
                                                        'weight' => $showcase->product->weight ?? null,
                                                        'dimensions' => $showcase->product->dimensions ?? null,
                                                        'created_at' => $showcase->created_at->format('d M Y')
                                                    ]) }})">
                                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Lihat Detail
                                            </button>webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        /* Custom scrollbar for horizontal scroll */
        .overflow-x-auto::-webkit-scrollbar {
            height: 6px;
        }
        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h4a1 1 0 011 1v5m-6 0V9a1 1 0 011-1h4a1 1 0 011 1v11"></path>
                        </svg>
                        Toko {{ $user->sellerInfo->store_name ?? $user->full_name }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Tampilan publik etalase - Hanya produk aktif</p>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.showcases.show', $user->id) }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Manajemen
                    </a>
                    <a href="{{ route('admin.showcases.index') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        Semua Etalase
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Store Header -->
            <div class="mb-8">
                <div class="text-center py-12 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600">
                    <div class="mb-6">
                        @if($user->sellerInfo && $user->sellerInfo->store_logo)
                            <img src="{{ Storage::url($user->sellerInfo->store_logo) }}" 
                                 alt="{{ $user->sellerInfo->store_name ?? $user->full_name }}" 
                                 class="w-32 h-32 rounded-full object-cover border-4 border-white mx-auto shadow-lg">
                        @else
                            <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center border-4 border-white mx-auto shadow-lg">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h4a1 1 0 011 1v5m-6 0V9a1 1 0 011-1h4a1 1 0 011 1v11"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-3">{{ $user->sellerInfo->store_name ?? $user->full_name }}</h1>
                    @if($user->sellerInfo && $user->sellerInfo->description)
                        <p class="text-blue-100 text-lg mb-4">{{ $user->sellerInfo->description }}</p>
                    @endif
                    <div class="inline-flex items-center bg-white text-gray-800 px-4 py-2 rounded-full font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4-8-4m16 0v10l-8 4-8-4V7"></path>
                        </svg>
                        {{ $showcases->count() }} Produk Tersedia
                    </div>
                </div>
            </div>

            <!-- Products Showcase -->
            @if($showcases->count() > 0)
                @php
                    $featuredShowcases = $showcases->where('is_featured', true);
                    $regularShowcases = $showcases->where('is_featured', false);
                @endphp

                <!-- Featured Products Section -->
                @if($featuredShowcases->count() > 0)
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                Produk Unggulan
                            </h2>
                            <span class="text-sm text-gray-500">{{ $featuredShowcases->count() }} produk</span>
                        </div>
                        
                        <!-- Horizontal Scrollable Featured Products -->
                        <div class="overflow-x-auto pb-4">
                            <div class="flex space-x-4" style="width: max-content;">
                                @foreach($featuredShowcases as $showcase)
                                    <div class="flex-shrink-0 w-80 bg-white rounded-xl shadow-lg border border-yellow-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                                        <!-- Product Image -->
                                        <div class="relative">
                                            @if($showcase->product && $showcase->product->image_url)
                                                <img src="{{ $showcase->product->image_url }}" 
                                                     alt="{{ $showcase->product->name }}" 
                                                     class="w-full h-48 object-cover">
                                            @else
                                                <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
                                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                            
                                            <!-- Featured Badge -->
                                            <div class="absolute top-3 left-3">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 shadow-sm">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                    UNGGULAN
                                                </span>
                                            </div>

                                            <!-- Discount Badge -->
                                            @if($showcase->original_price > $showcase->price)
                                                <div class="absolute top-3 right-3">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-500 text-white shadow-sm">
                                                        {{ round((($showcase->original_price - $showcase->price) / $showcase->original_price) * 100) }}% OFF
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="p-4">
                                            <!-- Product Name -->
                                            <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2 min-h-[3.5rem]">
                                                {{ $showcase->product->name ?? 'Produk Tidak Ditemukan' }}
                                            </h3>
                                            
                                            <!-- Category -->
                                            <p class="text-sm text-gray-500 mb-3 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                </svg>
                                                {{ $showcase->product->category->name ?? 'Tanpa Kategori' }}
                                            </p>

                                            <!-- Description -->
                                            @if($showcase->description)
                                                <p class="text-sm text-gray-600 mb-4 line-clamp-2 min-h-[2.5rem]">
                                                    {{ Str::limit($showcase->description, 80) }}
                                                </p>
                                            @endif

                                            <!-- Pricing -->
                                            <div class="mb-4">
                                                <div class="text-xl font-bold text-green-600">
                                                    Rp {{ number_format($showcase->price, 0, ',', '.') }}
                                                </div>
                                                @if($showcase->original_price > $showcase->price)
                                                    <div class="text-sm text-gray-500 line-through">
                                                        Rp {{ number_format($showcase->original_price, 0, ',', '.') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <button type="button" 
                                                    onclick="showProductDetail({{ json_encode($showcase) }})" 
                                                    class="w-full bg-gradient-to-r from-yellow-400 to-yellow-500 hover:from-yellow-500 hover:to-yellow-600 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 transform hover:scale-105">
                                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Lihat Detail
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Regular Products Section -->
                @if($regularShowcases->count() > 0)
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4-8-4m16 0v10l-8 4-8-4V7"></path>
                                </svg>
                                Produk Lainnya
                            </h2>
                            <span class="text-sm text-gray-500">{{ $regularShowcases->count() }} produk</span>
                        </div>
                        
                        <!-- Grid Layout for Regular Products -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach($regularShowcases as $showcase)
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                                    <!-- Product Image -->
                                    <div class="relative">
                                        @if($showcase->product && $showcase->product->image_url)
                                            <img src="{{ $showcase->product->image_url }}" 
                                                 alt="{{ $showcase->product->name }}" 
                                                 class="w-full h-48 object-cover">
                                        @else
                                            <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        
                                        <!-- Discount Badge -->
                                        @if($showcase->original_price > $showcase->price)
                                            <div class="absolute top-3 right-3">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    {{ round((($showcase->original_price - $showcase->price) / $showcase->original_price) * 100) }}% OFF
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="p-4">
                                        <!-- Product Name -->
                                        <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2 min-h-[3.5rem]">
                                            {{ $showcase->product->name ?? 'Produk Tidak Ditemukan' }}
                                        </h3>
                                        
                                        <!-- Category -->
                                        <p class="text-sm text-gray-500 mb-3 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                            {{ $showcase->product->category->name ?? 'Tanpa Kategori' }}
                                        </p>

                                        <!-- Description -->
                                        @if($showcase->description)
                                            <p class="text-sm text-gray-600 mb-4 line-clamp-2 min-h-[2.5rem]">
                                                {{ Str::limit($showcase->description, 80) }}
                                            </p>
                                        @endif

                                        <!-- Pricing -->
                                        <div class="mb-4">
                                            <div class="text-xl font-bold text-green-600">
                                                Rp {{ number_format($showcase->price, 0, ',', '.') }}
                                            </div>
                                            @if($showcase->original_price > $showcase->price)
                                                <div class="text-sm text-gray-500 line-through">
                                                    Rp {{ number_format($showcase->original_price, 0, ',', '.') }}
                                                </div>
                                            @endif
                                        </div>                        <button type="button" 
                                onclick="showProductDetail({{ json_encode($showcase) }})" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Lihat Detail
                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-20 w-20 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293L16 17.586a1 1 0 01-1.414 0L12 15l-2.586 2.586a1 1 0 01-1.414 0L5.414 15.293A1 1 0 004.828 15H2"></path>
                    </svg>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">Tidak Ada Produk Tersedia</h3>
                    <p class="text-gray-500 text-lg">
                        {{ $user->sellerInfo->store_name ?? $user->full_name }} belum menambahkan produk ke etalase mereka.
                    </p>
                </div>
            @endif

            <!-- Store Information -->
            @if($user->sellerInfo)
                <div class="mt-8">
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-6 text-center">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Informasi Toko
                        </h3>
                        <div class="max-w-2xl mx-auto">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-medium text-gray-700 mb-2">Pemilik Toko:</h4>
                                    <p class="text-gray-900">{{ $user->full_name }}</p>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-700 mb-2">Skor Kredit:</h4>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ $user->sellerInfo->credit_score ?? 0 }} Poin
                                    </span>
                                </div>
                            </div>
                            @if($user->sellerInfo->description)
                                <div class="mt-6">
                                    <h4 class="font-medium text-gray-700 mb-2">Tentang Toko Ini:</h4>
                                    <p class="text-gray-600 leading-relaxed">{{ $user->sellerInfo->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Product Detail Modal -->
    <div id="productModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-8 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 xl:w-1/2 shadow-lg rounded-lg bg-white">
            <div class="flex items-center justify-between pb-3 border-b">
                <h3 class="text-xl font-semibold text-gray-800" id="modalTitle">
                    Detail Produk
                </h3>
                <button onclick="closeProductModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div id="modalContent" class="mt-4">
                <!-- Content will be populated by JavaScript -->
                <div class="animate-pulse">
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentProductData = null;

        function showProductDetail(showcase) {
            currentProductData = showcase;
            
            const modal = document.getElementById('productModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalContent = document.getElementById('modalContent');
            
            // Set title
            modalTitle.textContent = showcase.product ? showcase.product.name : 'Produk Tidak Ditemukan';
            
            // Build content
            let content = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Product Image -->
                    <div class="space-y-4">
                        <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
            `;
            
            if (showcase.product && showcase.product.image_url) {
                content += `
                    <img src="${showcase.product.image_url}" 
                         alt="${showcase.product.name}" 
                         class="w-full h-full object-cover">
                `;
            } else {
                content += `
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                `;
            }
            
            content += `
                        </div>
                        
                        <!-- Badges -->
                        <div class="flex flex-wrap gap-2">
            `;
            
            if (showcase.is_featured) {
                content += `
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        Produk Unggulan
                    </span>
                `;
            }
            
            if (showcase.is_active) {
                content += `
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        Aktif
                    </span>
                `;
            }
            
            if (showcase.original_price > showcase.price) {
                const discount = Math.round(((showcase.original_price - showcase.price) / showcase.original_price) * 100);
                content += `
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        ${discount}% OFF
                    </span>
                `;
            }
            
            content += `
                        </div>
                    </div>
                    
                    <!-- Product Details -->
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-2">${showcase.product ? showcase.product.name : 'Produk Tidak Ditemukan'}</h4>
                            <p class="text-gray-600 text-sm">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                Kategori: ${showcase.product && showcase.product.category ? showcase.product.category.name : 'Tanpa Kategori'}
                            </p>
                        </div>
                        
                        <!-- Pricing -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-green-600 mb-1">
                                Rp ${new Intl.NumberFormat('id-ID').format(showcase.price)}
                            </div>
            `;
            
            if (showcase.original_price > showcase.price) {
                content += `
                    <div class="text-lg text-gray-500 line-through">
                        Rp ${new Intl.NumberFormat('id-ID').format(showcase.original_price)}
                    </div>
                    <div class="text-sm text-green-600 font-medium">
                        Hemat Rp ${new Intl.NumberFormat('id-ID').format(showcase.original_price - showcase.price)}
                    </div>
                `;
            }
            
            content += `
                        </div>
                        
                        <!-- Description -->
            `;
            
            if (showcase.description) {
                content += `
                    <div>
                        <h5 class="font-medium text-gray-800 mb-2">Deskripsi Etalase:</h5>
                        <p class="text-gray-600 leading-relaxed">${showcase.description}</p>
                    </div>
                `;
            }
            
            content += `
                        <!-- Meta Information -->
                        <div class="space-y-2 text-sm text-gray-500">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                </svg>
                                Urutan Tampil: ${showcase.sort_order}
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Ditambahkan: ${new Date(showcase.created_at).toLocaleDateString('id-ID', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })}
                            </div>
            `;
            
            if (showcase.is_featured && showcase.featured_until) {
                content += `
                    <div class="flex items-center text-yellow-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        Unggulan sampai: ${new Date(showcase.featured_until).toLocaleDateString('id-ID', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        })}
                    </div>
                `;
            }
            
            content += `
                        </div>
                    </div>
                </div>
            `;
            
            modalContent.innerHTML = content;
            modal.classList.remove('hidden');
        }
        
        function closeProductModal() {
            document.getElementById('productModal').classList.add('hidden');
        }
        
        // Close modal on background click
        document.getElementById('productModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeProductModal();
            }
        });
        
        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeProductModal();
            }
        });
    </script>
</x-admin-layout>
