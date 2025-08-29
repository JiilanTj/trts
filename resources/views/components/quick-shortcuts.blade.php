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
    </div>
</div>
