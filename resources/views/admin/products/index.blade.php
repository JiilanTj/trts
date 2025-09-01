<x-admin-layout>
    <x-slot name="title">Manajemen Produk</x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Produk</h2>
                <a href="{{ route('admin.products.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow">
                    Tambah Produk
                </a>
            </div>

            <!-- Form Pencarian & Filter -->
            <div class="mt-4">
                <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-col md:flex-row gap-3">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / SKU..." class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div class="w-full md:w-48">
                        <select name="category_id" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" @selected($category_id == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full md:w-40">
                        <select name="status" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Status</option>
                            <option value="active" @selected($status==='active')>Aktif</option>
                            <option value="inactive" @selected($status==='inactive')>Tidak Aktif</option>
                        </select>
                    </div>
                    <button type="submit" 
                            class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors shadow">
                        Cari
                    </button>
                    @if($search || $category_id || $status)
                        <a href="{{ route('admin.products.index') }}" class="text-sm px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Pesan Sukses / Error -->
        @if(session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tabel Produk -->
        <div class="overflow-x-auto mt-4">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                        <th class="px-6 py-3">Produk</th>
                        <th class="px-6 py-3">SKU</th>
                        <th class="px-6 py-3">Kategori</th>
                        <th class="px-6 py-3">Stok</th>
                        <th class="px-6 py-3">Harga Beli</th>
                        <th class="px-6 py-3">Harga Biasa</th>
                        <th class="px-6 py-3">Harga Jual</th>
                        <th class="px-6 py-3">Laba</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium text-gray-800">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded border border-gray-200 bg-gray-50 overflow-hidden flex items-center justify-center">
                                        @if($product->image)
                                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                                        @else
                                            <span class="text-[10px] text-gray-400">No Img</span>
                                        @endif
                                    </div>
                                    <div class="leading-tight">
                                        <a href="{{ route('admin.products.show',$product) }}" class="hover:text-blue-600">{{ $product->name }}</a>
                                        @if($product->promo_price && $product->promo_price < $product->sell_price)
                                            <span class="ml-2 text-xs px-2 py-0.5 rounded bg-pink-600 text-white">Promo</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $product->sku }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $product->category?->name ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-700">{{ $product->stock }}</td>
                            <td class="px-6 py-3 text-gray-700">Rp {{ number_format($product->purchase_price,0,',','.') }}</td>
                            <td class="px-6 py-3 text-gray-700">
                                Rp {{ number_format($product->harga_biasa,0,',','.') }}
                                @if($product->promo_price && $product->promo_price < $product->sell_price)
                                    <div class="text-[11px] text-pink-600">Promo: Rp {{ number_format($product->promo_price,0,',','.') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-gray-700">Rp {{ number_format($product->sell_price,0,',','.') }}</td>
                            <td class="px-6 py-3 text-gray-700">Rp {{ number_format($product->profit,0,',','.') }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $product->status==='active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $product->status==='active' ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                            <!-- Adjust Aksi buttons layout to always sit horizontally without wrapping -->
                            <td class="px-6 py-3">
                                <div class="flex justify-end items-center gap-2">
                                    <a href="{{ route('admin.products.show',$product) }}" class="whitespace-nowrap text-xs px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 inline-flex items-center">Detail</a>
                                    <a href="{{ route('admin.products.edit',$product) }}" class="whitespace-nowrap text-xs px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700 inline-flex items-center">Edit</a>
                                    <form action="{{ route('admin.products.destroy',$product) }}" method="POST" onsubmit="return confirm('Hapus produk ini? Tindakan tidak dapat dibatalkan.');" class="inline-flex">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="whitespace-nowrap text-xs px-3 py-1 rounded bg-red-600 text-white hover:bg-red-700 inline-flex items-center">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-6 text-center text-gray-500">Belum ada produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginasi -->
        @if($products->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
