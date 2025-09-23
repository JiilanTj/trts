@php /** @var \App\Models\OrderByAdmin $order */ @endphp
<x-admin-layout>
    <x-slot name="title">Ubah Order Admin #{{ $order->id }}</x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Ubah Order Admin</h2>
                <a href="{{ route('admin.orders-by-admin.show',$order) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Kembali</a>
            </div>
        </div>
        <div class="p-6">
            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            @php $currentStatus = strtoupper(old('status', $order->status)); @endphp
            <form method="POST" action="{{ route('admin.orders-by-admin.update',$order) }}" class="space-y-6" id="edit-order-admin-form">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Qty</label>
                        <input type="number" name="quantity" id="quantity" min="1" value="{{ old('quantity',$order->quantity) }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Harga/Unit</label>
                        <input type="number" name="unit_price" id="unit_price" min="0" value="{{ old('unit_price',$order->unit_price) }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        <p class="text-xs text-gray-500 mt-1">Harga otomatis dari produk saat dibuat, bisa disesuaikan.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="PENDING" @selected($currentStatus==='PENDING')>Menunggu Konfirmasi</option>
                            <option value="CONFIRMED" @selected($currentStatus==='CONFIRMED')>Dikonfirmasi</option>
                            <option value="PACKED" @selected($currentStatus==='PACKED')>Dikemas</option>
                            <option value="SHIPPED" @selected($currentStatus==='SHIPPED')>Dikirim</option>
                            <option value="DELIVERED" @selected($currentStatus==='DELIVERED')>Terkirim</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Gambar Produk</label>
                        <div class="mt-1">
                            @if(optional($order->product)->image_url)
                                <img src="{{ $order->product->image_url }}" alt="Gambar Produk" class="w-24 h-24 rounded object-cover border border-gray-200" />
                            @else
                                <div class="w-24 h-24 rounded bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400 text-xs">Tidak ada gambar</div>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Produk</label>
                        <input type="text" value="{{ $order->product->name ?? ('Produk#'.$order->product_id) }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-gray-700" readonly />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Total Harga</label>
                        <input type="number" id="total_price" class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-gray-700" value="{{ (int)old('quantity',$order->quantity) * (int)old('unit_price',$order->unit_price) }}" readonly />
                        <p class="text-xs text-gray-500 mt-1">Otomatis: Qty × Harga/Unit.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.orders-by-admin.show',$order) }}" class="px-4 py-2 rounded-lg text-sm bg-gray-200 hover:bg-gray-300 text-gray-800">Batal</a>
                    <button class="px-5 py-2 rounded-lg text-sm bg-blue-600 hover:bg-blue-700 text-white font-medium">Simpan</button>
                </div>
            </form>

            <script>
                function updateTotal(){
                    const qty = parseInt(document.getElementById('quantity')?.value || '0', 10) || 0;
                    const unit = parseInt(document.getElementById('unit_price')?.value || '0', 10) || 0;
                    const totalEl = document.getElementById('total_price');
                    if (totalEl) totalEl.value = qty * unit;
                }
                document.addEventListener('DOMContentLoaded', () => {
                    const q = document.getElementById('quantity');
                    const u = document.getElementById('unit_price');
                    q?.addEventListener('input', updateTotal);
                    u?.addEventListener('input', updateTotal);
                    updateTotal();
                });
            </script>
        </div>
    </div>
</x-admin-layout>
