@php /** @var \App\Models\Order $order */ @endphp
<x-admin-layout>
    <x-slot name="title">Detail Order #{{ $order->id }}</x-slot>

    @php
        $statusColors = [
            'pending' => 'bg-gray-100 text-gray-800',
            'awaiting_confirmation' => 'bg-yellow-100 text-yellow-800',
            'packaging' => 'bg-blue-100 text-blue-800',
            'shipped' => 'bg-indigo-100 text-indigo-800',
            'delivered' => 'bg-teal-100 text-teal-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
        ];
        $paymentColors = [
            'unpaid' => 'bg-red-100 text-red-800',
            'waiting_confirmation' => 'bg-yellow-100 text-yellow-800',
            'paid' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-rose-100 text-rose-800',
        ];
    @endphp

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Detail Order #{{ $order->id }}</h2>
                    <p class="text-sm text-gray-500">Dibuat {{ $order->created_at->format('d M Y H:i') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.orders.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        Kembali ke Orders
                    </a>
                    <form method="POST" action="{{ route('admin.orders.cancel', $order) }}" onsubmit="return confirm('Batalkan order ini?');" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm font-medium border border-red-200 transition-colors" @disabled(in_array($order->status,['completed','cancelled']))>
                            Batalkan
                        </button>
                    </form>
                    @if($order->payment_status==='waiting_confirmation')
                        <form method="POST" action="{{ route('admin.orders.approve-payment', $order) }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Approve Pembayaran</button>
                        </form>
                        <button type="button" onclick="showRejectModal()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Reject</button>
                    @endif
                    @if(in_array($order->status,['packaging','shipped','delivered']))
                        <form method="POST" action="{{ route('admin.orders.advance-status', $order) }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Next Status</button>
                        </form>
                    @endif
                    <!-- Flexible Status Update -->
                    @if(!in_array($order->status, ['completed', 'cancelled']))
                        <button type="button" onclick="showStatusModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Ubah Status</button>
                    @endif
                    @if($order->isBalancePayment() && $order->payment_status==='paid' && !in_array($order->status,['shipped','delivered','completed']))
                        <form method="POST" action="{{ route('admin.orders.refund', $order) }}" onsubmit="return confirm('Refund order saldo ini? Saldo user akan dikembalikan.');" class="inline">
                            @csrf
                            <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Refund Saldo</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Flash / Errors -->
        @if(session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <!-- Body -->
        <div class="p-6 space-y-10">
            <!-- Ringkasan -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Status Order</label>
                            <div class="mt-1">
                                @php $st=$order->status; @endphp
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $statusColors[$st] ?? 'bg-gray-100 text-gray-800' }}">{{ $order->statusLabel() }}</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Status Pembayaran</label>
                            <div class="mt-1">
                                @php $ps=$order->payment_status; @endphp
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $paymentColors[$ps] ?? 'bg-gray-100 text-gray-800' }}">{{ $order->paymentStatusLabel() }}</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Metode Pembayaran</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $order->isBalancePayment() ? 'Saldo (otomatis)' : 'Transfer Manual' }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600">User</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $order->user->full_name }} (ID {{ $order->user_id }})</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Total Item</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $order->items->sum('quantity') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Dibuat Pada</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $order->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Grand Total</label>
                            <p class="mt-1 text-sm font-semibold text-blue-600 bg-blue-50 px-3 py-2 rounded-md">Rp {{ number_format($order->grand_total,0,',','.') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Margin Seller</label>
                            <p class="mt-1 text-sm font-semibold text-green-600 bg-green-50 px-3 py-2 rounded-md">Rp {{ number_format($order->seller_margin_total,0,',','.') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Diskon</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">Rp {{ number_format($order->discount_total,0,',','.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Pelanggan -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Info Pelanggan</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Nama</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $order->external_customer_name ?: '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Telepon</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $order->external_customer_phone ?: '-' }}</p>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-gray-600">Alamat</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $order->address ?: '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Item -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Item</h3>
                <div class="bg-gray-50 rounded-lg overflow-hidden border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($order->items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($item->unit_price,0,',','.') }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">Rp {{ number_format($item->line_total,0,',','.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pembayaran -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pembayaran</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Bukti Pembayaran</label>
                        <div class="mt-1">
                            @if($order->isBalancePayment())
                                <p class="text-sm text-cyan-700 bg-cyan-50 border border-cyan-100 rounded-md px-3 py-2">Dibayar otomatis via saldo. Tidak ada bukti.</p>
                            @else
                                @if($order->payment_proof_path)
                                    <a href="{{ Storage::url($order->payment_proof_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50">Lihat Bukti</a>
                                @else
                                    <p class="text-sm text-gray-500">Belum ada bukti.</p>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600">Konfirmasi</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                            @if($order->confirmer)
                                Dikonfirmasi oleh {{ $order->confirmer->full_name }} pada {{ optional($order->payment_confirmed_at)->format('d M Y H:i') }}
                            @else
                                @if($order->isBalancePayment() && $order->payment_status==='paid')
                                    Pembayaran saldo otomatis pada {{ optional($order->payment_confirmed_at)->format('d M Y H:i') }}
                                @else
                                    -
                                @endif
                            @endif
                        </p>
                    </div>
                </div>
                @if($order->isBalancePayment())
                    <div class="mt-4 p-4 rounded-md bg-cyan-50 border border-cyan-100">
                        <p class="text-xs font-semibold text-cyan-700 mb-1">Pembayaran Saldo</p>
                        <p class="text-sm text-cyan-700">Saldo user telah dipotong saat order dibuat. @if($order->payment_status==='refunded') Refund dilakukan pada {{ optional($order->payment_refunded_at)->format('d M Y H:i') }}. @endif</p>
                    </div>
                @endif
                @if($order->payment_status==='refunded')
                    <div class="mt-4 p-4 rounded-md bg-rose-50 border border-rose-100">
                        <p class="text-xs font-semibold text-rose-700 mb-1">Refund</p>
                        <p class="text-sm text-rose-700">Order direfund pada {{ optional($order->payment_refunded_at)->format('d M Y H:i') }}. Saldo telah dikembalikan ke user.</p>
                    </div>
                @endif
            </div>

            <!-- Catatan -->
            @if($order->user_notes || $order->admin_notes)
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Catatan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($order->user_notes)
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Catatan Pengguna</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $order->user_notes }}</p>
                            </div>
                        @endif
                        @if($order->admin_notes)
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Catatan Admin</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $order->admin_notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white w-full max-w-md rounded-lg shadow-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Tolak Pembayaran</h2>
            <form method="POST" action="{{ route('admin.orders.reject-payment',$order) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Catatan (opsional)</label>
                    <textarea name="admin_notes" rows="4" class="w-full rounded-md border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Alasan ditolak / instruksi ulang..."></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="hideRejectModal()" class="px-4 py-2 text-sm rounded-md border bg-white hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm rounded-md bg-yellow-600 text-white font-medium hover:bg-yellow-700">Kirim</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/40">
        <div class="bg-white w-full max-w-md rounded-lg shadow-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Ubah Status Order</h2>
            <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Status Baru</label>
                    <select name="status" class="w-full rounded-md border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach(App\Models\Order::statusOptions() as $statusKey => $statusLabel)
                            <option value="{{ $statusKey }}" {{ $order->status === $statusKey ? 'selected' : '' }}>
                                {{ $statusLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Catatan Admin (opsional)</label>
                    <textarea name="admin_notes" rows="3" class="w-full rounded-md border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Catatan untuk perubahan status...">{{ $order->admin_notes }}</textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="hideStatusModal()" class="px-4 py-2 text-sm rounded-md border bg-white hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm rounded-md bg-purple-600 text-white font-medium hover:bg-purple-700">Update Status</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Fix modal display
        document.addEventListener('DOMContentLoaded', function() {
            const statusModal = document.getElementById('statusModal');
            const rejectModal = document.getElementById('rejectModal');
            
            // Show status modal
            window.showStatusModal = function() {
                statusModal.classList.remove('hidden');
                statusModal.classList.add('flex');
            }
            
            // Hide status modal
            window.hideStatusModal = function() {
                statusModal.classList.add('hidden');
                statusModal.classList.remove('flex');
            }
            
            // Show reject modal
            window.showRejectModal = function() {
                rejectModal.classList.remove('hidden');
                rejectModal.classList.add('flex');
            }
            
            // Hide reject modal
            window.hideRejectModal = function() {
                rejectModal.classList.add('hidden');
                rejectModal.classList.remove('flex');
            }
        });
    </script>
</x-admin-layout>
