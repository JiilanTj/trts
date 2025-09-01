<x-admin-layout>
    <x-slot name="title">Ubah Produk</x-slot>

    <div class="bg-white rounded-lg shadow-md border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Ubah Produk</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.products.show', $product) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 text-sm">Lihat Produk</a>
                    <a href="{{ route('admin.products.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200 text-sm">Kembali ke Produk</a>
                </div>
            </div>
        </div>

        <div class="p-6">
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="product-form">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Informasi Dasar -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2">Informasi Dasar</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                                <input name="name" value="{{ old('name',$product->name) }}" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                                <input name="sku" value="{{ old('sku',$product->sku) }}" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                <select name="category_id" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}" @selected(old('category_id',$product->category_id)==$c->id)>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                <textarea name="description" rows="4" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('description',$product->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Harga & Persediaan -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2">Harga & Persediaan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="space-y-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="active" @selected(old('status',$product->status)==='active')>Aktif</option>
                                    <option value="inactive" @selected(old('status',$product->status)==='inactive')>Tidak Aktif</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Stok</label>
                                <input type="number" min="0" name="stock" value="{{ old('stock',$product->stock) }}" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Berat (gram)</label>
                                <input type="number" min="0" name="weight" value="{{ old('weight',$product->weight) }}" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kadaluarsa</label>
                                <input type="date" name="expiry_date" value="{{ old('expiry_date',$product->expiry_date?->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Harga Beli</label>
                                <input type="number" min="0" name="purchase_price" id="purchase_price" value="{{ old('purchase_price',$product->purchase_price) }}" class="price-field w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Harga Jual (External)</label>
                                <input type="number" min="0" name="sell_price" id="sell_price" value="{{ old('sell_price',$product->sell_price) }}" class="price-field w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Promo Price (Opsional)</label>
                                <input type="number" min="0" name="promo_price" id="promo_price" value="{{ old('promo_price',$product->promo_price) }}" class="price-field w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Laba (Auto)</label>
                                <div class="px-3 py-2 rounded-lg bg-gray-100 border border-gray-200 text-gray-800" id="profit-display">Rp {{ number_format($product->profit,0,',','.') }}</div>
                                <input type="hidden" name="profit" id="profit" value="{{ old('profit',$product->profit) }}" />
                            </div>
                            <div class="md:col-span-2 mt-2 p-3 rounded-lg bg-blue-50 border border-blue-200 text-xs text-gray-700">
                                <div class="font-semibold mb-1 text-blue-700">Info Harga:</div>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Harga Biasa (User) = Promo Price jika lebih rendah dari Harga Jual, selain itu Harga Jual.</li>
                                    <li>Harga Jual (External) = Harga Jual (sell_price).</li>
                                    <li>Laba = Harga Jual - Harga Beli (otomatis).</li>
                                </ul>
                                <div class="mt-2 text-blue-700" id="computed-prices"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Gambar -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2">Gambar Produk</h3>
                    @if($product->image)
                        <div class="mb-3">
                            <p class="text-xs text-gray-500 mb-1">Gambar Saat Ini:</p>
                            <img src="{{ Storage::url($product->image) }}" class="w-40 h-40 object-cover rounded-lg border border-gray-200" />
                        </div>
                    @endif
                    <div>
                        <input type="file" name="image" id="image" accept="image/*" class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                    </div>
                    <div id="image-preview" class="hidden">
                        <p class="text-xs text-gray-500 mb-1">Preview Baru:</p>
                        <img id="preview-img" alt="Preview" class="w-40 h-40 object-cover rounded-lg border border-gray-200" />
                    </div>
                </div>

                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.products.index') }}" class="px-4 py-2 rounded-lg bg-gray-200 text-gray-800 hover:bg-gray-300 text-sm">Batal</a>
                    <button type="submit" class="px-6 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 shadow">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function formatRupiah(num){
        return 'Rp ' + (num||0).toLocaleString('id-ID');
    }
    function calculateProfit() {
        const purchasePrice = parseInt(document.getElementById('purchase_price').value)||0;
        const sellPrice = parseInt(document.getElementById('sell_price').value)||0;
        const promoPrice = parseInt(document.getElementById('promo_price').value)||0;
        const profit = sellPrice - purchasePrice;
        document.getElementById('profit-display').textContent = formatRupiah(profit);
        document.getElementById('profit').value = profit;
        const hargaBiasa = (promoPrice && promoPrice < sellPrice) ? promoPrice : sellPrice;
        document.getElementById('computed-prices').innerHTML = 'Harga Biasa: <strong>'+formatRupiah(hargaBiasa)+'</strong> | Harga Jual (External): <strong>'+formatRupiah(sellPrice)+'</strong>';
    }
    ['purchase_price','sell_price','promo_price'].forEach(id=>{ const el=document.getElementById(id); if(el){ el.addEventListener('input',calculateProfit);} });
    calculateProfit();

    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                document.getElementById('preview-img').src = ev.target.result;
                document.getElementById('image-preview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            document.getElementById('image-preview').classList.add('hidden');
        }
    });
    </script>
</x-admin-layout>
