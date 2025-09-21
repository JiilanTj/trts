<x-admin-layout>
  <x-slot name="title">Detail Scheduled Batch</x-slot>

  <div class="bg-white rounded-lg shadow-sm border border-gray-200" x-data="showBatch()" x-init="init()">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
      <div>
        <h2 class="text-lg font-semibold text-gray-800">Detail Batch</h2>
        <p class="text-sm text-gray-500" x-text="meta"></p>
      </div>
      <div class="flex items-center gap-2">
        <a href="{{ route('admin.scheduled-orders.ui.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-100">Kembali</a>
        <button class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-100" @click="runNow">Run Now</button>
        <button class="inline-flex items-center px-4 py-2 border border-red-300 rounded-lg text-sm text-red-700 hover:bg-red-50" @click="cancel">Cancel</button>
      </div>
    </div>

    <div class="p-6">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left border-b">
              <th class="py-2">Seller</th>
              <th>Product</th>
              <th>Qty</th>
              <th>Status</th>
              <th>Order ID</th>
              <th>Error</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="it in (batch?.items || [])" :key="it.id">
              <tr class="border-b">
                <td class="py-2" x-text="it.seller?.full_name || it.seller_id"></td>
                <td x-text="it.product?.name || it.product_id"></td>
                <td x-text="it.quantity"></td>
                <td x-text="it.status"></td>
                <td><a :href="`{{ url('/admin/orders') }}/${it.created_order_id}`" class="text-blue-600" x-text="it.created_order_id || '-'" target="_blank"></a></td>
                <td x-text="it.error_message || '-' "></td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
  function showBatch(){
    return {
      batch: null,
      fmt(dt, tz){
        try{ const d = new Date(dt); return new Intl.DateTimeFormat('id-ID', { dateStyle:'medium', timeStyle:'short', timeZone: tz || 'Asia/Jakarta' }).format(d); }catch(e){ return dt; }
      },
      get meta(){
        if(!this.batch) return '';
        const tz = this.batch.timezone || 'Asia/Jakarta';
        const when = this.fmt(this.batch.schedule_at, tz);
        return `Buyer: ${this.batch.buyer?.full_name || this.batch.buyer_id} | TZ: ${tz} | Schedule: ${when}`;
      },
      async init(){
        const id = window.location.pathname.split('/').filter(Boolean).slice(-2)[0]; // path ends with /{id}/ui
        const r = await fetch(`{{ url('/admin/scheduled-orders') }}/${id}`, {headers:{'Accept':'application/json'}});
        this.batch = await r.json();
      },
      async runNow(){
        const id = this.batch.id; await fetch(`{{ url('/admin/scheduled-orders') }}/${id}/run-now`, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}); location.reload();
      },
      async cancel(){
        const id = this.batch.id; await fetch(`{{ url('/admin/scheduled-orders') }}/${id}/cancel`, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}); location.reload();
      }
    }
  }
  </script>
</x-admin-layout>
