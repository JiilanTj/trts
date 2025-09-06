<x-app-layout>
    <div class="min-h-screen bg-[#0f1115] text-gray-200 relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_25%_15%,rgba(236,72,153,0.07),transparent_60%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_85%,rgba(59,130,246,0.08),transparent_65%)]"></div>
        <div class="sticky top-0 z-40 backdrop-blur-md bg-[#0f1115]/70 border-b border-white/10">
            <div class="px-4 py-3 flex items-center gap-3">
                <a href="{{ url()->previous() }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:bg-white/5 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60" aria-label="Kembali">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-semibold text-white leading-tight">Buat Order</h1>
                    <p class="text-[11px] text-gray-500 mt-0.5">Lengkapi keranjang & upload bukti bayar nanti.</p>
                </div>
                <a href="{{ route('user.orders.index') }}" class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-gradient-to-r from-fuchsia-500 via-rose-500 to-cyan-500 text-white hover:from-fuchsia-500/90 hover:via-rose-500/90 hover:to-cyan-500/90 shadow-sm shadow-fuchsia-500/30 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h2l2-3h10l2 3h2v13H3z" /></svg>
                    Order Saya
                </a>
            </div>
        </div>

        <div class="px-4 py-6 max-w-5xl mx-auto space-y-8">
            @if($errors->any())
                <div class="bg-red-600/10 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('user.orders.store') }}" id="order-form" class="space-y-8">
                @csrf
                <input type="hidden" name="purchase_type" id="purchase_type_input" value="{{ $purchaseType }}">
                <input type="hidden" id="single_mode" value="{{ isset($singleMode)&&$singleMode?1:0 }}">
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="md:col-span-2 space-y-8">
                        <div class="bg-[#181d23] border border-white/10 rounded-2xl p-6 space-y-6" id="product-select-block">
                            <div class="flex items-center justify-between">
                                <h2 class="text-sm font-semibold text-white">Pilih Produk</h2>
                                @if(auth()->user()->isSeller())
                                    <div class="flex items-center gap-4 text-[11px] font-medium" id="purchase-type-toggle">
                                        <label class="flex items-center gap-1 cursor-pointer">
                                            <input type="radio" name="purchase_type_switch" value="self" class="purchase-type-switch text-fuchsia-500 focus:ring-fuchsia-500/60 bg-[#1b1f25] border-white/10" @checked($purchaseType==='self')>
                                            <span>Untuk Diri</span>
                                        </label>
                                        <label class="flex items-center gap-1 cursor-pointer">
                                            <input type="radio" name="purchase_type_switch" value="external" class="purchase-type-switch text-cyan-500 focus:ring-cyan-500/60 bg-[#1b1f25] border-white/10" @checked($purchaseType==='external')>
                                            <span>Untuk Pelanggan</span>
                                        </label>
                                    </div>
                                @endif
                            </div>
                            <div class="grid sm:grid-cols-2 gap-4 {{ (isset($singleMode)&&$singleMode) || (isset($wholesaleMode)&&$wholesaleMode) ? 'hidden' : '' }}" id="product-list">
                                @foreach($products as $p)
                                    <button type="button"
                                        data-product-id="{{ $p->id }}"
                                        data-product-name="{{ e($p->name) }}"
                                        data-product-stock="{{ $p->stock }}"
                                        data-product-harga-biasa="{{ $p->harga_biasa }}"
                                        data-product-harga-jual="{{ $p->harga_jual }}"
                                        class="group relative rounded-xl p-4 bg-[#1f252c] border {{ $prefill && $prefill->id===$p->id ? 'border-fuchsia-500/60' : 'border-white/5' }} hover:border-fuchsia-500/60 transition flex flex-col gap-3 text-left focus:outline-none focus:ring-2 focus:ring-fuchsia-500/50">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[11px] text-gray-500">#{{ $p->id }}</p>
                                                <h3 class="text-xs font-medium text-gray-200 group-hover:text-white leading-snug line-clamp-2">{{ $p->name }}</h3>
                                            </div>
                                            <span class="px-2 py-1 text-[10px] rounded-full font-medium {{ $p->stock>0 ? 'bg-emerald-600/20 text-emerald-400 border border-emerald-600/30' : 'bg-red-600/20 text-red-400 border border-red-600/30' }}">{{ $p->stock>0 ? 'Stok '.$p->stock : 'Habis' }}</span>
                                        </div>
                                        <div class="text-[11px] font-semibold bg-clip-text text-transparent bg-gradient-to-r from-fuchsia-400 via-rose-400 to-cyan-400">Rp {{ number_format($p->harga_biasa,0,',','.') }}</div>
                                    </button>
                                @endforeach
                            </div>
                            
                            @if(isset($wholesaleMode) && $wholesaleMode && !empty($prefilledProducts))
                                <div id="wholesale-products-summary" class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-semibold text-white">Produk dari Wholesale ({{ count($prefilledProducts) }} produk)</h3>
                                        <a href="{{ route('user.wholesale.index') }}" class="text-xs text-fuchsia-400 hover:text-fuchsia-300">Ubah Pilihan</a>
                                    </div>
                                    @foreach($prefilledProducts as $index => $item)
                                        <div class="flex items-start justify-between gap-4 rounded-xl border border-fuchsia-500/30 bg-[#1f1115] p-4">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[11px] text-gray-500 mb-0.5">#{{ $item['product']->id }}</p>
                                                <h3 class="text-xs font-medium text-gray-200 leading-snug">{{ $item['product']->name }}</h3>
                                                <p class="text-[10px] text-gray-500 mt-1">Stok: {{ $item['product']->stock }}</p>
                                            </div>
                                            <div class="text-right space-y-2">
                                                <p class="text-[11px] font-semibold bg-clip-text text-transparent bg-gradient-to-r from-fuchsia-400 via-rose-400 to-cyan-400">Rp {{ number_format($item['product']->harga_biasa,0,',','.') }}</p>
                                                <div class="flex items-center gap-2 justify-end">
                                                    <button type="button" data-action="decrease" data-index="{{ $index }}" class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-[#242b33] text-gray-400 hover:text-white hover:bg-fuchsia-600/20 border border-white/10">-</button>
                                                    <input type="number" min="1" value="{{ $item['quantity'] }}" data-wholesale-qty="{{ $index }}" class="w-14 text-xs bg-[#242b33] border border-white/10 rounded-lg px-2 py-1 text-center focus:outline-none focus:ring-2 focus:ring-fuchsia-500/50 text-gray-200">
                                                    <button type="button" data-action="increase" data-index="{{ $index }}" class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-[#242b33] text-gray-400 hover:text-white hover:bg-fuchsia-600/20 border border-white/10">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <p class="text-[10px] text-gray-500">Produk dari wholesale. Ubah jumlah sesuai kebutuhan lalu buat order.</p>
                                </div>
                            @endif
                            @if(isset($singleMode)&&$singleMode && $prefill)
                                <div id="single-product-summary" class="space-y-4">
                                    <div class="flex items-start justify-between gap-4 rounded-xl border border-fuchsia-500/50 bg-[#1f1115] p-4">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[11px] text-gray-500 mb-0.5">#{{ $prefill->id }}</p>
                                            <h3 class="text-xs font-medium text-gray-200 leading-snug">{{ $prefill->name }}</h3>
                                            <p class="text-[10px] text-gray-500 mt-1">Stok: {{ $prefill->stock }}</p>
                                        </div>
                                        <div class="text-right space-y-2">
                                            <p class="text-[11px] font-semibold bg-clip-text text-transparent bg-gradient-to-r from-fuchsia-400 via-rose-400 to-cyan-400">Rp {{ number_format($prefill->harga_biasa,0,',','.') }}</p>
                                            <div class="flex items-center gap-2 justify-end">
                                                <button type="button" id="qty-minus" class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-[#242b33] text-gray-400 hover:text-white hover:bg-fuchsia-600/20 border border-white/10">-</button>
                                                <input type="number" min="1" value="1" id="single-qty" class="w-14 text-xs bg-[#242b33] border border-white/10 rounded-lg px-2 py-1 text-center focus:outline-none focus:ring-2 focus:ring-fuchsia-500/50 text-gray-200">
                                                <button type="button" id="qty-plus" class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-[#242b33] text-gray-400 hover:text-white hover:bg-fuchsia-600/20 border border-white/10">+</button>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-gray-500">Mode checkout cepat untuk 1 produk. Ubah jumlah lalu buat order.</p>
                                </div>
                            @endif
                        </div>

                        <div class="bg-[#181d23] border border-white/10 rounded-2xl p-6 space-y-6">
                            <h2 class="text-sm font-semibold text-white">Item Order</h2>
                            <div id="order-items" class="space-y-4"></div>
                            <template id="item-row-template">
                                <div class="item-row flex items-center gap-4 bg-[#1f252c] border border-white/5 rounded-xl p-4 relative">
                                    <input type="hidden" name="items[__INDEX__][product_id]" data-field="product_id">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-200" data-field="name"></p>
                                        <p class="text-[10px] text-gray-500">Harga: <span data-field="price_label"></span></p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="number" min="1" value="1" name="items[__INDEX__][quantity]" class="w-16 text-xs bg-[#242b33] border border-white/10 rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/50 text-gray-200" data-field="quantity">
                                        <button type="button" class="remove-item w-8 h-8 inline-flex items-center justify-center rounded-lg bg-red-600/20 text-red-400 hover:bg-red-600/30 border border-red-600/30 focus:outline-none focus:ring-2 focus:ring-red-500/40" aria-label="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <p class="text-[11px] text-gray-500" id="empty-items">Belum ada produk dipilih.</p>
                        </div>

                        <div class="bg-[#181d23] border border-white/10 rounded-2xl p-6 space-y-6">
                            <h2 class="text-sm font-semibold text-white">Informasi Pemesan / Pelanggan</h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-400 mb-1">Nama</label>
                                    <input type="text" name="external_customer_name" id="customer_name_field" value="{{ old('external_customer_name', $purchaseType==='self' ? ($selfPrefill['name'] ?? '') : '') }}" class="w-full text-sm bg-[#1b1f25] border border-white/10 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 text-gray-200 placeholder-gray-500" placeholder="Nama Pelanggan">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-400 mb-1">No. Telepon</label>
                                    <input type="text" name="external_customer_phone" id="customer_phone_field" value="{{ old('external_customer_phone', $purchaseType==='self' ? ($selfPrefill['phone'] ?? '') : '') }}" class="w-full text-sm bg-[#1b1f25] border border-white/10 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 text-gray-200 placeholder-gray-500" placeholder="08xxxxxxxxxx">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-400 mb-1">Alamat <span class="text-red-500">*</span></label>
                                    <input type="text" name="address" id="address_field" required value="{{ old('address', $purchaseType==='self' ? ($selfPrefill['address'] ?? '') : '') }}" class="w-full text-sm bg-[#1b1f25] border border-white/10 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 text-gray-200 placeholder-gray-500" placeholder="Alamat pengiriman / catatan lokasi">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-400 mb-1">Catatan (opsional)</label>
                                    <textarea name="user_notes" rows="3" class="w-full text-sm bg-[#1b1f25] border border-white/10 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/50 text-gray-200 placeholder-gray-500" placeholder="Catatan tambahan...">{{ old('user_notes') }}</textarea>
                                </div>
                                <div class="text-[10px] text-gray-500 leading-relaxed">
                                    Jika mode "Untuk Diri" aktif, kolom diisi otomatis dari profil Anda namun tetap bisa diedit / dikosongkan sebelum submit.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-8">
                        <div class="bg-[#181d23] border border-white/10 rounded-2xl p-6 space-y-5 sticky top-24">
                            <h2 class="text-sm font-semibold text-white">Ringkasan</h2>
                            <div class="space-y-3 text-[13px] font-medium">
                                <div class="flex items-center justify-between"><span class="text-gray-400">Subtotal</span><span id="subtotal" class="text-gray-200">Rp 0</span></div>
                                <div class="flex items-center justify-between"><span class="text-gray-400">Diskon</span><span id="discount_total" class="text-gray-200">Rp 0</span></div>
                                <div class="flex items-center justify-between"><span class="text-gray-400">Grand Total</span><span id="grand_total" class="text-fuchsia-300">Rp 0</span></div>
                                @if(auth()->user()->isSeller())
                                <div class="flex items-center justify-between"><span class="text-gray-400">Margin Seller</span><span id="margin_total" class="text-emerald-300">Rp 0</span></div>
                                @endif
                            </div>
                            <button type="submit" class="w-full py-3 rounded-xl text-sm font-medium text-white bg-gradient-to-r from-fuchsia-500 via-rose-500 to-cyan-500 hover:from-fuchsia-500/90 hover:via-rose-500/90 hover:to-cyan-500/90 disabled:from-gray-600 disabled:via-gray-500 disabled:to-gray-400 disabled:cursor-not-allowed shadow-sm shadow-fuchsia-500/30 flex items-center justify-center gap-2 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m5-9v9m4-9v9m4-9l2 9" /></svg>
                                Buat Order
                            </button>
                            <p class="text-[10px] text-gray-500 leading-relaxed">Setelah order dibuat, upload bukti transfer di halaman detail order untuk verifikasi admin.</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function(){
            const productsContainer = document.getElementById('product-list');
            const itemsWrapper = document.getElementById('order-items');
            const template = document.getElementById('item-row-template').innerHTML.trim();
            const emptyIndicator = document.getElementById('empty-items');
            const subtotalEl = document.getElementById('subtotal');
            const discountEl = document.getElementById('discount_total');
            const grandTotalEl = document.getElementById('grand_total');
            const marginTotalEl = document.getElementById('margin_total');
            const purchaseTypeInput = document.getElementById('purchase_type_input');
            const switches = document.querySelectorAll('.purchase-type-switch');
            const prefillId = {{ $prefill?->id ?? 'null' }};
            const singleMode = document.getElementById('single_mode').value === '1';
            const wholesaleMode = {{ isset($wholesaleMode) && $wholesaleMode ? 'true' : 'false' }};
            const wholesaleProducts = @json($prefilledProducts ?? []);
            const singleQtyInput = document.getElementById('single-qty');
            const qtyMinus = document.getElementById('qty-minus');
            const qtyPlus = document.getElementById('qty-plus');
            const nameField = document.getElementById('customer_name_field');
            const phoneField = document.getElementById('customer_phone_field');
            const addressField = document.getElementById('address_field');
            const selfData = @json($selfPrefill);

            const rupiah = n => 'Rp ' + (n||0).toLocaleString('id-ID');

            switches.forEach(sw=>{
                sw.addEventListener('change',()=>{
                    purchaseTypeInput.value = document.querySelector('.purchase-type-switch:checked').value;
                    if(purchaseTypeInput.value==='self'){
                        if(!nameField.value.trim()){ nameField.value = selfData.name || ''; }
                        if(!phoneField.value.trim()){ phoneField.value = selfData.phone || ''; }
                        if(!addressField.value.trim()){ addressField.value = selfData.address || ''; }
                    }
                    updatePriceLabels();
                    recalc();
                });
            });

            function buildRow(prod){
                let html = template.replace(/__INDEX__/g, Date.now()+Math.random());
                const wrapper = document.createElement('div');
                wrapper.innerHTML = html;
                const row = wrapper.firstElementChild;
                row.querySelector('[data-field="product_id"]').value = prod.id;
                row.querySelector('[data-field="name"]').textContent = prod.name;
                row.querySelector('[data-field="quantity"]').addEventListener('input',recalc);
                row.querySelector('.remove-item').addEventListener('click',()=>{ row.remove(); recalc(); });
                row.dataset.product = JSON.stringify(prod);
                itemsWrapper.appendChild(row);
                updatePriceLabel(row);
                recalc();
            }

            function getRows(){ return Array.from(itemsWrapper.querySelectorAll('.item-row')); }
            function extract(row){ return JSON.parse(row.dataset.product); }
            function unitPrice(prod){ return purchaseTypeInput.value==='external' ? prod.harga_jual : prod.harga_biasa; }
            function sellerMargin(prod){ const m = prod.harga_jual - prod.harga_biasa; return purchaseTypeInput.value==='external' && m>0 ? m : 0; }

            function updatePriceLabel(row){
                const prod = extract(row); const price = unitPrice(prod);
                row.querySelector('[data-field="price_label"]').textContent = rupiah(price);
            }
            function updatePriceLabels(){ getRows().forEach(updatePriceLabel); }

            function recalc(){
                const rows = getRows();
                emptyIndicator.classList.toggle('hidden', rows.length>0);
                let subtotal=0, discount=0, grand=0, margin=0;
                rows.forEach(r=>{
                    const prod = extract(r);
                    const qty = parseInt(r.querySelector('[data-field="quantity"]').value,10)||1;
                    const price = unitPrice(prod);
                    const line = price * qty;
                    subtotal += price * qty;
                    grand += line;
                    margin += sellerMargin(prod) * qty;
                });
                subtotalEl.textContent = rupiah(subtotal);
                discountEl.textContent = rupiah(discount);
                grandTotalEl.textContent = rupiah(grand);
                if(marginTotalEl) marginTotalEl.textContent = rupiah(margin);
            }

            productsContainer.addEventListener('click',e=>{
                const btn = e.target.closest('button[data-product-id]');
                if(!btn) return;
                const prod = {
                    id: parseInt(btn.dataset.productId,10),
                    name: btn.dataset.productName,
                    stock: parseInt(btn.dataset.productStock,10),
                    harga_biasa: parseInt(btn.dataset.productHargaBiasa,10),
                    harga_jual: parseInt(btn.dataset.productHargaJual,10)
                };
                if(prod.stock<=0) return;
                if(getRows().some(r=>extract(r).id===prod.id)) return;
                buildRow(prod);
            });

            if(prefillId){
                const preBtn = productsContainer.querySelector(`button[data-product-id='${prefillId}']`);
                if(preBtn){ preBtn.click(); }
            }

            function initSingleMode(){
                if(singleMode && prefillId){
                    const preBtn = document.querySelector(`button[data-product-id='${prefillId}']`);
                    if(preBtn){ preBtn.click(); }
                    if(singleQtyInput){
                        singleQtyInput.addEventListener('input',()=>{
                            const row = itemsWrapper.querySelector('.item-row');
                            if(row){ row.querySelector('[data-field="quantity"]').value = Math.max(1, parseInt(singleQtyInput.value,10)||1); recalc(); }
                        });
                        qtyMinus&&qtyMinus.addEventListener('click',()=>{ singleQtyInput.value = Math.max(1,(parseInt(singleQtyInput.value,10)||1)-1); singleQtyInput.dispatchEvent(new Event('input')); });
                        qtyPlus&&qtyPlus.addEventListener('click',()=>{ singleQtyInput.value = (parseInt(singleQtyInput.value,10)||1)+1; singleQtyInput.dispatchEvent(new Event('input')); });
                    }
                }
            }
            
            function initWholesaleMode(){
                if(wholesaleMode && wholesaleProducts.length > 0){
                    console.log('Initializing wholesale mode with products:', wholesaleProducts);
                    
                    // Add all wholesale products to the order
                    wholesaleProducts.forEach(item => {
                        const prod = {
                            id: item.product.id,
                            name: item.product.name,
                            stock: item.product.stock,
                            harga_biasa: item.product.harga_biasa || item.product.sell_price,
                            harga_jual: item.product.harga_jual || item.product.sell_price
                        };
                        
                        // Build the row
                        buildRow(prod);
                        
                        // Set the quantity from wholesale selection
                        const row = itemsWrapper.querySelector(`.item-row:last-child`);
                        if(row) {
                            const qtyInput = row.querySelector('[data-field="quantity"]');
                            if(qtyInput) {
                                qtyInput.value = item.quantity || 1;
                                recalc();
                            }
                        }
                    });
                    
                    // Add event listeners for wholesale quantity controls
                    document.querySelectorAll('[data-wholesale-qty]').forEach((input, index) => {
                        input.addEventListener('input', () => {
                            const row = itemsWrapper.querySelectorAll('.item-row')[index];
                            if(row) {
                                const qtyInput = row.querySelector('[data-field="quantity"]');
                                if(qtyInput) {
                                    qtyInput.value = Math.max(1, parseInt(input.value, 10) || 1);
                                    recalc();
                                }
                            }
                        });
                    });
                    
                    // Add event listeners for wholesale quantity buttons
                    document.querySelectorAll('[data-action]').forEach(btn => {
                        btn.addEventListener('click', () => {
                            const action = btn.dataset.action;
                            const index = parseInt(btn.dataset.index, 10);
                            const input = document.querySelector(`[data-wholesale-qty="${index}"]`);
                            const row = itemsWrapper.querySelectorAll('.item-row')[index];
                            
                            if(input && row) {
                                let newValue = parseInt(input.value, 10) || 1;
                                if(action === 'increase') {
                                    newValue += 1;
                                } else if(action === 'decrease') {
                                    newValue = Math.max(1, newValue - 1);
                                }
                                
                                input.value = newValue;
                                const qtyInput = row.querySelector('[data-field="quantity"]');
                                if(qtyInput) {
                                    qtyInput.value = newValue;
                                    recalc();
                                }
                            }
                        });
                    });
                }
            }

            initSingleMode();
            initWholesaleMode();
        })();
    </script>
</x-app-layout>
