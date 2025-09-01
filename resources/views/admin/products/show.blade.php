<x-admin-layout>
    <x-slot name="title">{{ $product->name }}</x-slot>

    <div class="bg-white rounded-lg shadow-md border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        {{ $product->name }}
                        @if($product->promo_price && $product->promo_price < $product->sell_price)
                            <span class="text-xs px-2 py-1 rounded bg-blue-600 text-white">Promo</span>
                        @endif
                    </h1>
                    <p class="text-gray-600 mt-1">SKU: {{ $product->sku }}</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 text-sm">Ubah Produk</a>
                    <a href="{{ route('admin.products.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200 text-sm">Kembali ke Produk</a>
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
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="aspect-square rounded-lg bg-gray-100 flex items-center justify-center border-2 border-gray-200">
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
                    <div class="flex items-center flex-wrap gap-3">
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $product->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $product->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                        @if($product->stock <= 5)
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Stok Menipis</span>
                        @endif
                        @if($product->expiry_date && $product->expiry_date->isPast())
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Kadaluarsa</span>
                        @elseif($product->expiry_date && $product->expiry_date->diffInDays() <= 30)
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Hampir Kadaluarsa</span>
                        @endif
                    </div>

                    <!-- Informasi Dasar -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Dasar</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Nama</p>
                                <p class="font-medium text-gray-800">{{ $product->name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Kategori</p>
                                <p class="font-medium text-gray-800">{{ $product->category?->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">SKU</p>
                                <p class="font-medium text-gray-800">{{ $product->sku }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Berat</p>
                                <p class="font-medium text-gray-800">{{ $product->weight ? $product->weight.' gr' : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Kadaluarsa</p>
                                <p class="font-medium text-gray-800">{{ $product->expiry_date?->format('d M Y') ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Dibuat</p>
                                <p class="font-medium text-gray-800">{{ $product->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        @if($product->description)
                            <div class="mt-4">
                                <p class="text-gray-500 mb-1">Deskripsi</p>
                                <p class="text-gray-700 text-sm leading-relaxed">{!! nl2br(e($product->description)) !!}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Informasi Harga -->
                    <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Harga</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Harga Beli</p>
                                <p class="font-medium text-gray-800">Rp {{ number_format($product->purchase_price,0,',','.') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Harga Jual (External)</p>
                                <p class="font-medium text-gray-800">Rp {{ number_format($product->sell_price,0,',','.') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Harga Biasa (User)</p>
                                <p class="font-medium text-gray-800">Rp {{ number_format($product->harga_biasa,0,',','.') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Laba</p>
                                <p class="font-medium text-gray-800">Rp {{ number_format($product->profit,0,',','.') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Promo Price</p>
                                <p class="font-medium text-gray-800">{{ $product->promo_price ? 'Rp '.number_format($product->promo_price,0,',','.') : '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Persediaan & Detail Fisik -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Persediaan & Detail Fisik</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Stok</p>
                                <p class="font-medium text-gray-800">{{ $product->stock }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Status</p>
                                <p class="font-medium text-gray-800">{{ $product->status }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Aksi -->
                    <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                        <div class="flex space-x-4">
                            <a href="{{ route('admin.products.edit',$product) }}" class="px-4 py-2 text-sm rounded-lg bg-blue-600 hover:bg-blue-700 text-white">Edit</a>
                            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 text-sm rounded-lg bg-gray-600 hover:bg-gray-700 text-white">Kembali</a>
                        </div>
                        <div class="text-xs text-gray-500 space-y-1">
                            <div>Diperbarui: {{ $product->updated_at->diffForHumans() }}</div>
                            <div>ID: {{ $product->id }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
