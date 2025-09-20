@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
  <h1 class="text-2xl font-semibold mb-6">New Scheduled Batch</h1>
  <form method="POST" action="{{ route('admin.scheduled-orders.store') }}" id="batchForm">
    @csrf
    <div class="grid grid-cols-1 gap-4">
      <label class="block">Buyer ID
        <input type="number" name="buyer_id" class="mt-1 w-full border rounded px-3 py-2" required>
      </label>
      <label class="block">Purchase Type
        <select name="purchase_type" class="mt-1 w-full border rounded px-3 py-2">
          <option value="self">Self</option>
          <option value="external">External</option>
        </select>
      </label>
      <label class="block">External Customer Name
        <input type="text" name="external_customer_name" class="mt-1 w-full border rounded px-3 py-2">
      </label>
      <label class="block">External Customer Phone
        <input type="text" name="external_customer_phone" class="mt-1 w-full border rounded px-3 py-2">
      </label>
      <label class="block">Address
        <textarea name="address" class="mt-1 w-full border rounded px-3 py-2" required></textarea>
      </label>
      <label class="block">User Notes
        <textarea name="user_notes" class="mt-1 w-full border rounded px-3 py-2"></textarea>
      </label>
      <label class="flex items-center gap-2"><input type="checkbox" name="auto_paid" value="1"> Auto mark paid</label>
      <div class="grid grid-cols-2 gap-4">
        <label class="block">Schedule At (Local)
          <input type="datetime-local" id="schedule_local" class="mt-1 w-full border rounded px-3 py-2" required>
        </label>
        <label class="block">Timezone
          <input type="text" name="timezone" value="Asia/Jakarta" class="mt-1 w-full border rounded px-3 py-2" required>
        </label>
      </div>
      <input type="hidden" name="schedule_at" id="schedule_at">

      <div x-data="itemsForm()" class="mt-6">
        <div class="flex items-center justify-between mb-2">
          <h2 class="text-lg font-semibold">Items</h2>
          <button type="button" @click="add()" class="px-3 py-1 bg-slate-700 text-white rounded">Add Item</button>
        </div>
        <template x-for="(it, idx) in items" :key="idx">
          <div class="grid grid-cols-4 gap-3 mb-2">
            <input type="number" x-model.number="it.seller_id" placeholder="Seller ID" class="border rounded px-3 py-2" required>
            <input type="number" x-model.number="it.product_id" placeholder="Product ID" class="border rounded px-3 py-2" required>
            <input type="number" x-model.number="it.quantity" placeholder="Qty" class="border rounded px-3 py-2" required>
            <input type="number" x-model.number="it.price_cap" placeholder="Price cap (opt)" class="border rounded px-3 py-2">
          </div>
        </template>
        <input type="hidden" name="items" :value="JSON.stringify(items)">
      </div>

      <div class="mt-4 flex justify-end">
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded">Schedule</button>
      </div>
    </div>
  </form>
</div>
<script>
function itemsForm(){ return { items: [{seller_id:'',product_id:'',quantity:1,price_cap:null}], add(){ this.items.push({seller_id:'',product_id:'',quantity:1,price_cap:null}) } } }

document.getElementById('batchForm').addEventListener('submit', (e)=>{
  const local = document.getElementById('schedule_local').value;
  if(!local) return;
  // send as 'YYYY-MM-DD HH:mm' string in the provided timezone
  document.getElementById('schedule_at').value = local.replace('T',' ');
});
</script>
@endsection
