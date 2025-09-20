@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Scheduled Batches</h1>
        <a href="{{ route('admin.scheduled-orders.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">New Batch</a>
    </div>

    <div id="app" x-data="scheduledBatches()" x-init="init()">
        <div class="mb-4 flex gap-2">
            <input x-model="filters.search" type="text" placeholder="Search buyer/creator" class="border rounded px-3 py-2 w-72">
            <select x-model="filters.status" class="border rounded px-3 py-2">
                <option value="">All Status</option>
                <option value="scheduled">Scheduled</option>
                <option value="processing">Processing</option>
                <option value="completed">Completed</option>
                <option value="partial">Partial</option>
                <option value="failed">Failed</option>
                <option value="canceled">Canceled</option>
            </select>
            <button @click="load()" class="px-4 py-2 bg-slate-700 text-white rounded">Apply</button>
        </div>

        <div class="bg-white shadow rounded overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">ID</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Buyer</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Schedule (local)</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Items</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="b in batches" :key="b.id">
                        <tr class="border-t">
                            <td class="px-4 py-3 text-sm" x-text="b.id"></td>
                            <td class="px-4 py-3 text-sm" x-text="b.buyer?.full_name || b.buyer_id"></td>
                            <td class="px-4 py-3 text-sm" x-text="formatLocal(b.schedule_at, b.timezone)"></td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded text-white" :class="statusClass(b.status)" x-text="b.status"></span>
                            </td>
                            <td class="px-4 py-3 text-sm" x-text="b.items_count"></td>
                            <td class="px-4 py-3 text-sm text-right">
                                <a :href="'{{ url('/admin/scheduled-orders') }}/' + b.id" class="text-emerald-700 hover:underline">Detail</a>
                                <button @click="runNow(b)" class="ml-3 text-indigo-700 hover:underline" x-show="['scheduled','failed','partial'].includes(b.status)">Run now</button>
                                <button @click="cancel(b)" class="ml-3 text-rose-700 hover:underline" x-show="b.status==='scheduled'">Cancel</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function scheduledBatches(){
  return {
    filters: { status: '', search: '' },
    batches: [],
    init(){ this.load(); },
    async load(){
      const params = new URLSearchParams();
      if(this.filters.status) params.set('status', this.filters.status);
      if(this.filters.search) params.set('search', this.filters.search);
      const res = await fetch(`{{ route('admin.scheduled-orders.index') }}?` + params.toString(), { headers: { 'Accept': 'application/json' }});
      const data = await res.json();
      this.batches = data.data || data;
    },
    formatLocal(utc, tz){ try { return new Date(utc + 'Z').toLocaleString('id-ID', { timeZone: tz || 'Asia/Jakarta' }); } catch(e){ return utc; } },
    statusClass(s){
      return {
        scheduled: 'bg-amber-500', processing: 'bg-indigo-500', completed: 'bg-emerald-600', partial: 'bg-amber-700', failed: 'bg-rose-600', canceled: 'bg-slate-500'
      }[s] || 'bg-slate-600';
    },
    async runNow(b){
      await fetch(`{{ url('/admin/scheduled-orders') }}/${b.id}/run-now`, { method:'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }});
      this.load();
    },
    async cancel(b){
      await fetch(`{{ url('/admin/scheduled-orders') }}/${b.id}/cancel`, { method:'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }});
      this.load();
    }
  }
}
</script>
@endsection
