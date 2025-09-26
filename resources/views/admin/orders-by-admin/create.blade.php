<x-admin-layout>
    <x-slot name="title">Buat Order Admin</x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Buat Order Admin</h2>
        </div>
        <div class="p-6">
            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            @php $oldStatus = 'PENDING'; @endphp
            <form method="POST" action="{{ route('admin.orders-by-admin.store') }}" class="space-y-6" id="create-order-admin-form">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Seller (User)</label>
                        <select id="seller_id" name="user_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->username }} - {{ optional($u->sellerInfo)->store_name ?? 'Tanpa Toko' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Alamat Pengiriman</label>
                        <textarea name="adress" rows="3" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Alamat lengkap atau catatan pengiriman">{{ old('adress') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Isi alamat tujuan order (min. 5 karakter).</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-2">Item</label>
                        <div id="items-container" class="space-y-4">
                            <div class="order-item border rounded-lg p-4" data-index="0">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Pilih Item Etalase</label>
                                        <div class="flex items-start gap-3">
                                            <img id="thumb_0" src="" alt="Thumb" class="w-12 h-12 rounded object-cover bg-gray-100 border border-gray-200 hidden" />
                                            <div class="flex-1">
                                                <select id="store_showcase_id_0" name="items[0][store_showcase_id]" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                    <option value="">Pilih seller terlebih dahulu</option>
                                                </select>
                                                <input type="hidden" id="product_id_0" name="items[0][product_id]" />
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Qty</label>
                                        <input type="number" id="quantity_0" name="items[0][quantity]" min="1" value="1" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Harga/Unit</label>
                                        <input type="number" id="unit_price_0" class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-gray-700" readonly />
                                        <p class="text-xs text-gray-500 mt-1">Otomatis dari harga jual produk seller.</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Total Harga</label>
                                        <input type="number" id="total_price_0" class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-gray-700" readonly />
                                        <p class="text-xs text-gray-500 mt-1">Otomatis: Qty × Harga/Unit.</p>
                                    </div>
                                </div>
                                <div class="mt-3 text-right">
                                    <button type="button" class="inline-flex items-center px-3 py-1.5 rounded border border-red-200 text-red-700 hover:bg-red-50 text-xs" onclick="removeItem(this)">Hapus Item</button>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="button" id="add-item-btn" class="inline-flex items-center px-3 py-2 rounded-lg text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 border border-gray-300">+ Tambah Item</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                        <input type="text" value="Menunggu Konfirmasi" class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-100 text-gray-700" readonly />
                        <input type="hidden" name="status" value="PENDING" />
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.orders-by-admin.index') }}" class="px-4 py-2 rounded-lg text-sm bg-gray-200 hover:bg-gray-300 text-gray-800">Batal</a>
                    <button class="px-5 py-2 rounded-lg text-sm bg-emerald-600 hover:bg-emerald-700 text-white font-medium">Simpan</button>
                </div>
            </form>

            <template id="order-item-template">
                <div class="order-item border rounded-lg p-4" data-index="__INDEX__">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Pilih Item Etalase</label>
                            <div class="flex items-start gap-3">
                                <img id="thumb___INDEX__" src="" alt="Thumb" class="w-12 h-12 rounded object-cover bg-gray-100 border border-gray-200 hidden" />
                                <div class="flex-1">
                                    <select id="store_showcase_id___INDEX__" name="items[__INDEX__][store_showcase_id]" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Pilih seller terlebih dahulu</option>
                                    </select>
                                    <input type="hidden" id="product_id___INDEX__" name="items[__INDEX__][product_id]" />
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Qty</label>
                            <input type="number" id="quantity___INDEX__" name="items[__INDEX__][quantity]" min="1" value="1" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Harga/Unit</label>
                            <input type="number" id="unit_price___INDEX__" class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-gray-700" readonly />
                            <p class="text-xs text-gray-500 mt-1">Otomatis dari harga jual produk seller.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Total Harga</label>
                            <input type="number" id="total_price___INDEX__" class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-gray-700" readonly />
                            <p class="text-xs text-gray-500 mt-1">Otomatis: Qty × Harga/Unit.</p>
                        </div>
                    </div>
                    <div class="mt-3 text-right">
                        <button type="button" class="inline-flex items-center px-3 py-1.5 rounded border border-red-200 text-red-700 hover:bg-red-50 text-xs" onclick="removeItem(this)">Hapus Item</button>
                    </div>
                </div>
            </template>

            <script>
                function updateRowTotal(row){
                    const idx = row.dataset.index;
                    const qty = parseInt(document.getElementById(`quantity_${idx}`)?.value || '0', 10) || 0;
                    const unit = parseInt(document.getElementById(`unit_price_${idx}`)?.value || '0', 10) || 0;
                    const totalEl = document.getElementById(`total_price_${idx}`);
                    if (totalEl) totalEl.value = qty * unit;
                }

                function setThumb(row, url){
                    const idx = row.dataset.index;
                    const img = document.getElementById(`thumb_${idx}`);
                    if (!img) return;
                    if (url) {
                        img.src = url;
                        img.classList.remove('hidden');
                    } else {
                        img.src = '';
                        img.classList.add('hidden');
                    }
                }

                async function fetchShowcasesForRow(row, sellerId){
                    const idx = row.dataset.index;
                    const showcaseSelect = document.getElementById(`store_showcase_id_${idx}`);
                    const productHidden = document.getElementById(`product_id_${idx}`);
                    const unitPriceInput = document.getElementById(`unit_price_${idx}`);

                    showcaseSelect.innerHTML = '<option value="">Memuat etalase...</option>';

                    try {
                        const res = await fetch(`/admin/api/showcases?seller_id=${sellerId}`);
                        if (!res.ok) throw new Error('Gagal memuat etalase');
                        const showcases = await res.json();

                        showcaseSelect.innerHTML = '';
                        if (!Array.isArray(showcases) || showcases.length === 0) {
                            showcaseSelect.innerHTML = '<option value="">Tidak ada etalase aktif</option>';
                            productHidden.value = '';
                            unitPriceInput.value = 0;
                            setThumb(row, null);
                            updateRowTotal(row);
                            return;
                        }

                        showcases.forEach(s => {
                            const opt = document.createElement('option');
                            opt.value = s.id;
                            opt.textContent = `#${s.id} - ${s.product_name ?? ('Produk#' + s.product_id)}`;
                            opt.dataset.productId = s.product_id || (s.product && s.product.id) || '';
                            opt.dataset.sellPrice = s.sell_price ?? (s.product && s.product.sell_price) ?? '';
                            opt.dataset.promoPrice = s.promo_price ?? (s.product && s.product.promo_price) ?? '';
                            opt.dataset.imageUrl = s.image_url ?? (s.product && s.product.image_url) ?? '';
                            showcaseSelect.appendChild(opt);
                        });

                        const firstOpt = showcaseSelect.options[0];
                        productHidden.value = firstOpt?.dataset.productId || '';
                        const firstPrice = firstOpt?.dataset.sellPrice || firstOpt?.dataset.promoPrice || 0;
                        unitPriceInput.value = firstPrice || 0;
                        setThumb(row, firstOpt?.dataset.imageUrl || null);
                        updateRowTotal(row);
                    } catch (e) {
                        showcaseSelect.innerHTML = '<option value="">Gagal memuat etalase</option>';
                        productHidden.value = '';
                        unitPriceInput.value = 0;
                        setThumb(row, null);
                        updateRowTotal(row);
                    }
                }

                function onShowcaseChangeRow(row){
                    const idx = row.dataset.index;
                    const showcaseSelect = document.getElementById(`store_showcase_id_${idx}`);
                    const sel = showcaseSelect.selectedOptions[0];
                    if (!sel) return;
                    document.getElementById(`product_id_${idx}`).value = sel.dataset.productId || '';
                    const price = sel.dataset.sellPrice || sel.dataset.promoPrice || 0;
                    document.getElementById(`unit_price_${idx}`).value = price || 0;
                    setThumb(row, sel.dataset.imageUrl || null);
                    updateRowTotal(row);
                }

                function bindRowEvents(row){
                    const idx = row.dataset.index;
                    document.getElementById(`store_showcase_id_${idx}`).addEventListener('change', () => onShowcaseChangeRow(row));
                    document.getElementById(`quantity_${idx}`).addEventListener('input', () => updateRowTotal(row));
                }

                function reindexItems(){
                    const rows = Array.from(document.querySelectorAll('#items-container .order-item'));
                    rows.forEach((row, i) => {
                        const oldIdx = row.dataset.index;
                        row.dataset.index = i;
                        const map = [
                            ['store_showcase_id', 'select'],
                            ['product_id', 'input'],
                            ['quantity', 'input'],
                            ['unit_price', 'input'],
                            ['total_price', 'input'],
                            ['thumb', 'img'],
                        ];
                        map.forEach(([base, tag]) => {
                            const el = row.querySelector(`${tag}[id^='${base}_']`);
                            if (el) {
                                const newId = `${base}_${i}`;
                                el.id = newId;
                                if (base === 'store_showcase_id') el.name = `items[${i}][store_showcase_id]`;
                                if (base === 'product_id') el.name = `items[${i}][product_id]`;
                                if (base === 'quantity') el.name = `items[${i}][quantity]`;
                            }
                        });
                        bindRowEvents(row);
                        updateRowTotal(row);
                    });
                }

                function addItem(){
                    const tpl = document.getElementById('order-item-template').innerHTML;
                    const container = document.getElementById('items-container');
                    const nextIndex = container.querySelectorAll('.order-item').length;
                    const html = tpl.replaceAll('__INDEX__', nextIndex);
                    const temp = document.createElement('div');
                    temp.innerHTML = html.trim();
                    const row = temp.firstElementChild;
                    container.appendChild(row);
                    bindRowEvents(row);
                    const sellerId = document.getElementById('seller_id').value;
                    if (sellerId) fetchShowcasesForRow(row, sellerId);
                }

                window.removeItem = function(btn){
                    const container = document.getElementById('items-container');
                    const rows = container.querySelectorAll('.order-item');
                    if (rows.length <= 1) {
                        const row = rows[0];
                        const idx = row.dataset.index;
                        document.getElementById(`store_showcase_id_${idx}`).innerHTML = '<option value="">Pilih seller terlebih dahulu</option>';
                        document.getElementById(`product_id_${idx}`).value = '';
                        document.getElementById(`quantity_${idx}`).value = 1;
                        document.getElementById(`unit_price_${idx}`).value = 0;
                        document.getElementById(`total_price_${idx}`).value = 0;
                        setThumb(row, null);
                        return;
                    }
                    btn.closest('.order-item').remove();
                    reindexItems();
                }

                document.addEventListener('DOMContentLoaded', () => {
                    const sellerSelect = document.getElementById('seller_id');
                    const addBtn = document.getElementById('add-item-btn');
                    const firstRow = document.querySelector('#items-container .order-item');

                    bindRowEvents(firstRow);

                    if (sellerSelect.value) {
                        fetchShowcasesForRow(firstRow, sellerSelect.value);
                    }

                    sellerSelect.addEventListener('change', (e) => {
                        const sellerId = e.target.value;
                        document.querySelectorAll('#items-container .order-item').forEach(row => fetchShowcasesForRow(row, sellerId));
                    });

                    addBtn.addEventListener('click', addItem);
                });
            </script>
        </div>
    </div>
</x-admin-layout>
