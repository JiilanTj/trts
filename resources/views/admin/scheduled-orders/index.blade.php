<x-admin-layout>
  <x-slot name="title">Scheduled Orders</x-slot>

  <div class="bg-white rounded-lg shadow-sm border border-gray-200" id="app" x-data="scheduledOrders()" x-init="init()">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
      <div>
        <h2 class="text-lg font-semibold text-gray-800">Daftar Scheduled Orders</h2>
        <p class="text-sm text-gray-500">Kelola batch auto order yang terjadwal.</p>
      </div>
      <div class="flex items-center gap-2">
        <button @click="fetchBatches()" class="inline-flex items-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">Refresh</button>
        <a href="{{ route('admin.scheduled-orders.create') }}" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Buat Jadwal</a>
      </div>
    </div>

    <div class="divide-y">
      <template x-for="b in batches" :key="b.id">
        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
          <div>
            <div class="font-medium text-gray-900" x-text="`Batch #${b.id} - ${b.status}`"></div>
            <div class="text-sm text-gray-500" x-text="`Schedule: ${fmt(b.schedule_at, b.timezone)} (${b.timezone}) | Items: ${b.items_count}`"></div>
          </div>
          <div class="flex gap-2">
            <a :href="`{{ url('/admin/scheduled-orders/') }}/${b.id}/ui`" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-100">Detail</a>
            <button @click="runNow(b.id)" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-100">Run Now</button>
            <button @click="cancel(b.id)" class="inline-flex items-center px-3 py-1.5 border border-red-300 rounded-lg text-sm text-red-700 hover:bg-red-50">Cancel</button>
          </div>
        </div>
      </template>
      <div x-show="batches.length===0" class="p-10 text-center text-gray-500">
        <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada batch</h3>
        <p class="mt-1 text-sm text-gray-500">Buat jadwal baru untuk mulai auto order.</p>
      </div>
    </div>
  </div>

  <script>
  function scheduledOrders(){
    return {
      batches: [],
      async init(){ this.fetchBatches(); },
      fmt(dt, tz){
        try{
          const d = new Date(dt);
          return new Intl.DateTimeFormat('id-ID', { dateStyle:'medium', timeStyle:'short', timeZone: tz || 'Asia/Jakarta' }).format(d);
        }catch(e){ return dt; }
      },
      async fetchBatches(){
        const r = await fetch(`{{ route('admin.scheduled-orders.index') }}`, {headers:{'Accept':'application/json'}});
        const j = await r.json();
        this.batches = j.data || [];
      },
      async runNow(id){
        await fetch(`{{ url('/admin/scheduled-orders') }}/${id}/run-now`, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}});
        this.fetchBatches();
      },
      async cancel(id){
        await fetch(`{{ url('/admin/scheduled-orders') }}/${id}/cancel`, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}});
        this.fetchBatches();
      }
    }
  }
  </script>
</x-admin-layout>
