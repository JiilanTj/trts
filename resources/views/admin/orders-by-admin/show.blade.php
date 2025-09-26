@php /** @var \App\Models\OrderByAdmin $order */ @endphp
<x-admin-layout>
    <x-slot name="title">Detail Order Admin #{{ $order->id }}</x-slot>

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
    @endphp

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Detail Order Admin #{{ $order->id }}</h2>
                    <p class="text-sm text-gray-500">Dibuat {{ optional($order->created_at)->format('d M Y H:i') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.orders-by-admin.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        Kembali
                    </a>
                    @if(strtoupper($order->status)==='PENDING')
                        <form method="POST" action="{{ route('admin.orders-by-admin.confirm',$order) }}" onsubmit="return confirm('Konfirmasi order ini?');" class="inline">
                            @csrf
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Konfirmasi</button>
                        </form>
                    @endif
                    <a href="{{ route('admin.orders-by-admin.edit',$order) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Ubah</a>
                    <form method="POST" action="{{ route('admin.orders-by-admin.destroy',$order) }}" onsubmit="return confirm('Hapus order ini?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm font-medium border border-red-200 transition-colors">Hapus</button>
                    </form>
                </div>
            </div>
        </div>

        @if(session('status'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <!-- Body -->
        <div class="p-6 space-y-10">
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Status</label>
                            <div class="mt-1">
                                @php $st=strtoupper($order->status); @endphp
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $statusColors[$st] ?? 'bg-gray-100 text-gray-800' }}">{{ $statusLabels[$st] ?? $st }}</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Admin</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $order->admin->full_name ?? ('Admin#'.$order->admin_id) }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">User</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $order->user->full_name ?? ('User#'.$order->user_id) }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Gambar Produk</label>
                            <div class="mt-1">
                                @if(optional($order->product)->image_url)
                                    <img src="{{ $order->product->image_url }}" alt="Gambar Produk" class="w-24 h-24 rounded object-cover border border-gray-200" />
                                @else
                                    <div class="w-24 h-24 rounded bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400 text-xs">Tidak ada gambar</div>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Produk</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $order->product->name ?? ('Produk#'.$order->product_id) }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Etalase</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">#{{ $order->store_showcase_id }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Dibuat Pada</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ optional($order->created_at)->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Qty</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ number_format($order->quantity) }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Harga/Unit</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">Rp {{ number_format($order->unit_price,0,',','.') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Total</label>
                            <p class="mt-1 text-sm font-semibold text-blue-600 bg-blue-50 px-3 py-2 rounded-md">Rp {{ number_format($order->total_price,0,',','.') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Alamat Pengiriman</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md whitespace-pre-line">{{ $order->adress ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
