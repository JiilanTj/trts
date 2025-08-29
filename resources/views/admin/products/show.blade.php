<x-admin-layout>
    <x-slot name="title">{{ $product->name }}</x-slot>

    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ $product->name }}</h1>
                    <p class="text-gray-600 mt-1">SKU: {{ $product->sku }}</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        Ubah Produk
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        Kembali ke Produk
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Gambar Produk -->
                <div class="lg:col-span-1">
                    <div class="sticky top-6">
                        @if($product->image)
                            <div class="aspect-square rounded-lg overflow-hidden border-2 border-gray-200">
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="aspect-square rounded-lg bg-gray-200 flex items-center justify-center border-2 border-gray-300">
                                <div class="text-center text-gray-500">
                                    <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p>Tidak Ada Gambar</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Detail Produk -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Badge Status -->
                    <div class="flex items-center space-x-4">
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            {{ $product->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                        @if($product->stock <= 5)
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                Stok Rendah
                            </span>
                        @endif
                        @if($product->expiry_date && $product->expiry_date->isPast())
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                Kedaluwarsa
                            </span>
                        @elseif($product->expiry_date && $product->expiry_date->diffInDays() <= 30)
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                Segera Kedaluwarsa
                            </span>
                        @endif
                    </div>

                    <!-- Informasi Dasar -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Dasar</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Nama Produk</label>
                                <p class="mt-1 text-gray-900">{{ $product->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">SKU</label>
                                <p class="mt-1 text-gray-900 font-mono">{{ $product->sku }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Kategori</label>
                                <p class="mt-1">
                                    <a href="{{ route('admin.categories.show', $product->category) }}" 
                                       class="text-blue-600 hover:text-blue-800">
                                        {{ $product->category->name }}
                                    </a>
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Status</label>
                                <p class="mt-1 text-gray-900">{{ $product->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}</p>
                            </div>
                        </div>
                        @if($product->description)
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-600">Deskripsi</label>
                                <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $product->description }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Informasi Harga -->
                    <div class="bg-blue-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Harga</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Harga Beli</label>
                                <p class="mt-1 text-xl font-semibold text-gray-900">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Harga Jual</label>
                                <p class="mt-1 text-xl font-semibold text-green-600">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</p>
                            </div>
                            @if($product->promo_price)
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Harga Promo</label>
                                    <p class="mt-1 text-xl font-semibold text-orange-600">Rp {{ number_format($product->promo_price, 0, ',', '.') }}</p>
                                </div>
                            @endif
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Laba</label>
                                <p class="mt-1 text-xl font-semibold {{ $product->profit > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    Rp {{ number_format($product->profit, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Persediaan & Detail Fisik -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Persediaan & Detail Fisik</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Jumlah Stok</label>
                                <p class="mt-1 text-xl font-semibold {{ $product->stock <= 5 ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $product->stock }} unit
                                </p>
                            </div>
                            @if($product->weight)
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Berat</label>
                                    <p class="mt-1 text-gray-900">{{ number_format($product->weight, 2, ',', '.') }} gram</p>
                                </div>
                            @endif
                            @if($product->expiry_date)
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Tanggal Kedaluwarsa</label>
                                    <p class="mt-1 text-gray-900">
                                        {{ $product->expiry_date->format('d M Y') }}
                                        <span class="text-sm text-gray-500">
                                            ({{ $product->expiry_date->diffForHumans() }})
                                        </span>
                                    </p>
                                </div>
                            @endif
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Dibuat</label>
                                <p class="mt-1 text-gray-900">{{ $product->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Aksi -->
                    <div class="flex justify-between items-center pt-6 border-t">
                        <div class="flex space-x-4">
                            <a href="{{ route('admin.products.edit', $product) }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                                Ubah Produk
                            </a>
                            
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')"
                                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition duration-200">
                                    Hapus Produk
                                </button>
                            </form>
                        </div>

                        <div class="text-sm text-gray-500">
                            Terakhir diperbarui: {{ $product->updated_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
