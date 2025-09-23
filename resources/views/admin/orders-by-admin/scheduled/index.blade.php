<x-admin-layout>
    <x-slot name="title">Jadwal Order Admin</x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200" id="app" x-data="scheduledOrders()" x-init="init()">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Jadwal Order Admin</h2>
                <p class="text-sm text-gray-500">Kelola batch order yang akan dibuat otomatis pada waktu tertentu.</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="fetchBatches()" class="inline-flex items-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">Refresh</button>
                <button @click="openCreate()" class="inline-flex items-center bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Buat Jadwal
                </button>
            </div>
        </div>

        <!-- List -->
        <div class="divide-y" x-show="view==='list'">
            <template x-for="b in batches" :key="b.id">
                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <div class="font-semibold text-gray-900" x-text="`Batch #${b.id}`"></div>
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium"
                                  :class="{
                                    'bg-gray-100 text-gray-800': b.status==='scheduled',
                                    'bg-yellow-100 text-yellow-800': b.status==='processing',
                                    'bg-emerald-100 text-emerald-800': b.status==='completed',
                                    'bg-rose-100 text-rose-800': b.status==='failed',
                                    'bg-slate-100 text-slate-800': b.status==='canceled',
                                  }" x-text="b.status"></span>
                        </div>
                        <div class="text-sm text-gray-600 mt-1">
                            <span x-text="`Jadwal: ${fmt(b.schedule_at, b.timezone)} (${b.timezone || 'Asia/Jakarta'})`"></span>
                            <span class="mx-2 text-gray-300">•</span>
                            <span x-text="`Items: ${b.items?.length || b.items_count || 0}`"></span>
                            <template x-if="b.seller">
                                <span><span class="mx-2 text-gray-300">•</span>Seller: <span class="font-medium" x-text="b.seller.full_name || b.seller.username"></span></span>
                            </template>
                        </div>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        <button @click="runNow(b)" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-100">Run Now</button>
                        <button @click="cancel(b)" class="inline-flex items-center px-3 py-1.5 border border-red-300 rounded-lg text-sm text-red-700 hover:bg-red-50">Cancel</button>
                    </div>
                </div>
            </template>
            <div x-show="batches.length===0" class="p-12 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada jadwal</h3>
                <p class="mt-1 text-sm text-gray-500">Klik "Buat Jadwal" untuk membuat batch order otomatis.</p>
            </div>
        </div>

        <!-- Create Modal -->
        <div x-show="view==='create'" class="p-6">
            <div class="max-w-3xl">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Buat Jadwal Order</h3>
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Seller (User)</label>
                            <select x-model.number="form.user_id" @change="fetchShowcases()" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih seller</option>
                                @foreach(($users ?? []) as $u)
                                    <option value="{{ data_get($u, 'id') }}">{{ data_get($u, 'username') }} - {{ data_get($u, 'full_name') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Zona Waktu</label>
                            <select x-model="form.timezone" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                                <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Waktu Eksekusi</label>
                            <input type="datetime-local" x-model="form.schedule_at" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                            <p class="text-xs text-gray-500 mt-1">Diinterpretasikan pada zona waktu terpilih.</p>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-2">Item</label>
                        <div id="items-container" class="space-y-4">
                            <template x-for="(it, idx) in form.items" :key="idx">
                                <div class="order-item border rounded-lg p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Pilih Item Etalase</label>
                                            <div class="flex items-start gap-3">
                                                <img :id="`thumb_${idx}`" :src="it.image_url || ''" alt="Thumb" class="w-12 h-12 rounded object-cover bg-gray-100 border border-gray-200" :class="{'invisible': !it.image_url}" />
                                                <div class="flex-1">
                                                    <select :id="`store_showcase_id_${idx}`" x-model.number="it.store_showcase_id" @change="onShowcaseChange(idx)" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                        <option value="">Pilih seller terlebih dahulu</option>
                                                        <template x-for="sc in showcases" :key="sc.id">
                                                            <option :value="sc.id" :data-product-id="sc.product_id" :data-sell-price="sc.sell_price" :data-promo-price="sc.promo_price" :data-image-url="sc.image_url" x-text="`#${sc.id} - ${sc.product_name || ('Produk#' + sc.product_id)}`"></option>
                                                        </template>
                                                    </select>
                                                    <input type="hidden" :id="`product_id_${idx}`" x-model.number="it.product_id" />
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Qty</label>
                                            <input type="number" min="1" x-model.number="it.quantity" @input="recalc(idx)" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Harga/Unit</label>
                                            <input type="number" :id="`unit_price_${idx}`" x-model.number="it.unit_price" readonly class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-gray-700" />
                                            <p class="text-xs text-gray-500 mt-1">Otomatis dari harga jual produk seller.</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Total Harga</label>
                                            <input type="number" :id="`total_price_${idx}`" x-model.number="it.total_price" readonly class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-gray-700" />
                                            <p class="text-xs text-gray-500 mt-1">Otomatis: Qty × Harga/Unit.</p>
                                        </div>
                                    </div>
                                    <div class="mt-3 text-right">
                                        <button type="button" @click="removeItem(idx)" class="inline-flex items-center px-3 py-1.5 rounded border border-red-200 text-red-700 hover:bg-red-50 text-xs">Hapus Item</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="mt-3">
                            <button type="button" @click="addItem()" class="inline-flex items-center px-3 py-2 rounded-lg text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 border border-gray-300">+ Tambah Item</button>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="save()" class="px-5 py-2 rounded-lg text-sm bg-emerald-600 hover:bg-emerald-700 text-white font-medium">Simpan</button>
                        <button type="button" @click="view='list'" class="px-4 py-2 rounded-lg text-sm bg-gray-200 hover:bg-gray-300 text-gray-800">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function scheduledOrders(){
        return {
            view: 'list',
            batches: [],
            showcases: [],
            form: {
                user_id: '',
                timezone: 'Asia/Jakarta',
                schedule_at: '',
                items: [ { store_showcase_id: '', product_id: '', quantity: 1, unit_price: 0, total_price: 0, image_url: '' } ],
            },
            fmt(dt, tz){
                try{ const d = new Date(dt); return new Intl.DateTimeFormat('id-ID',{dateStyle:'medium', timeStyle:'short', timeZone: tz||'Asia/Jakarta'}).format(d);}catch(e){return dt;}
            },
            async init(){ await this.fetchBatches(); },
            openCreate(){ this.view='create'; this.resetForm(); },
            resetForm(){
                this.form = { user_id: '', timezone: 'Asia/Jakarta', schedule_at: '', items: [ { store_showcase_id: '', product_id: '', quantity: 1, unit_price: 0, total_price: 0, image_url: '' } ] };
                this.showcases = [];
            },
            async fetchBatches(){
                const r = await fetch(`{{ route('admin.orders-by-admin.scheduled.index') }}`, {headers:{'Accept':'application/json'}});
                const j = await r.json();
                this.batches = j.data || [];
            },
            async fetchShowcases(){
                if(!this.form.user_id){ this.showcases=[]; return; }
                const r = await fetch(`{{ route('admin.api.showcases') }}?seller_id=${this.form.user_id}`);
                const data = await r.json();
                this.showcases = Array.isArray(data)? data: [];
                // if items exist, prefill first
                if(this.form.items.length){
                    const s0 = this.showcases[0];
                    if(s0){
                        this.form.items[0].store_showcase_id = s0.id;
                        this.form.items[0].product_id = s0.product_id;
                        this.form.items[0].unit_price = parseInt(s0.sell_price || s0.promo_price || 0) || 0;
                        this.form.items[0].total_price = this.form.items[0].unit_price * (parseInt(this.form.items[0].quantity)||0);
                        this.form.items[0].image_url = s0.image_url || '';
                    }
                }
            },
            onShowcaseChange(idx){
                const sel = document.getElementById(`store_showcase_id_${idx}`);
                const opt = sel?.selectedOptions?.[0];
                if(!opt) return;
                const pid = opt.dataset.productId || '';
                const price = parseInt(opt.dataset.sellPrice || opt.dataset.promoPrice || 0) || 0;
                const img = opt.dataset.imageUrl || '';
                this.form.items[idx].product_id = pid ? parseInt(pid) : '';
                this.form.items[idx].unit_price = price;
                this.form.items[idx].image_url = img;
                this.recalc(idx);
            },
            recalc(idx){
                const it = this.form.items[idx];
                const q = parseInt(it.quantity||0) || 0;
                const u = parseInt(it.unit_price||0) || 0;
                it.total_price = q*u;
            },
            addItem(){
                this.form.items.push({ store_showcase_id: '', product_id: '', quantity: 1, unit_price: 0, total_price: 0, image_url: '' });
            },
            removeItem(idx){
                if(this.form.items.length<=1){
                    const it = this.form.items[0];
                    it.store_showcase_id=''; it.product_id=''; it.quantity=1; it.unit_price=0; it.total_price=0; it.image_url='';
                    return;
                }
                this.form.items.splice(idx,1);
            },
            async runNow(b){
                await fetch(`{{ route('admin.orders-by-admin.scheduled.run-now', ['scheduled'=>0]) }}`.replace('0', b.id), {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}});
                this.fetchBatches();
            },
            async cancel(b){
                await fetch(`{{ route('admin.orders-by-admin.scheduled.cancel', ['scheduled'=>0]) }}`.replace('0', b.id), {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}});
                this.fetchBatches();
            },
            async save(){
                // simple validation
                if(!this.form.user_id){ alert('Pilih seller.'); return; }
                if(!this.form.schedule_at){ alert('Isi waktu eksekusi.'); return; }
                if(!this.form.items.length){ alert('Tambah minimal 1 item.'); return; }
                const payload = {
                    user_id: this.form.user_id,
                    timezone: this.form.timezone,
                    schedule_at: this.form.schedule_at,
                    items: this.form.items.map(i=>({ store_showcase_id: i.store_showcase_id, product_id: i.product_id, quantity: i.quantity }))
                };
                const r = await fetch(`{{ route('admin.orders-by-admin.scheduled.store') }}`, {
                    method:'POST',
                    headers: { 'Content-Type':'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' },
                    body: JSON.stringify(payload)
                });
                if(r.ok){
                    this.view='list';
                    await this.fetchBatches();
                } else {
                    const j = await r.json().catch(()=>({message:'Gagal menyimpan'}));
                    alert(j.message || 'Gagal menyimpan');
                }
            }
        }
    }
    </script>
</x-admin-layout>
