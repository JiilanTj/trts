@php /** @var \Illuminate\Pagination\LengthAwarePaginator $orders */ @endphp
<x-admin-layout>
    <x-slot name="title">Order Admin</x-slot>

    @php
        $statusColors = [
            'PENDING' => 'bg-gray-100 text-gray-800',
            'CONFIRMED' => 'bg-green-100 text-green-800',
            'PACKED' => 'bg-yellow-100 text-yellow-800',
            'SHIPPED' => 'bg-blue-100 text-blue-800',
            'DELIVERED' => 'bg-emerald-100 text-emerald-800',
        ];
        $statusLabels = [
            'PENDING' => 'Menunggu Konfirmasi',
            'CONFIRMED' => 'Dikonfirmasi',
            'PACKED' => 'Dikemas',
            'SHIPPED' => 'Dikirim',
            'DELIVERED' => 'Terkirim',
        ];
        $selectedStatus = isset($status) ? strtoupper($status) : null;
    @endphp

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Order Admin</h2>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.orders-by-admin.scheduled.index') }}" class="inline-flex items-center bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                        Jadwal Order Admin
                    </a>
                    <a href="{{ route('admin.orders-by-admin.create') }}" class="inline-flex items-center bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Buat Order Admin
                    </a>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="mt-4">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                        <select name="status" class="px-3 py-2 text-sm rounded-lg bg-white border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua</option>
                            <option value="PENDING" @selected($selectedStatus==='PENDING')>{{ $statusLabels['PENDING'] }}</option>
                            <option value="CONFIRMED" @selected($selectedStatus==='CONFIRMED')>{{ $statusLabels['CONFIRMED'] }}</option>
                            <option value="PACKED" @selected($selectedStatus==='PACKED')>{{ $statusLabels['PACKED'] }}</option>
                            <option value="SHIPPED" @selected($selectedStatus==='SHIPPED')>{{ $statusLabels['SHIPPED'] }}</option>
                            <option value="DELIVERED" @selected($selectedStatus==='DELIVERED')>{{ $statusLabels['DELIVERED'] }}</option>
                        </select>
                    </div>
                    <div class="flex gap-2 items-center mt-1">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">Filter</button>
                        @if(request('status'))
                            <a href="{{ route('admin.orders-by-admin.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-medium transition">Reset</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('status'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('status') }}</div>
        @endif

        <!-- Table -->
        <div class="overflow-x-auto mt-4">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga/Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total + Profit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-mono">#{{ $order->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $order->user->full_name ?? ('User#'.$order->user_id) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $order->admin->full_name ?? ('Admin#'.$order->admin_id) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $order->product->name ?? ('Produk#'.$order->product_id) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 max-w-xs">
                                <span title="{{ $order->adress }}">{{ \Illuminate\Support\Str::limit($order->adress, 40) ?: '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ number_format($order->quantity) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Rp {{ number_format($order->unit_price,0,',','.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">Rp {{ number_format($order->total_price,0,',','.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                @php
                                    $pInt = optional($order->user)->getLevelMarginPercent();
                                    $p = (int)($pInt ?? 0);
                                @endphp
                                {{ number_format($p, 0) }}%
                                <span class="text-gray-400">(Lvl {{ optional($order->user)->level ?? 0 }})</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                @php $p = (optional($order->user)->getLevelMarginPercent() ?? 0) / 100; @endphp
                                Rp {{ number_format((int) round(($order->total_price ?? 0) * (1 + $p)),0,',','.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php $st = strtoupper($order->status); @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$st] ?? 'bg-gray-100 text-gray-800' }}">{{ $statusLabels[$st] ?? $st }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional($order->created_at)->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center gap-3">
                                <a href="{{ route('admin.orders-by-admin.show',$order) }}" class="text-blue-600 hover:text-blue-900">Detail</a>
                                <a href="{{ route('admin.orders-by-admin.edit',$order) }}" class="text-gray-700 hover:text-gray-900">Edit</a>
                                <!-- Confirm action removed from index -->
                                <form method="POST" action="{{ route('admin.orders-by-admin.destroy',$order) }}" onsubmit="return confirm('Hapus order ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-700 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada Order Admin</h3>
                                <p class="mt-1 text-sm text-gray-500">Order yang dibuat oleh admin akan tampil di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
