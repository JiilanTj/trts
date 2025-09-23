<x-app-layout>
    <style>
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
    </style>
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
                <a href="{{ route('browse.products.index') }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:bg-white/5 transition" aria-label="Produk">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h2l2-3h10l2 3h2v13H3z"/></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-semibold text-white leading-tight">Tugas Order</h1>
                </div>
            </div>
        </div>

        <!-- Status Filter Menu -->
        <div class="sticky top-[72px] z-30 bg-[#0f1115]/95 backdrop-blur-md border-b border-white/5">
            <div class="px-4 py-3">
                <div class="flex gap-2 overflow-x-auto scrollbar-hide pb-1">
                    @foreach($statusOptions as $statusKey => $statusLabel)
                        @php
                            $count = $statusCounts[$statusKey] ?? 0;
                            $isActive = $status === $statusKey;
                            $localized = $statusLabels[$statusKey] ?? $statusLabel;
                        @endphp
                        <a href="{{ route('user.orders-by-admin.index', array_merge(request()->query(), ['status' => $statusKey])) }}"
                           class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium border transition-all duration-200 whitespace-nowrap {{
                               $isActive
                                   ? 'bg-gradient-to-r from-fuchsia-500/20 via-rose-500/20 to-cyan-500/20 border-fuchsia-500/50 text-white shadow-sm shadow-fuchsia-500/20'
                                   : 'bg-[#1a1f25] border-white/10 text-gray-400 hover:border-fuchsia-500/30 hover:text-gray-200'
                           }}">
                            <span>{{ $localized }}</span>
                            @if($count > 0)
                                <span class="px-1.5 py-0.5 text-[9px] rounded-full font-medium {{ $isActive ? 'bg-white/20 text-white' : 'bg-gray-600/30 text-gray-400' }}">{{ $count }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="px-3 sm:px-4 py-4 sm:py-6 max-w-5xl mx-auto space-y-6 sm:space-y-8">
            @if(session('success'))
                <div class="bg-emerald-600/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
            @endif
            @if(session('info'))
                <div class="bg-cyan-600/10 border border-cyan-500/30 text-cyan-300 px-4 py-3 rounded-xl text-sm">{{ session('info') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-600/10 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
            @endif

            <div class="space-y-3">
                @forelse($orders as $order)
                    <div class="bg-[#181d23] border border-white/10 rounded-xl overflow-hidden hover:bg-white/5 transition">
                        <div class="p-4 pb-2">
                            <div class="flex items-center justify-between">
                                <!-- Changed card header to remove 'Order Admin' -->
                                <p class="font-semibold text-white text-sm">Order: {{ str_pad($order->id, 10, '0', STR_PAD_LEFT) }}</p>
                                <span class="px-2 py-1 text-[10px] rounded-md font-medium {{ $statusColors[$order->status] ?? 'bg-gray-600/20 text-gray-300 border border-gray-500/30' }}">{{ $statusLabels[$order->status] ?? $order->status }}</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">{{ optional($order->created_at)->format('d-m-Y H:i') }}</p>
                        </div>
                        <div class="px-4 py-3 border-t border-white/5">
                            <div class="flex items-center gap-3">
                                @if(optional($order->product)->image_url)
                                    <img src="{{ $order->product->image_url }}" alt="{{ $order->product->name }}" class="w-12 h-12 rounded-lg object-cover bg-gray-800 border border-white/10">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-gray-700/50 border border-white/10 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-white truncate">{{ $order->product->name ?? ('Produk#'.$order->product_id) }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">Qty: {{ number_format($order->quantity) }} • Harga: Rp {{ number_format($order->unit_price,0,',','.') }}</p>
                                </div>
                                <div class="text-sm font-semibold text-fuchsia-300">Rp {{ number_format($order->total_price,0,',','.') }}</div>
                            </div>
                        </div>
                        <div class="p-4 pt-3">
                            <div class="flex items-center justify-end">
                                <div class="flex items-center gap-2">
                                    @if($order->status === \App\Models\OrderByAdmin::STATUS_PENDING)
                                        @php $sufficient = auth()->user()->hasSufficientBalance((int)$order->total_price); @endphp
                                        @if($sufficient)
                                            <form method="post" action="{{ route('user.orders-by-admin.confirm', $order) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-gradient-to-r from-fuchsia-600 to-cyan-600 text-white hover:from-fuchsia-500 hover:to-cyan-500 transition border border-white/10">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Konfirmasi
                                                </button>
                                            </form>
                                        @else
                                            <div class="flex flex-col gap-2">
                                                <span class="text-[11px] text-amber-300">Saldo kurang, silahkan chat admin untuk topup atau gunakan menu Topup pada halaman utama</span>
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('user.chat.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-[#242b33] border border-white/10 hover:border-fuchsia-500/50 hover:text-white transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h6m-6 4h10M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        Chat Admin
                                                        <span class="px-1.5 py-0.5 text-[9px] rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-medium">Lebih Cepat</span>
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                    <a href="{{ route('user.orders-by-admin.show',$order) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-[#242b33] border border-white/10 hover:border-fuchsia-500/50 hover:text-white transition">Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16">
                        <div class="w-14 h-14 mx-auto bg-gradient-to-br from-fuchsia-600/20 to-cyan-500/20 border border-white/10 rounded-full flex items-center justify-center mb-5">
                            <svg class="w-7 h-7 text-fuchsia-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18M6 7h12M6 11h12M6 15h12"/></svg>
                        </div>
                        @if(request('status'))
                            @php $statusKey = strtoupper(request('status')); @endphp
                            <h3 class="text-sm font-semibold text-white mb-2">Tidak Ada Order {{ $statusLabels[$statusKey] ?? ucfirst(strtolower(request('status'))) }}</h3>
                            <p class="text-xs text-gray-500 mb-6 max-w-xs mx-auto leading-relaxed">Belum ada order dengan status tersebut.</p>
                            <!-- Fix broken route helper -->
                            <a href="{{ route('user.orders-by-admin.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-medium bg-[#242b33] border border-white/10 hover:border-fuchsia-500/50 hover:text-white transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                Lihat Semua
                            </a>
                        @else
                            <h3 class="text-sm font-semibold text-white mb-2">Belum Ada Order</h3>
                            <p class="text-xs text-gray-500 mb-6 max-w-xs mx-auto leading-relaxed">Order yang dibuat untuk Anda akan tampil di sini.</p>
                        @endif
                    </div>
                @endforelse
            </div>

            @if($orders->hasPages())
                <div class="mt-6">{{ $orders->appends(request()->query())->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
