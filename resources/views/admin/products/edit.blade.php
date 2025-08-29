<x-admin-layout>
    <x-slot name="title">Ubah Produk</x-slot>

    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Ubah Produk</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.products.show', $product) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        Lihat Produk
                    </a>
                        <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                            Kembali ke Produk
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Informasi Dasar -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Informasi Dasar</h3>
                            
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Produk *</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                       required>
                            </div>

                            <div>
                                <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">SKU *</label>
                                <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                       required>
                                <p class="text-sm text-gray-500 mt-1">Kode unik produk</p>
                            </div>

                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                                <select name="category_id" id="category_id" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                        required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                                <textarea name="description" id="description" rows="4" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $product->description) }}</textarea>
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" id="status" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                            </div>
                        </div>

                        <!-- Harga & Persediaan -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Harga & Persediaan</h3>
                            
                            <div>
                                <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-2">Harga Beli *</label>
                                <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" 
                                       min="0" step="0.01" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                       required>
                            </div>

                            <div>
                                <label for="sell_price" class="block text-sm font-medium text-gray-700 mb-2">Harga Jual *</label>
                                <input type="number" name="sell_price" id="sell_price" value="{{ old('sell_price', $product->sell_price) }}" 
                                       min="0" step="0.01" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                       required>
                            </div>

                            <div>
                                <label for="promo_price" class="block text-sm font-medium text-gray-700 mb-2">Harga Promo</label>
                                <input type="number" name="promo_price" id="promo_price" value="{{ old('promo_price', $product->promo_price) }}" 
                                       min="0" step="0.01" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-sm text-gray-500 mt-1">Kosongkan jika tidak ada promo</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Laba Saat Ini</label>
                                <div class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
                                    Rp {{ number_format($product->profit, 0, ',', '.') }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Perkiraan Laba Baru</label>
                                <div id="profit-display" class="w-full px-3 py-2 bg-blue-50 border border-blue-300 rounded-lg text-blue-700">
                                    Rp {{ number_format($product->profit, 0, ',', '.') }}
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Otomatis dihitung: harga jual - harga beli</p>
                            </div>

                            <div>
                                <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Stok *</label>
                                <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" 
                                       min="0" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                       required>
                            </div>

                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Berat (gram)</label>
                                <input type="number" name="weight" id="weight" value="{{ old('weight', $product->weight) }}" 
                                       min="0" step="0.01" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kedaluwarsa</label>
                                <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date', $product->expiry_date?->format('Y-m-d')) }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Upload Gambar -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Gambar Produk</h3>
                        
                        @if($product->image)
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-700 mb-2">Gambar Saat Ini:</p>
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-lg border">
                            </div>
                        @endif
                        
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Unggah Gambar Baru</label>
                            <input type="file" name="image" id="image" accept="image/*" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-sm text-gray-500 mt-1">Format didukung: JPG, PNG, GIF. Maks: 2MB. Biarkan kosong untuk mempertahankan gambar sekarang.</p>
                        </div>

                        <div id="image-preview" class="hidden">
                            <p class="text-sm font-medium text-gray-700 mb-2">Preview Gambar Baru:</p>
                            <img id="preview-img" src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg border">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 pt-6 border-t">
                        <a href="{{ route('admin.products.index') }}" 
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                            Perbarui Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Hitung laba otomatis (Rupiah)
    function formatRupiah(num){
        return 'Rp' + num.toLocaleString('id-ID');
    }
    function calculateProfit() {
        const purchasePrice = parseFloat(document.getElementById('purchase_price').value) || 0;
        const sellPrice = parseFloat(document.getElementById('sell_price').value) || 0;
        const profit = sellPrice - purchasePrice;
        document.getElementById('profit-display').textContent = formatRupiah(profit);
    }

    document.getElementById('purchase_price').addEventListener('input', calculateProfit);
    document.getElementById('sell_price').addEventListener('input', calculateProfit);

    // Preview gambar
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('image-preview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            document.getElementById('image-preview').classList.add('hidden');
        }
    });
    </script>
</x-admin-layout>
