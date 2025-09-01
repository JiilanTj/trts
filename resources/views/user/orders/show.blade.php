<x-app-layout>
    <div class="min-h-screen bg-[#0f1115] text-gray-200 relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_25%_15%,rgba(236,72,153,0.07),transparent_60%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_85%,rgba(59,130,246,0.08),transparent_65%)]"></div>
        <div class="sticky top-0 z-40 backdrop-blur-md bg-[#0f1115]/70 border-b border-white/10">
            <div class="px-4 py-3 flex items-center gap-3">
                <a href="{{ route('user.orders.index') }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:bg-white/5 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60" aria-label="Kembali">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-semibold text-white leading-tight">Detail Order #{{ $order->id }}</h1>
                    <p class="text-[11px] text-gray-500 mt-0.5">Status & informasi lengkap.</p>
                </div>
                @if(in_array($order->status,['pending']) && in_array($order->payment_status,['unpaid','rejected']))
                    <form method="POST" action="{{ route('user.orders.cancel',$order) }}" onsubmit="return confirm('Batalkan order ini?')">
                        @csrf
                        <button class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-[11px] font-medium bg-red-600/20 text-red-300 hover:bg-red-600/30 border border-red-600/30 focus:outline-none focus:ring-2 focus:ring-red-500/40">Batalkan</button>
                    </form>
                @endif
            </div>
        </div>
        <div class="px-4 py-6 max-w-5xl mx-auto space-y-8">
            @if(session('success'))
                <div class="bg-emerald-600/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-red-600/10 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="grid md:grid-cols-3 gap-8">
                <div class="md:col-span-2 space-y-8">
                    <div class="bg-[#181d23] border border-white/10 rounded-2xl p-6 space-y-6">
                        <h2 class="text-sm font-semibold text-white">Status Order</h2>
                        <div class="flex flex-wrap gap-3 text-[11px] font-medium">
                            <span class="px-2.5 py-1 rounded-full bg-[#1f252c] border border-white/5 text-gray-300">Pembayaran: {{ $order->payment_status }}</span>
                            <span class="px-2.5 py-1 rounded-full bg-[#1f252c] border border-white/5 text-gray-300">Status: {{ $order->status }}</span>
                            <span class="px-2.5 py-1 rounded-full bg-[#1f252c] border border-white/5 text-gray-300">Tipe: {{ $order->purchase_type==='external' ? 'Eksternal':'Pribadi' }}</span>
                            <span class="px-2.5 py-1 rounded-full bg-[#1f252c] border border-white/5 text-gray-300">Total Item: {{ $order->items->sum('quantity') }}</span>
                        </div>
                        @if($order->user_notes)
                            <div class="p-4 rounded-xl bg-[#1f252c] border border-white/5">
                                <p class="text-[11px] font-semibold text-gray-400 mb-1">Catatan Pengguna:</p>
                                <p class="text-sm text-gray-300 leading-relaxed">{{ $order->user_notes }}</p>
                            </div>
                        @endif
                        @if($order->admin_notes)
                            <div class="p-4 rounded-xl bg-[#1f252c] border border-white/5">
                                <p class="text-[11px] font-semibold text-gray-400 mb-1">Catatan Admin:</p>
                                <p class="text-sm text-gray-300 leading-relaxed">{{ $order->admin_notes }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="bg-[#181d23] border border-white/10 rounded-2xl p-6 space-y-6">
                        <h2 class="text-sm font-semibold text-white">Item</h2>
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center gap-4 bg-[#1f252c] border border-white/5 rounded-xl p-4">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-200 leading-snug">{{ $item->product->name ?? 'Produk Dihapus' }}</p>
                                        <p class="text-[10px] text-gray-500">Qty: {{ $item->quantity }} • Harga: Rp {{ number_format($item->unit_price,0,',','.') }}</p>
                                    </div>
                                    <div class="text-xs font-semibold text-fuchsia-300">Rp {{ number_format($item->line_total,0,',','.') }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-[#181d23] border border-white/10 rounded-2xl p-6 space-y-6">
                        <h2 class="text-sm font-semibold text-white">Pembayaran</h2>
                        @if($order->canUploadProof())
                            <form method="POST" action="{{ route('user.orders.upload-proof',$order) }}" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-400 mb-1">Upload Bukti (jpg/png/pdf)</label>
                                    <input type="file" name="payment_proof" accept="image/*,.pdf" class="w-full text-sm bg-[#1b1f25] border border-white/10 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/50 text-gray-200 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-fuchsia-600/20 file:text-fuchsia-300 hover:file:bg-fuchsia-600/30">
                                </div>
                                <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-medium bg-gradient-to-r from-fuchsia-500 via-rose-500 to-cyan-500 text-white hover:from-fuchsia-500/90 hover:via-rose-500/90 hover:to-cyan-500/90 shadow-sm shadow-fuchsia-500/30 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M8 12l4 4m0 0l4-4m-4 4V4" /></svg>
                                    Upload
                                </button>
                            </form>
                        @else
                            <div class="text-sm text-gray-400">Status pembayaran: <span class="text-gray-300 font-medium">{{ $order->payment_status }}</span>.</div>
                            @if($order->payment_proof_path)
                                <div class="mt-3">
                                    <a href="{{ Storage::url($order->payment_proof_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-medium bg-[#242b33] border border-white/10 hover:border-fuchsia-500/50 hover:text-white transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/50">Lihat Bukti</a>
                                </div>
                            @endif
                        @endif
                    </div>

                    <div class="bg-[#181d23] border border-white/10 rounded-2xl p-6 space-y-4">
                        <h2 class="text-sm font-semibold text-white">Info Pelanggan</h2>
                        <div class="grid sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-[11px] font-medium text-gray-400 mb-1">Nama</p>
                                <p class="text-gray-300">{{ $order->external_customer_name ?: '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-medium text-gray-400 mb-1">Telepon</p>
                                <p class="text-gray-300">{{ $order->external_customer_phone ?: '-' }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <p class="text-[11px] font-medium text-gray-400 mb-1">Alamat</p>
                                <p class="text-gray-300">{{ $order->address ?: '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="space-y-8">
                    <div class="bg-[#181d23] border border-white/10 rounded-2xl p-6 space-y-5 sticky top-24">
                        <h2 class="text-sm font-semibold text-white">Ringkasan</h2>
                        <div class="space-y-3 text-[13px] font-medium">
                            <div class="flex items-center justify-between"><span class="text-gray-400">Subtotal</span><span class="text-gray-200">Rp {{ number_format($order->subtotal,0,',','.') }}</span></div>
                            <div class="flex items-center justify-between"><span class="text-gray-400">Diskon</span><span class="text-gray-200">Rp {{ number_format($order->discount_total,0,',','.') }}</span></div>
                            <div class="flex items-center justify-between"><span class="text-gray-400">Grand Total</span><span class="text-fuchsia-300">Rp {{ number_format($order->grand_total,0,',','.') }}</span></div>
                            @if(auth()->user()->isSeller())
                                <div class="flex items-center justify-between"><span class="text-gray-400">Margin Seller</span><span class="text-emerald-300">Rp {{ number_format($order->seller_margin_total,0,',','.') }}</span></div>
                            @endif
                        </div>
                        <p class="text-[10px] text-gray-500 leading-relaxed">Admin akan memproses order setelah bukti pembayaran valid.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
