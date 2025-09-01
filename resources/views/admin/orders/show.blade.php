<x-admin-layout>
    <x-slot name="title">Order #{{ $order->id }}</x-slot>

    <div class="px-6 py-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Order #{{ $order->id }}</h1>
                <p class="text-sm text-gray-500">Dibuat {{ $order->created_at->format('d M Y H:i') }}</p>
            </div>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('admin.orders.cancel', $order) }}" onsubmit="return confirm('Batalkan order ini?');">
                    @csrf
                    <button type="submit" class="px-3 py-2 bg-red-50 text-red-600 text-xs font-semibold rounded-md border border-red-200 hover:bg-red-100" @disabled(in_array($order->status,['completed','cancelled']))>Batalkan</button>
                </form>
                @if(in_array($order->payment_status,['waiting_confirmation']))
                    <form method="POST" action="{{ route('admin.orders.approve-payment', $order) }}">
                        @csrf
                        <button type="submit" class="px-3 py-2 bg-green-600 text-white text-xs font-semibold rounded-md shadow hover:bg-green-700">Approve Pembayaran</button>
                    </form>
                    <button onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="px-3 py-2 bg-yellow-600 text-white text-xs font-semibold rounded-md shadow hover:bg-yellow-700">Reject</button>
                @endif
                @if(in_array($order->status,['packaging','shipped','delivered']))
                    <form method="POST" action="{{ route('admin.orders.advance-status', $order) }}">
                        @csrf
                        <button type="submit" class="px-3 py-2 bg-blue-600 text-white text-xs font-semibold rounded-md shadow hover:bg-blue-700">Next Status</button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-4 bg-white border border-gray-100 rounded-lg shadow">
                <p class="text-xs text-gray-500 font-semibold uppercase mb-1">User</p>
                <p class="font-medium text-gray-800">{{ $order->user->username }}</p>
                <p class="text-xs text-gray-400">ID: {{ $order->user_id }}</p>
            </div>
            <div class="p-4 bg-white border border-gray-100 rounded-lg shadow">
                <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Payment Status</p>
                <p class="font-semibold text-sm">{{ $order->payment_status }}</p>
                @if($order->payment_confirmed_at)
                    <p class="text-xs text-green-600">Confirmed {{ $order->payment_confirmed_at->format('d M H:i') }}</p>
                @endif
            </div>
            <div class="p-4 bg-white border border-gray-100 rounded-lg shadow">
                <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Order Status</p>
                <p class="font-semibold text-sm">{{ $order->status }}</p>
            </div>
            <div class="p-4 bg-white border border-gray-100 rounded-lg shadow">
                <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Total</p>
                <p class="text-lg font-bold text-gray-800">Rp {{ number_format($order->grand_total,0,',','.') }}</p>
            </div>
        </div>

        <!-- Items -->
        <div class="bg-white border border-gray-100 rounded-xl shadow">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800">Items</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-xs font-semibold uppercase tracking-wider text-gray-600">
                            <th class="px-4 py-3 text-left">Produk</th>
                            <th class="px-4 py-3 text-left">Qty</th>
                            <th class="px-4 py-3 text-left">Unit</th>
                            <th class="px-4 py-3 text-left">Diskon</th>
                            <th class="px-4 py-3 text-left">Margin</th>
                            <th class="px-4 py-3 text-left">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($order->items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-800">{{ $item->product->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-400">ID: {{ $item->product_id }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $item->quantity }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($item->unit_price,0,',','.') }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($item->discount * $item->quantity,0,',','.') }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($item->seller_margin * $item->quantity,0,',','.') }}</td>
                                <td class="px-4 py-3 font-semibold">Rp {{ number_format($item->line_total,0,',','.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-gray-100 flex flex-col gap-1 text-sm">
                <div class="flex justify-between"><span class="text-gray-600">Subtotal</span><span class="font-medium">Rp {{ number_format($order->subtotal,0,',','.') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-600">Diskon</span><span class="font-medium">- Rp {{ number_format($order->discount_total,0,',','.') }}</span></div>
                @if($order->seller_margin_total > 0)
                    <div class="flex justify-between"><span class="text-gray-600">Total Margin Seller</span><span class="font-medium">Rp {{ number_format($order->seller_margin_total,0,',','.') }}</span></div>
                @endif
                <div class="flex justify-between text-base font-bold pt-2 border-t border-dashed"><span>Total</span><span>Rp {{ number_format($order->grand_total,0,',','.') }}</span></div>
            </div>
        </div>

        <!-- Payment Proof -->
        <div class="bg-white border border-gray-100 rounded-xl shadow">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800">Bukti Pembayaran</h2>
            </div>
            <div class="p-5">
                @if($order->payment_proof_path)
                    <div class="flex items-center gap-4">
                        <div class="p-3 border rounded-lg bg-gray-50 flex items-center gap-3">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828L18 9.828M16 5l3 3m-6.414-1.414a2 2 0 112.828 2.828L7.828 19.828a4 4 0 01-5.656-5.656L10.586 3.586z" /></svg>
                            <div>
                                <p class="text-sm font-medium text-gray-700">File terupload</p>
                                <a target="_blank" href="{{ asset('storage/'.$order->payment_proof_path) }}" class="text-xs text-blue-600 hover:underline">Lihat / Download</a>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500">Belum ada bukti pembayaran.</p>
                @endif
            </div>
        </div>

        <!-- Notes -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white border border-gray-100 rounded-xl shadow p-5">
                <h3 class="font-semibold text-gray-800 mb-2 text-sm uppercase">Catatan User</h3>
                <div class="text-sm text-gray-600 whitespace-pre-line min-h-[60px]">{{ $order->user_notes ?? '-' }}</div>
            </div>
            <div class="bg-white border border-gray-100 rounded-xl shadow p-5">
                <h3 class="font-semibold text-gray-800 mb-2 text-sm uppercase">Catatan Admin</h3>
                <div class="text-sm text-gray-600 whitespace-pre-line min-h-[60px]">{{ $order->admin_notes ?? '-' }}</div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/40">
        <div class="bg-white w-full max-w-md rounded-xl shadow-lg border border-gray-200 p-6 space-y-4">
            <h2 class="text-lg font-semibold text-gray-800">Tolak Pembayaran</h2>
            <form method="POST" action="{{ route('admin.orders.reject-payment',$order) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Catatan (opsional)</label>
                    <textarea name="admin_notes" rows="4" class="w-full rounded-md border-gray-300 text-sm" placeholder="Alasan ditolak / instruksi ulang..."></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="px-4 py-2 text-sm rounded-md border bg-white hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm rounded-md bg-yellow-600 text-white font-medium hover:bg-yellow-700">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
