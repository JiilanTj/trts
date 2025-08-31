<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Pintasan Cepat</h3>
    </div>
    <!-- Komponen khusus user (role=user), jadi langsung pakai route browse.* -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        <a href="{{ route('browse.categories.index') }}" class="group flex flex-col items-center justify-center gap-2 p-4 border border-gray-200 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition">
            <div class="w-10 h-10 rounded-full flex items-center justify-center bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition">
                <!-- Heroicon: Squares 2x2 -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75h6.5v6.5h-6.5zM13.75 3.75h6.5v6.5h-6.5zM13.75 13.75h6.5v6.5h-6.5zM3.75 13.75h6.5v6.5h-6.5z" />
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-700 group-hover:text-blue-700">Kategori</span>
        </a>
        <a href="{{ route('browse.products.index') }}" class="group flex flex-col items-center justify-center gap-2 p-4 border border-gray-200 rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition">
            <div class="w-10 h-10 rounded-full flex items-center justify-center bg-indigo-100 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition">
                <!-- Heroicon: Cube -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5L12 3 3 7.5m18 0L12 12m9-4.5v9L12 21m0-9v9m0-9L3 7.5m9 4.5L3 7.5m0 0v9L12 21" />
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-700">Produk</span>
        </a>
        @if(auth()->user()->isSeller())
            <!-- If user is already a seller, show seller-related shortcuts -->
            <a href="{{ route('seller-requests.index') }}" class="group flex flex-col items-center justify-center gap-2 p-4 border border-gray-200 rounded-xl hover:border-green-500 hover:bg-green-50 transition">
                <div class="w-10 h-10 rounded-full flex items-center justify-center bg-green-100 text-green-600 group-hover:bg-green-600 group-hover:text-white transition">
                    <!-- Heroicon: Building Storefront -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-2.836A3 3 0 008.25 4.5c0-1.41.6-2.68 1.56-3.62a3.001 3.001 0 011.94.6 3 3 0 013.7 2.75c0 1.41.6 2.68 1.56 3.62.75.75 1.92.75 2.67 0 .96-.94 1.56-2.21 1.56-3.62a3 3 0 013.75-2.836A3.001 3.001 0 0116.5 2.85M8.25 9.75h7.5" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-green-700">Toko Saya</span>
            </a>
        @else
            <!-- If user is not a seller, show option to become one -->
            <a href="{{ route('seller-requests.index') }}" class="group flex flex-col items-center justify-center gap-2 p-4 border border-gray-200 rounded-xl hover:border-green-500 hover:bg-green-50 transition">
                <div class="w-10 h-10 rounded-full flex items-center justify-center bg-green-100 text-green-600 group-hover:bg-green-600 group-hover:text-white transition">
                    <!-- Heroicon: Building Storefront -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-2.836A3 3 0 008.25 4.5c0-1.41.6-2.68 1.56-3.62a3.001 3.001 0 011.94.6 3 3 0 013.7 2.75c0 1.41.6 2.68 1.56 3.62.75.75 1.92.75 2.67 0 .96-.94 1.56-2.21 1.56-3.62a3 3 0 013.75-2.836A3.001 3.001 0 0116.5 2.85M8.25 9.75h7.5" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-green-700">Jadi Seller</span>
            </a>
        @endif
        <a href="#" class="group flex flex-col items-center justify-center gap-2 p-4 border border-gray-200 rounded-xl hover:border-purple-500 hover:bg-purple-50 transition">
            <div class="w-10 h-10 rounded-full flex items-center justify-center bg-purple-100 text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition">
                <!-- Heroicon: Clock -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-700 group-hover:text-purple-700">History Saya</span>
        </a>
    </div>
</div>
