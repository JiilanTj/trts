<x-app-layout>
    <style>
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>
    <div class="min-h-screen bg-[#0f1115] text-gray-200 relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_25%_15%,rgba(236,72,153,0.07),transparent_60%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_85%,rgba(59,130,246,0.08),transparent_65%)]"></div>
        <div class="sticky top-0 z-40 backdrop-blur-md bg-[#0f1115]/70 border-b border-white/10">
            <div class="px-4 py-3 flex items-center gap-3">
                <a href="{{ route('browse.products.index') }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:bg-white/5 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60" aria-label="Produk">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h2l2-3h10l2 3h2v13H3z" /></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-semibold text-white leading-tight">Order Saya</h1>
                    <p class="text-[11px] text-gray-500 mt-0.5">Riwayat order & status pembayaran.</p>
                </div>
                <a href="{{ route('user.orders.create') }}" class="sm:hidden inline-flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-r from-fuchsia-500 via-rose-500 to-cyan-500 text-white hover:from-fuchsia-500/90 hover:via-rose-500/90 hover:to-cyan-500/90 shadow-sm shadow-fuchsia-500/30 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60" aria-label="Order Baru">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                </a>
                <a href="{{ route('user.orders.create') }}" class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-gradient-to-r from-fuchsia-500 via-rose-500 to-cyan-500 text-white hover:from-fuchsia-500/90 hover:via-rose-500/90 hover:to-cyan-500/90 shadow-sm shadow-fuchsia-500/30 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Order Baru
                </a>
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
                        @endphp
                        <a href="{{ route('user.orders.index', array_merge(request()->query(), ['status' => $statusKey])) }}" 
                           class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium border transition-all duration-200 whitespace-nowrap {{ 
                               $isActive 
                                   ? 'bg-gradient-to-r from-fuchsia-500/20 via-rose-500/20 to-cyan-500/20 border-fuchsia-500/50 text-white shadow-sm shadow-fuchsia-500/20' 
                                   : 'bg-[#1a1f25] border-white/10 text-gray-400 hover:border-fuchsia-500/30 hover:text-gray-200' 
                           }}">
                            <span>{{ $statusLabel }}</span>
                            @if($count > 0)
                                <span class="px-1.5 py-0.5 text-[9px] rounded-full font-medium {{ 
                                    $isActive 
                                        ? 'bg-white/20 text-white' 
                                        : 'bg-gray-600/30 text-gray-400' 
                                }}">
                                    {{ $count }}
                                </span>
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
            @if(session('error'))
                <div class="bg-red-600/10 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
            @endif

            <div class="bg-[#181d23] border border-white/10 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-[#1b1f25]/70">
                            <tr class="text-[11px] text-gray-400 uppercase tracking-wide">
                                <th class="px-3 sm:px-4 py-3 text-left font-semibold">ID</th>
                                <th class="px-3 sm:px-4 py-3 text-left font-semibold hidden sm:table-cell">Tipe</th>
                                <th class="px-3 sm:px-4 py-3 text-left font-semibold hidden md:table-cell">Subtotal</th>
                                <th class="px-3 sm:px-4 py-3 text-left font-semibold">Total</th>
                                <th class="px-3 sm:px-4 py-3 text-left font-semibold hidden sm:table-cell">Pembayaran</th>
                                <th class="px-3 sm:px-4 py-3 text-left font-semibold">Status</th>
                                <th class="px-3 sm:px-4 py-3 text-left font-semibold hidden lg:table-cell">Tanggal</th>
                                <th class="px-3 sm:px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($orders as $order)
                                <tr class="hover:bg-white/5 transition">
                                    <td class="px-3 sm:px-4 py-3 font-mono text-[11px] text-gray-400">#{{ $order->id }}</td>
                                    <td class="px-3 sm:px-4 py-3 hidden sm:table-cell">
                                        <span class="px-2 py-1 text-[10px] rounded-full font-medium {{ $order->purchase_type==='external' ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-600/30' : 'bg-fuchsia-600/20 text-fuchsia-300 border border-fuchsia-600/30' }}">{{ $order->purchase_type==='external' ? 'Eksternal' : 'Pribadi' }}</span>
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 hidden md:table-cell">Rp {{ number_format($order->subtotal,0,',','.') }}</td>
                                    <td class="px-3 sm:px-4 py-3 font-semibold">Rp {{ number_format($order->grand_total,0,',','.') }}</td>
                                    <td class="px-3 sm:px-4 py-3 hidden sm:table-cell">
                                        @php($ps=$order->payment_status)
                                        <span class="px-2 py-1 rounded-full text-[10px] font-medium border {{ 
                                            $ps === 'unpaid' ? 'bg-gray-600/20 text-gray-300 border-gray-600/40' :
                                            ($ps === 'waiting_confirmation' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' :
                                            ($ps === 'paid' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' :
                                            'bg-red-500/20 text-red-300 border-red-500/40'))
                                        }}">{{ $ps }}</span>
                                    </td>
                                    <td class="px-3 sm:px-4 py-3">
                                        @php($st=$order->status)
                                        <span class="px-2 py-1 rounded-full text-[10px] font-medium border {{ 
                                            $st === 'pending' ? 'bg-gray-600/20 text-gray-300 border-gray-600/40' :
                                            ($st === 'awaiting_confirmation' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' :
                                            ($st === 'packaging' ? 'bg-blue-500/20 text-blue-300 border-blue-500/40' :
                                            ($st === 'shipped' ? 'bg-indigo-500/20 text-indigo-300 border-indigo-500/40' :
                                            ($st === 'delivered' ? 'bg-purple-500/20 text-purple-300 border-purple-500/40' :
                                            ($st === 'completed' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' :
                                            'bg-red-500/20 text-red-300 border-red-500/40')))))
                                        }}">{{ $st }}</span>
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 text-[11px] text-gray-400 hidden lg:table-cell">{{ $order->created_at->format('d M H:i') }}</td>
                                    <td class="px-3 sm:px-4 py-3 text-right">
                                        <a href="{{ route('user.orders.show',$order) }}" class="inline-flex items-center gap-1 px-2 sm:px-3 py-1.5 rounded-lg text-[11px] font-medium bg-[#242b33] border border-white/10 hover:border-fuchsia-500/50 hover:text-white transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/50">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-16 text-center">
                                        <div class="w-14 h-14 mx-auto bg-gradient-to-br from-fuchsia-600/20 to-cyan-500/20 border border-white/10 rounded-full flex items-center justify-center mb-5">
                                            <svg class="w-7 h-7 text-fuchsia-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m5-9v9m4-9v9m4-9l2 9" /></svg>
                                        </div>
                                        @if(request('status') && request('status') !== 'all')
                                            <h3 class="text-sm font-semibold text-white mb-2">Tidak Ada Order {{ ucfirst(str_replace('_', ' ', request('status'))) }}</h3>
                                            <p class="text-xs text-gray-500 mb-6 max-w-xs mx-auto leading-relaxed">Belum ada order dengan status "{{ ucfirst(str_replace('_', ' ', request('status'))) }}".</p>
                                            <a href="{{ route('user.orders.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-medium bg-[#242b33] border border-white/10 hover:border-fuchsia-500/50 hover:text-white transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/50 mr-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                                                Lihat Semua
                                            </a>
                                        @else
                                            <h3 class="text-sm font-semibold text-white mb-2">Belum Ada Order</h3>
                                            <p class="text-xs text-gray-500 mb-6 max-w-xs mx-auto leading-relaxed">Buat order pertama Anda untuk mulai pembelian manual.</p>
                                        @endif
                                        <a href="{{ route('user.orders.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-medium bg-gradient-to-r from-fuchsia-500 via-rose-500 to-cyan-500 text-white hover:from-fuchsia-500/90 hover:via-rose-500/90 hover:to-cyan-500/90 shadow-sm shadow-fuchsia-500/30 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                            Order Baru
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-white/10">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
