<x-app-layout>
    @php
        $statusColors = [
            'PENDING' => 'bg-amber-500/20 text-amber-300 border border-amber-500/30',
            'CONFIRMED' => 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30',
            'PACKED' => 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30',
            'SHIPPED' => 'bg-blue-500/20 text-blue-300 border border-blue-500/30',
            'DELIVERED' => 'bg-emerald-600/20 text-emerald-300 border border-emerald-600/30',
        ];
        $statusLabels = [
            'PENDING' => 'Menunggu Konfirmasi',
            'CONFIRMED' => 'Dikonfirmasi',
            'PACKED' => 'Dikemas',
            'SHIPPED' => 'Dikirim',
            'DELIVERED' => 'Terkirim',
        ];
    @endphp

    <div class="min-h-screen bg-[#0f1115] text-gray-200 relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_25%_15%,rgba(236,72,153,0.07),transparent_60%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_85%,rgba(59,130,246,0.08),transparent_65%)]"></div>

        <!-- Top bar -->
        <div class="sticky top-0 z-40 backdrop-blur-md bg-[#0f1115]/70 border-b border-white/10">
            <div class="px-4 py-3 flex items-center gap-3">
                <a href="{{ route('user.orders-by-admin.index') }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:bg-white/5 transition" aria-label="Kembali">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-semibold text-white leading-tight">Tugas Order</h1>
                    <p class="text-[11px] text-gray-500 mt-0.5">{{ str_pad($order->id, 10, '0', STR_PAD_LEFT) }}</p>
                </div>
                <span class="px-2 py-1 text-[10px] rounded-md font-medium {{ $statusColors[$order->status] ?? 'bg-gray-600/20 text-gray-300 border border-gray-500/30' }}">{{ $statusLabels[$order->status] ?? $order->status }}</span>
            </div>
        </div>

        <div class="px-3 sm:px-4 py-4 sm:py-6 max-w-3xl mx-auto space-y-4 sm:space-y-6">
            @if(session('success'))
                <div class="bg-emerald-600/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
            @endif
            @if(session('info'))
                <div class="bg-cyan-600/10 border border-cyan-500/30 text-cyan-300 px-4 py-3 rounded-xl text-sm">{{ session('info') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-600/10 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-red-600/10 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-[#181d23] border border-white/10 rounded-2xl overflow-hidden">
                <div class="p-4 sm:p-6 flex gap-4">
                    <div class="shrink-0">
                        @if(optional($order->product)->image_url)
                            <img src="{{ $order->product->image_url }}" alt="{{ $order->product->name }}" class="w-28 h-28 sm:w-32 sm:h-32 rounded-xl object-cover bg-gray-800 border border-white/10">
                        @else
                            <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-xl bg-gray-700/50 border border-white/10 flex items-center justify-center">
                                <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <h2 class="text-white font-semibold text-sm sm:text-base">{{ $order->product->name ?? ('Produk#'.$order->product_id) }}</h2>
                        <p class="text-[11px] text-gray-400 mt-1">Dibuat: {{ optional($order->created_at)->setTimezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB</p>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-4">
                            <div class="bg-[#0f1115]/60 border border-white/5 rounded-xl p-3">
                                <p class="text-[10px] text-gray-400">Harga Satuan</p>
                                <p class="text-sm font-semibold text-white">Rp {{ number_format($order->unit_price,0,',','.') }}</p>
                            </div>
                            <div class="bg-[#0f1115]/60 border border-white/5 rounded-xl p-3">
                                <p class="text-[10px] text-gray-400">Kuantitas</p>
                                <p class="text-sm font-semibold text-white">{{ number_format($order->quantity) }}</p>
                            </div>
                            <div class="bg-[#0f1115]/60 border border-white/5 rounded-xl p-3 col-span-2 sm:col-span-1">
                                <p class="text-[10px] text-gray-400">Total Harga</p>
                                <p class="text-sm font-semibold text-fuchsia-300">Rp {{ number_format($order->total_price,0,',','.') }}</p>
                            </div>
                            <div class="bg-[#0f1115]/60 border border-white/5 rounded-xl p-3 col-span-2">
                                <p class="text-[10px] text-gray-400">Alamat Pengiriman</p>
                                <p class="text-xs text-gray-200 whitespace-pre-line mt-1">{{ $order->adress ?: '-' }}</p>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center gap-2 text-[11px] text-gray-400">
                            <span class="px-2 py-0.5 rounded-md bg-[#242b33] border border-white/10 text-gray-300">{{ str_pad($order->id, 10, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                </div>

                @if($order->status === \App\Models\OrderByAdmin::STATUS_PENDING)
                    <div class="px-4 sm:px-6 pb-5">
                        <div class="rounded-xl bg-amber-500/10 border border-amber-500/30 p-3 sm:p-4 text-xs text-amber-200 mb-3">
                            Pastikan saldo Anda mencukupi untuk konfirmasi. Total yang akan dipotong: <span class="font-semibold">Rp {{ number_format($order->total_price,0,',','.') }}</span>.
                        </div>
                        @php $sufficient = auth()->user()->hasSufficientBalance((int)$order->total_price); @endphp
                        @if($sufficient)
                            <form method="post" action="{{ route('user.orders-by-admin.confirm', $order) }}" class="inline-flex">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-medium bg-gradient-to-r from-fuchsia-600 to-cyan-600 text-white hover:from-fuchsia-500 hover:to-cyan-500 transition shadow-lg shadow-fuchsia-600/10 border border-white/10">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Konfirmasi
                                </button>
                            </form>
                        @else
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-amber-300 text-xs">Saldo kurang, silahkan chat admin untuk topup</span>
                                <a href="{{ route('user.chat.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-medium bg-[#242b33] border border-white/10 hover:border-fuchsia-500/50 hover:text-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h6m-6 4h10M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Chat Admin
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="text-[10px] text-gray-500">Terakhir diperbarui: {{ optional($order->updated_at)->setTimezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB</div>
        </div>
    </div>
</x-app-layout>
