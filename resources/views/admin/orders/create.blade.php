@php /** @var \Illuminate\Support\Collection<int, \App\Models\User> $users */ @endphp
@php /** @var \Illuminate\Support\Collection<int, \App\Models\User> $sellers */ @endphp
@php /** @var \Illuminate\Support\Collection<int, \App\Models\Product> $products */ @endphp
<x-admin-layout>
    <x-slot name="title">Buat Order Baru</x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Form Order</h2>
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                Kembali
            </a>
        </div>

        <!-- Flash / Errors -->
        @if($errors->any())
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.orders.store') }}" class="p-6 space-y-8" id="admin-create-order-form">
            @csrf

            <!-- Order Info -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Order</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Pembeli</label>
                        <select name="buyer_id" id="buyer_id" class="w-full rounded-md border border-gray-300 text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Pilih pembeli...</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ old('buyer_id') == $u->id ? 'selected' : '' }}>{{ $u->full_name }} (ID {{ $u->id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tipe Pembelian</label>
                        <select name="purchase_type" id="purchase_type" class="w-full rounded-md border border-gray-300 text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="self" {{ old('purchase_type','self')==='self' ? 'selected' : '' }}>Pribadi</option>
                            <option value="external" {{ old('purchase_type')==='external' ? 'selected' : '' }}>Eksternal (untuk pelanggan)</option>
                        </select>
                    </div>

                    <div class="md:col-span-2 flex flex-col gap-2">
                        <div class="flex items-center gap-6">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="from_etalase" id="from_etalase" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ old('from_etalase') ? 'checked' : '' }}>
                                <span class="text-sm text-gray-800">Order dari Etalase Seller</span>
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="auto_paid" id="auto_paid" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ old('auto_paid') ? 'checked' : '' }}>
                                <span class="text-sm text-gray-800">Auto Paid (langsung tandai lunas)</span>
                            </label>
                        </div>
                        <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 px-3 py-2 rounded-md w-fit" id="auto_paid_hint" style="display: none;">Auto Paid akan melewati pengecekan saldo dan langsung menandai order sebagai Lunas. Pastikan data sudah benar.</p>
                    </div>

                    <div id="seller_wrapper" class="hidden">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Pilih Seller Etalase</label>
                        <select name="seller_id" id="seller_id" class="w-full rounded-md border border-gray-300 text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih seller...</option>
                            @foreach($sellers as $s)
                                <option value="{{ $s->id }}" {{ old('seller_id') == $s->id ? 'selected' : '' }}>{{ $s->full_name }} (ID {{ $s->id }})</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Wajib diisi jika order dari etalase.</p>
                    </div>
                </div>
            </div>

            <!-- Customer (External) & Address -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pelanggan & Alamat</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="external-only">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Pelanggan (opsional)</label>
                        <input type="text" name="external_customer_name" value="{{ old('external_customer_name') }}" class="w-full rounded-md border border-gray-300 text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nama pelanggan">
                    </div>
                    <div class="external-only">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Telepon Pelanggan (opsional)</label>
                        <input type="text" name="external_customer_phone" value="{{ old('external_customer_phone') }}" class="w-full rounded-md border border-gray-300 text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Alamat Pengiriman</label>
                        <textarea name="address" rows="3" class="w-full rounded-md border border-gray-300 text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Alamat lengkap" required>{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Item</h3>
                <div class="bg-gray-50 rounded-lg overflow-hidden border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga (perkiraan)</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody id="items_tbody" class="bg-white divide-y divide-gray-200">
                            <!-- Rows injected by JS -->
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 flex items-center justify-between gap-4">
                    <button type="button" id="btn_add_item" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Tambah Item
                    </button>
                    <div class="ml-auto p-3 rounded-md bg-gray-50 border border-gray-200 text-sm">
                        <div class="flex items-center gap-6">
                            <div class="font-medium text-gray-700">Perkiraan Grand Total:</div>
                            <div id="estimated_grand_total" class="font-semibold text-gray-900">Rp 0</div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Nilai ini hanya estimasi. Perhitungan final mengikuti logika backend (margin, etalase, diskon, dll).</p>
                    </div>
                </div>
            </div>

            <!-- Notes & Submit -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Catatan (opsional)</label>
                    <textarea name="user_notes" rows="4" class="w-full rounded-md border border-gray-300 text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Catatan untuk order">{{ old('user_notes') }}</textarea>
                </div>
                <div class="flex items-end justify-end">
                    <button type="submit" id="btn_submit" class="inline-flex items-center bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition-colors">
                        <svg id="submit_spinner" class="w-4 h-4 mr-2 animate-spin hidden" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                        Buat Order
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Data produk untuk JS
        window.PRODUCTS_JS = @json($products->map(function($p){
            return [
                'id' => $p->id,
                'name' => $p->name,
                'harga_biasa' => $p->harga_biasa,
                'harga_jual' => $p->harga_jual,
                'sell_price' => $p->sell_price,
            ];
        }));
        window.OLD_ITEMS = @json(old('items', []));

        (function(){
            const tbody = document.getElementById('items_tbody');
            const btnAdd = document.getElementById('btn_add_item');
            const form = document.getElementById('admin-create-order-form');
            const fromEtalase = document.getElementById('from_etalase');
            const sellerWrapper = document.getElementById('seller_wrapper');
            const sellerSelect = document.getElementById('seller_id');
            const purchaseType = document.getElementById('purchase_type');
            const estimatedGrandEl = document.getElementById('estimated_grand_total');
            const autoPaid = document.getElementById('auto_paid');
            const autoPaidHint = document.getElementById('auto_paid_hint');
            const submitBtn = document.getElementById('btn_submit');
            const submitSpinner = document.getElementById('submit_spinner');

            let rowIndex = 0;

            function productOptionsHtml(selectedId){
                return PRODUCTS_JS.map(p => `<option value="${p.id}" ${selectedId && Number(selectedId)===Number(p.id) ? 'selected' : ''} data-harga-biasa="${p.harga_biasa}" data-harga-jual="${p.harga_jual}">${p.name} (ID ${p.id})</option>`).join('');
            }

            function estimateUnitPriceFor(productSelect){
                const opt = productSelect.selectedOptions[0];
                if(!opt){ return 0; }
                const hb = Number(opt.getAttribute('data-harga-biasa')) || 0;
                const hj = Number(opt.getAttribute('data-harga-jual')) || 0;
                // Etalase always uses harga jual
                if(fromEtalase.checked){ return hj; }
                // Otherwise show harga biasa as default preview
                if(purchaseType.value === 'external'){ return hj; }
                return hb;
            }

            function formatRupiah(n){
                return new Intl.NumberFormat('id-ID').format(n || 0);
            }

            function recalcTotals(){
                let total = 0;
                [...tbody.querySelectorAll('tr')].forEach(tr => {
                    const select = tr.querySelector('select[name$="[product_id]"]');
                    const qtyInput = tr.querySelector('input[name$="[quantity]"]');
                    if(!select || !qtyInput) return;
                    const unit = estimateUnitPriceFor(select);
                    const qty = Math.max(1, parseInt(qtyInput.value || '1', 10));
                    total += unit * qty;
                });
                estimatedGrandEl.textContent = `Rp ${formatRupiah(total)}`;
            }

            function onRowChange(row){
                const select = row.querySelector('select[name$="[product_id]"]');
                const qtyInput = row.querySelector('input[name$="[quantity]"]');
                const priceEl = row.querySelector('.js-price');
                const unit = estimateUnitPriceFor(select);
                const qty = Math.max(1, parseInt(qtyInput.value || '1', 10));
                priceEl.textContent = `Rp ${formatRupiah(unit)} x ${qty} = Rp ${formatRupiah(unit * qty)}`;
                recalcTotals();
            }

            function createRow(idx, preset){
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50';
                const presetProductId = preset?.product_id ?? '';
                const presetQty = preset?.quantity ? Number(preset.quantity) : 1;
                tr.innerHTML = `
                    <td class="px-6 py-4">
                        <select name="items[${idx}][product_id]" class="w-full rounded-md border border-gray-300 text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Pilih produk...</option>
                            ${productOptionsHtml(presetProductId)}
                        </select>
                    </td>
                    <td class="px-6 py-4">
                        <input type="number" min="1" name="items[${idx}][quantity]" value="${presetQty}" class="w-24 rounded-md border border-gray-300 text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required />
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-700 js-price">-</div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button type="button" class="inline-flex items-center text-red-600 hover:text-red-800 text-sm font-medium js-remove-row" aria-label="Hapus baris">
                            Hapus
                        </button>
                    </td>
                `;

                // Listeners
                tr.querySelector('select').addEventListener('change', () => onRowChange(tr));
                tr.querySelector('input').addEventListener('input', () => onRowChange(tr));
                tr.querySelector('.js-remove-row').addEventListener('click', () => {
                    tr.remove();
                    ensureAtLeastOneRow();
                    recalcTotals();
                });

                tbody.appendChild(tr);
                onRowChange(tr);
            }

            function ensureAtLeastOneRow(){
                if(tbody.children.length === 0){
                    createRow(rowIndex++);
                }
            }

            function setSellerRequired(){
                if(!sellerSelect) return;
                if(fromEtalase.checked){
                    sellerSelect.setAttribute('required', 'required');
                } else {
                    sellerSelect.removeAttribute('required');
                }
            }

            // Initialize
            btnAdd.addEventListener('click', () => createRow(rowIndex++));
            fromEtalase.addEventListener('change', () => {
                if(fromEtalase.checked){ sellerWrapper.classList.remove('hidden'); }
                else { sellerWrapper.classList.add('hidden'); }
                setSellerRequired();
                // refresh price previews
                [...tbody.children].forEach(tr => onRowChange(tr));
                recalcTotals();
            });
            purchaseType.addEventListener('change', () => {
                toggleExternalFields();
                [...tbody.children].forEach(tr => onRowChange(tr));
                recalcTotals();
            });
            autoPaid.addEventListener('change', () => {
                if(autoPaid.checked){ autoPaidHint.style.display = 'block'; }
                else { autoPaidHint.style.display = 'none'; }
            });

            function toggleExternalFields(){
                const isExternal = purchaseType.value === 'external';
                document.querySelectorAll('.external-only').forEach(el => {
                    if(isExternal){ el.classList.remove('hidden'); }
                    else { el.classList.add('hidden'); }
                });
            }

            form.addEventListener('submit', (e) => {
                // Basic client validation
                if(tbody.children.length === 0){
                    e.preventDefault();
                    alert('Minimal 1 item.');
                    return;
                }
                if(fromEtalase.checked){
                    const seller = document.getElementById('seller_id');
                    if(!seller.value){
                        e.preventDefault();
                        seller.classList.add('ring-2','ring-red-500');
                        alert('Pilih seller etalase.');
                        return;
                    }
                }
                if(autoPaid.checked){
                    const ok = confirm('Anda mengaktifkan Auto Paid. Order akan langsung ditandai Lunas dan stok akan berkurang. Lanjutkan?');
                    if(!ok){
                        e.preventDefault();
                        return;
                    }
                }
                // Disable submit to prevent double submit
                submitBtn.setAttribute('disabled','disabled');
                submitBtn.classList.add('opacity-70','cursor-not-allowed');
                submitSpinner.classList.remove('hidden');
            });

            // On load
            toggleExternalFields();
            if({{ old('from_etalase') ? 'true' : 'false' }}){ sellerWrapper.classList.remove('hidden'); }
            setSellerRequired();

            // Restore old items if any
            if(Array.isArray(OLD_ITEMS) && OLD_ITEMS.length > 0){
                OLD_ITEMS.forEach(it => {
                    createRow(rowIndex++, { product_id: it.product_id, quantity: it.quantity });
                });
            } else {
                ensureAtLeastOneRow();
            }

            // Initial states
            recalcTotals();
            if(autoPaid.checked){ autoPaidHint.style.display = 'block'; }
        })();
    </script>
</x-admin-layout>
