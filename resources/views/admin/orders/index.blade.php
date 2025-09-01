<x-admin-layout>
    <x-slot name="title">Orders</x-slot>

    <div class="px-6 py-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Daftar Order</h1>
                <p class="text-sm text-gray-500">Monitoring pesanan manual & status pembayaran.</p>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 bg-white p-4 rounded-lg shadow mb-6 border border-gray-100">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Status Order</label>
                <select name="status" class="w-full rounded-md border-gray-300 text-sm">
                    <option value="">Semua</option>
                    @php($statusOptions = [
                        'pending' => 'Menunggu',
                        'awaiting_confirmation' => 'Menunggu Konfirmasi',
                        'packaging' => 'Sedang Dikemas',
                        'shipped' => 'Dikirim',
                        'delivered' => 'Diterima',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ])
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected(request('status')===$value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Status Pembayaran</label>
                <select name="payment_status" class="w-full rounded-md border-gray-300 text-sm">
                    <option value="">Semua</option>
                    @php($paymentStatusOptions = [
                        'unpaid' => 'Belum Dibayar',
                        'waiting_confirmation' => 'Menunggu Konfirmasi',
                        'paid' => 'Dibayar',
                        'rejected' => 'Ditolak',
                    ])
                    @foreach($paymentStatusOptions as $value => $label)
                        <option value="{{ $value }}" @selected(request('payment_status')===$value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">User ID</label>
                <input type="text" name="user_id" value="{{ request('user_id') }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="User ID">
            </div>
            <div class="md:col-span-2 flex items-end gap-2">
                <button class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow hover:bg-blue-700">Filter</button>
                <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200">Reset</a>
            </div>
        </form>

        <div class="bg-white border border-gray-100 rounded-xl shadow">
            <div class="overflow-x-auto rounded-t-xl">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="px-4 py-3 text-left">ID</th>
                            <th class="px-4 py-3 text-left">User</th>
                            <th class="px-4 py-3 text-left">Tipe</th>
                            <th class="px-4 py-3 text-left">Subtotal</th>
                            <th class="px-4 py-3 text-left">Grand Total</th>
                            <th class="px-4 py-3 text-left">Pembayaran</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Created</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono text-xs">#{{ $order->id }}</td>
                                <td class="px-4 py-3">{{ $order->user->username ?? 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded text-xs bg-indigo-50 text-indigo-700 font-medium">{{ $order->purchase_type }}</span>
                                </td>
                                <td class="px-4 py-3">Rp {{ number_format($order->subtotal,0,',','.') }}</td>
                                <td class="px-4 py-3 font-semibold">Rp {{ number_format($order->grand_total,0,',','.') }}</td>
                                <td class="px-4 py-3">
                                    @php($ps=$order->payment_status)
                                    <span class="px-2 py-1 rounded text-xs font-medium
                                        @class([
                                            'bg-gray-100 text-gray-700'=> $ps==='unpaid',
                                            'bg-yellow-100 text-yellow-700'=> $ps==='waiting_confirmation',
                                            'bg-green-100 text-green-700'=> $ps==='paid',
                                            'bg-red-100 text-red-700'=> $ps==='rejected',
                                        ])">{{ $ps }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @php($st=$order->status)
                                    <span class="px-2 py-1 rounded text-xs font-medium @class([
                                            'bg-gray-100 text-gray-700'=> $st==='pending',
                                            'bg-yellow-100 text-yellow-700'=> $st==='awaiting_confirmation',
                                            'bg-blue-100 text-blue-700'=> $st==='packaging',
                                            'bg-indigo-100 text-indigo-700'=> $st==='shipped',
                                            'bg-purple-100 text-purple-700'=> $st==='delivered',
                                            'bg-green-100 text-green-700'=> $st==='completed',
                                            'bg-red-100 text-red-700'=> $st==='cancelled',
                                        ])">{{ $st }}</span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500">{{ $order->created_at->format('d M H:i') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium bg-white border border-gray-300 hover:bg-gray-100 shadow-sm">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-10 text-center text-sm text-gray-500">Belum ada order.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $orders->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
