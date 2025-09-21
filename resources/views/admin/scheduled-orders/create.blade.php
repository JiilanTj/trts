<x-admin-layout>
  <x-slot name="title">Buat Scheduled Order</x-slot>

  <style>
    /* Prevent flash/flicker before Alpine initializes */
    [x-cloak] { display: none !important; }
  </style>

  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" x-data="createBatch()" x-init="init()">
    <form @submit.prevent="submit" class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Buyer selector -->
        <div class="md:col-span-2">
          <label class="block text-xs font-medium text-gray-600">Buyer</label>
          <div class="relative mt-1" @keydown.escape.window="showBuyer=false">
            <div class="flex gap-2">
              <div class="flex-1">
                <input type="text" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Cari buyer (nama, username, ID)"
                       x-model="buyerQuery"
                       autocomplete="off"
                       @focus="openBuyer()"
                       @input.debounce.300ms="searchBuyers()">
              </div>
              <button type="button" class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-100" @click="clearBuyer()">Clear</button>
            </div>
            <template x-if="buyerSelected">
              <p class="text-xs text-gray-600 mt-1">Dipilih: <span class="font-medium" x-text="formatBuyer(buyerSelected)"></span> (ID: <span x-text="buyerSelected.id"></span>)</p>
            </template>
            <input type="hidden" :value="form.buyer_id">

            <!-- DROPDOWN: changed to use x-cloak + x-transition and x-show for inner states to avoid DOM teardown flicker -->
            <div class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-md max-h-64 overflow-auto" x-show="showBuyer" x-cloak x-transition.opacity @click.outside="showBuyer=false">
              <div class="p-3 text-sm text-gray-500" x-show="buyerLoading">Memuat...</div>
              <template x-for="b in buyerOptions" :key="b.id">
                <button type="button" class="w-full text-left px-3 py-2 hover:bg-gray-50 text-sm" @mousedown.prevent.stop="selectBuyer(b)">
                  <span class="font-medium" x-text="formatBuyer(b)"></span>
                  <span class="text-gray-500"> — ID: <span x-text="b.id"></span></span>
                </button>
              </template>
              <div class="p-3 text-sm text-gray-500" x-show="!buyerLoading && buyerOptions.length === 0">Tidak ada hasil</div>
            </div>
          </div>
        </div>

        <div>
          <label class="block text-xs font-medium text-gray-600">Purchase Type</label>
          <select class="mt-1 w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" x-model="form.purchase_type">
            <option value="self">Self</option>
            <option value="external">External</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600">Tanggal & Jam (TZ)</label>
          <input type="datetime-local" class="mt-1 w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" x-model="form.schedule_at" required>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600">Timezone</label>
          <input type="text" class="mt-1 w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" x-model="form.timezone" placeholder="Asia/Jakarta">
        </div>
        <div class="md:col-span-2">
          <label class="block text-xs font-medium text-gray-600">Alamat</label>
          <textarea class="mt-1 w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" x-model="form.address" required></textarea>
        </div>
        <div class="md:col-span-2">
          <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input type="checkbox" x-model="form.auto_paid" class="rounded border-gray-300"> <span>Auto Paid</span></label>
        </div>

        <!-- Items -->
        <div class="md:col-span-2">
          <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-semibold text-gray-800">Items</label>
            <span class="text-xs text-gray-500" x-text="form.items.length + ' item' + (form.items.length>1?'s':'')"></span>
          </div>

          <template x-for="(it, idx) in form.items" :key="it._key">
            <div class="rounded-lg border border-gray-200 bg-white shadow-sm p-3 md:p-4 mb-3">
              <div class="flex items-center justify-between mb-3">
                <div class="text-sm font-medium text-gray-700">Item #<span x-text="idx+1"></span></div>
                <button type="button" class="inline-flex items-center gap-1 px-2 py-1.5 text-sm text-gray-700 border border-gray-300 rounded-md hover:bg-gray-100" @click="remove(idx)">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 100 2h.293l.853 10.234A2 2 0 007.139 18h5.722a2 2 0 001.993-1.766L15.707 6H16a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0010 2H9zm-1 6a1 1 0 012 0v6a1 1 0 11-2 0V8zm5 0a1 1 0 10-2 0v6a1 1 0 102 0V8z" clip-rule="evenodd"/></svg>
                  Hapus
                </button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                <!-- Seller selector -->
                <div class="md:col-span-5">
                  <label class="block text-xs font-medium text-gray-600">Toko</label>
                  <div class="relative mt-1" @keydown.escape.window="it.showSeller=false">
                    <input class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder:text-gray-400"
                           placeholder="Cari toko (nama toko, ID seller)"
                           x-model="it.sellerQuery"
                           autocomplete="off"
                           @focus="openSeller(it)"
                           @input.debounce.300ms="searchSellers(it)">
                    <template x-if="it.seller">
                      <p class="text-xs text-gray-600 mt-1">
                        <span class="inline-flex items-center gap-1 bg-gray-100 px-2 py-0.5 rounded-full">
                          <span class="font-medium" x-text="formatSeller(it.seller)"></span>
                          <span class="text-gray-500">ID: <span x-text="it.seller.id"></span></span>
                        </span>
                      </p>
                    </template>
                    <div class="absolute z-40 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-md max-h-64 overflow-auto" x-show="it.showSeller" x-cloak x-transition.opacity @click.outside="it.showSeller=false">
                      <div class="p-3 text-sm text-gray-500" x-show="it.sellerLoading">Memuat...</div>
                      <template x-for="s in it.sellerOptions" :key="s.id">
                        <button type="button" class="w-full text-left px-3 py-2 hover:bg-gray-50 text-sm" @mousedown.prevent.stop="selectSeller(it, s)">
                          <span class="font-medium" x-text="formatSeller(s)"></span>
                          <span class="text-gray-500"> — ID: <span x-text="s.id"></span></span>
                        </button>
                      </template>
                      <div class="p-3 text-sm text-gray-500" x-show="!it.sellerLoading && it.sellerOptions.length === 0">Tidak ada hasil</div>
                    </div>
                  </div>
                </div>

                <!-- Product selector -->
                <div class="md:col-span-5">
                  <label class="block text-xs font-medium text-gray-600">Product</label>
                  <div class="relative mt-1" @keydown.escape.window="it.showProduct=false">
                    <input class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder:text-gray-400 disabled:cursor-not-allowed disabled:bg-gray-50"
                           :placeholder="it.seller ? 'Cari produk (nama, ID)' : 'Pilih seller dulu'"
                           :disabled="!it.seller"
                           x-model="it.productQuery"
                           autocomplete="off"
                           @focus="openProducts(it)"
                           @input.debounce.300ms="searchProducts(it)">
                    <template x-if="it.product">
                      <p class="text-xs text-gray-600 mt-1">
                        <span class="inline-flex items-center gap-1 bg-gray-100 px-2 py-0.5 rounded-full">
                          <span class="font-medium" x-text="it.product.name"></span>
                          <span class="text-gray-500">ID: <span x-text="it.product.id"></span></span>
                          <span class="text-gray-500">Stok: <span x-text="it.product.stock"></span></span>
                          <span class="text-gray-500">Harga: <span x-text="formatNumber(priceOf(it.product))"></span></span>
                        </span>
                      </p>
                    </template>
                    <div class="absolute z-40 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-md max-h-64 overflow-auto" x-show="it.showProduct" x-cloak x-transition.opacity @click.outside="it.showProduct=false">
                      <div class="p-3 text-sm text-gray-500" x-show="it.productLoading">Memuat...</div>
                      <template x-for="p in it.productOptions" :key="p.id">
                        <button type="button" class="w-full text-left px-3 py-2 hover:bg-gray-50 text-sm" @mousedown.prevent.stop="selectProduct(it, p)">
                          <span class="font-medium" x-text="p.name"></span>
                          <span class="text-gray-500"> — ID: <span x-text="p.id"></span> — Stok: <span x-text="p.stock"></span> — Harga: <span x-text="formatNumber(priceOf(p))"></span></span>
                        </button>
                      </template>
                      <div class="p-3 text-sm text-gray-500" x-show="!it.productLoading && it.productOptions.length === 0">Tidak ada hasil</div>
                    </div>
                  </div>
                </div>

                <!-- Qty -->
                <div class="md:col-span-1">
                  <label class="block text-xs font-medium text-gray-600">Qty</label>
                  <input class="mt-1 w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Qty" type="number" min="1" x-model.number="it.quantity" required>
                </div>

                <!-- Price cap -->
                <div class="md:col-span-1">
                  <label class="block text-xs font-medium text-gray-600">Price Cap</label>
                  <input class="mt-1 w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="opsional" type="number" min="1" x-model.number="it.price_cap">
                </div>
              </div>
            </div>
          </template>

          <button type="button" class="mt-2 inline-flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100" @click="addItem">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"/></svg>
            Tambah Item
          </button>
        </div>
      </div>
      <div class="md:col-span-2">
        <button type="submit" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Simpan</button>
        <a href="{{ route('admin.scheduled-orders.ui.index') }}" class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-100">Batal</a>
      </div>
    </form>
  </div>

  <script>
  function createBatch(){
    return {
      apiBase: '{{ url('/admin/api') }}',
      form: { buyer_id: '', purchase_type: 'self', address: '', schedule_at: '', timezone: 'Asia/Jakarta', auto_paid: false, items: [] },
      // Buyer state
      buyerQuery: '',
      buyerOptions: [],
      buyerSelected: null,
      buyerLoading: false,
      showBuyer: false,
      // UX prefs
      autoSelectFirst: false,

      init(){
        this.addItem();
      },

      // Utils
      formatNumber(n){ try { return new Intl.NumberFormat('id-ID').format(n); } catch(e){ return n; } },
      // prefer promo_price, else sell_price, fallback to price/harga_jual if present
      priceOf(p){ return (p?.promo_price ?? p?.sell_price ?? p?.price ?? p?.harga_jual ?? null); },
      formatBuyer(b){ return (b.full_name || b.username || ('User #' + b.id)); },
      getStoreName(s){ return s?.seller_info?.store_name || s?.store_name || s?.store?.name || ''; },
      formatSeller(s){ const name = this.getStoreName(s); return name || ('Toko #' + (s?.id ?? '')); },

      // Buyer methods
      openBuyer(){ this.showBuyer = true; if(!this.buyerOptions.length){ this.searchBuyers(); } },
      async searchBuyers(){
        this.buyerLoading = true;
        try {
          const r = await fetch(`${this.apiBase}/buyers?q=${encodeURIComponent(this.buyerQuery||'')}`, { headers:{ 'Accept':'application/json' } });
          this.buyerOptions = await r.json();
        } catch(e){ console.error(e); this.buyerOptions = []; }
        this.buyerLoading = false;
        this.showBuyer = true;
      },
      selectBuyer(b){ this.form.buyer_id = b.id; this.buyerSelected = b; this.showBuyer = false; this.buyerQuery = this.formatBuyer(b); },
      clearBuyer(){ this.form.buyer_id=''; this.buyerSelected=null; this.buyerQuery=''; this.buyerOptions=[]; },

      // Seller methods per item
      openSeller(it){ it.showSeller = true; this.searchSellers(it); },
      async searchSellers(it){
        it.sellerLoading = true;
        try {
          const qRaw = (it.sellerQuery || '').trim();
          const r = await fetch(`${this.apiBase}/sellers?q=${encodeURIComponent(qRaw)}`, { headers:{ 'Accept':'application/json' } });
          let arr = await r.json();
          if(Array.isArray(arr) && arr.length === 0 && qRaw){
            const r2 = await fetch(`${this.apiBase}/sellers?q=`, { headers:{ 'Accept':'application/json' } });
            arr = await r2.json();
          }
          if(qRaw){
            const q = qRaw.toLowerCase();
            arr = (arr || []).filter(s => (this.getStoreName(s) || '').toLowerCase().includes(q));
          }
          it.sellerOptions = arr || [];
        } catch(e){ console.error(e); it.sellerOptions = []; }
        it.sellerLoading = false;
        it.showSeller = true;
      },
      async selectSeller(it, s){
        it.seller = s; it.seller_id = s.id; it.showSeller = false; it.sellerQuery = this.getStoreName(s);
        // reset & prefetch product list for selected seller
        it.product = null; it.product_id = ''; it.productQuery=''; it.productOptions=[];
        const arr = await this.searchProducts(it);
        if((arr || []).length){
          // open dropdown immediately on seller selection
          it.showProduct = true;
          if(this.autoSelectFirst && !it.product){ this.selectProduct(it, arr[0]); }
        }
      },

      // Product methods per item
      openProducts(it){ if(!it.seller){ return; } it.showProduct = true; this.searchProducts(it); },
      async searchProducts(it){ if(!it.seller_id){ return []; }
        it.productLoading = true;
        let arr = [];
        try {
          const qRaw = (it.productQuery || '').trim();
          const baseUrl = `${this.apiBase}/products?seller_id=${encodeURIComponent(it.seller_id)}`;
          const r = await fetch(`${baseUrl}&q=${encodeURIComponent(qRaw)}`, { headers:{ 'Accept':'application/json' } });
          arr = await r.json();
          if(Array.isArray(arr) && arr.length === 0 && qRaw){
            const r2 = await fetch(`${baseUrl}&q=`, { headers:{ 'Accept':'application/json' } });
            arr = await r2.json();
          }
          if(qRaw){
            const q = qRaw.toLowerCase();
            arr = (arr || []).filter(p => (p?.name || '').toLowerCase().includes(q) || String(p?.id || '').includes(q));
          }
          it.productOptions = arr || [];
        } catch(e){ console.error(e); it.productOptions = []; }
        it.productLoading = false;
        it.showProduct = true;
        return it.productOptions;
      },
      selectProduct(it, p){ it.product = p; it.product_id = p.id; it.showProduct = false; it.productQuery = p.name; },

      // Items add/remove
      newItem(){ return { _key: (Date.now().toString(36)+Math.random().toString(36).slice(2)), seller_id:'', seller:null, sellerQuery:'', sellerOptions:[], sellerLoading:false, showSeller:false, product_id:'', product:null, productQuery:'', productOptions:[], productLoading:false, showProduct:false, quantity:1, price_cap:null }; },
      addItem(){ this.form.items.push(this.newItem()); },
      remove(i){ this.form.items.splice(i,1); if(this.form.items.length===0){ this.addItem(); } },

      // Submit
      async submit(){
        if(!this.form.buyer_id){ alert('Pilih buyer terlebih dahulu'); return; }
        const items = this.form.items.map(({seller_id, product_id, quantity, price_cap})=>({seller_id, product_id, quantity, price_cap})).filter(it=>it.seller_id && it.product_id && it.quantity>0);
        if(items.length === 0){ alert('Tambahkan minimal 1 item yang valid'); return; }
        const payload = { buyer_id: this.form.buyer_id, purchase_type: this.form.purchase_type, address: this.form.address, schedule_at: this.form.schedule_at, timezone: this.form.timezone, auto_paid: !!this.form.auto_paid, items };
        const r = await fetch(`{{ route('admin.scheduled-orders.store') }}`, { method:'POST', headers:{ 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' }, body: JSON.stringify(payload) });
        let j = {};
        try { j = await r.json(); } catch(e){}
        if(!r.ok){ alert(j.message || 'Gagal menyimpan batch'); return; }
        window.location.href = `{{ route('admin.scheduled-orders.ui.index') }}`;
      }
    }
  }
  </script>
</x-admin-layout>
