@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6" x-data="detail()" x-init="init()">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Batch #<span x-text="batch?.id"></span></h1>
    <div class="space-x-2">
      <button @click="runNow()" x-show="['scheduled','failed','partial'].includes(batch?.status)" class="px-3 py-1 bg-indigo-600 text-white rounded">Run now</button>
      <button @click="cancel()" x-show="batch?.status==='scheduled'" class="px-3 py-1 bg-rose-600 text-white rounded">Cancel</button>
    </div>
  </div>

  <div class="bg-white shadow rounded p-4 mb-6">
    <div class="grid grid-cols-2 gap-4 text-sm">
      <div><strong>Buyer:</strong> <span x-text="batch?.buyer?.full_name || batch?.buyer_id"></span></div>
      <div><strong>Status:</strong> <span x-text="batch?.status"></span></div>
      <div><strong>Schedule:</strong> <span x-text="formatLocal(batch?.schedule_at, batch?.timezone)"></span></div>
      <div><strong>Timezone:</strong> <span x-text="batch?.timezone"></span></div>
      <div class="col-span-2"><strong>Address:</strong> <span x-text="batch?.address"></span></div>
      <div class="col-span-2"><strong>User Notes:</strong> <span x-text="batch?.user_notes || '-' "></span></div>
    </div>
  </div>

  <div class="bg-white shadow rounded overflow-hidden">
    <table class="min-w-full">
      <thead class="bg-slate-50">
        <tr>
          <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Seller</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Product</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Qty</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Status</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Order</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Error</th>
        </tr>
      </thead>
      <tbody>
        <template x-for="it in batch?.items || []" :key="it.id">
          <tr class="border-t">
            <td class="px-4 py-3 text-sm" x-text="it.seller?.full_name || it.seller_id"></td>
            <td class="px-4 py-3 text-sm" x-text="it.product?.name || it.product_id"></td>
            <td class="px-4 py-3 text-sm" x-text="it.quantity"></td>
            <td class="px-4 py-3 text-sm" x-text="it.status"></td>
            <td class="px-4 py-3 text-sm" x-text="it.created_order_id || '-' "></td>
            <td class="px-4 py-3 text-sm" x-text="it.error_message || '-' "></td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</div>
<script>
function detail(){
  return {
    batch: null,
    async init(){ await this.load(); },
    async load(){
      const res = await fetch(`{{ url('/admin/scheduled-orders') }}/{{ request()->route('batch') }}`, { headers: { 'Accept':'application/json' }});
      this.batch = await res.json();
    },
    formatLocal(utc, tz){ try { return new Date(utc + 'Z').toLocaleString('id-ID', { timeZone: tz || 'Asia/Jakarta' }); } catch(e){ return utc; } },
    async runNow(){ await fetch(`{{ url('/admin/scheduled-orders') }}/${this.batch.id}/run-now`, { method:'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }}); await this.load(); },
    async cancel(){ await fetch(`{{ url('/admin/scheduled-orders') }}/${this.batch.id}/cancel`, { method:'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }}); await this.load(); },
  }
}
</script>
@endsection
