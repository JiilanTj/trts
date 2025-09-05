<x-app-layout>
    <div class="min-h-screen bg-[#0f1115] text-gray-200 relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_25%_15%,rgba(236,72,153,0.07),transparent_60%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_85%,rgba(59,130,246,0.08),transparent_65%)]"></div>
        <div class="sticky top-0 z-40 backdrop-blur-md bg-[#0f1115]/70 border-b border-white/10">
            <div class="px-3 sm:px-4 py-3 flex items-center gap-2 sm:gap-3">
                <a href="{{ route('user.orders.index') }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:bg-white/5 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60" aria-label="Kembali">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-sm sm:text-base font-semibold text-white leading-tight">Detail Order #{{ $order->id }}</h1>
                    <p class="text-[10px] sm:text-[11px] text-gray-500 mt-0.5">Status & informasi lengkap.</p>
                </div>
                @if(in_array($order->status,['pending']) && in_array($order->payment_status,['unpaid','rejected']))
                    <form method="POST" action="{{ route('user.orders.cancel',$order) }}" onsubmit="return confirm('Batalkan order ini?')">
                        @csrf
                        <button class="inline-flex items-center gap-1 sm:gap-1.5 px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg text-[10px] sm:text-[11px] font-medium bg-red-600/20 text-red-300 hover:bg-red-600/30 border border-red-600/30 focus:outline-none focus:ring-2 focus:ring-red-500/40">Batalkan</button>
                    </form>
                @endif
            </div>
        </div>
        <div class="px-3 sm:px-4 lg:px-5 py-4 sm:py-5 lg:max-w-5xl lg:mx-auto space-y-4 sm:space-y-6">
            @if(session('success'))
                <div class="bg-emerald-600/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-red-600/10 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="flex flex-col lg:grid lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                <div class="lg:col-span-2 space-y-6 lg:space-y-8 order-2 lg:order-1">
                    <div class="bg-[#181d23] border border-white/10 rounded-xl sm:rounded-2xl p-4 sm:p-5 lg:p-6 space-y-4 sm:space-y-6">
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

                    <div class="bg-[#181d23] border border-white/10 rounded-xl sm:rounded-2xl p-4 sm:p-5 lg:p-6 space-y-4 sm:space-y-6">
                        <h2 class="text-sm font-semibold text-white">Item</h2>
                        <div class="space-y-3 sm:space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center gap-3 sm:gap-4 bg-[#1f252c] border border-white/5 rounded-xl p-3 sm:p-4">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-200 leading-snug">{{ $item->product->name ?? 'Produk Dihapus' }}</p>
                                        <p class="text-[10px] text-gray-500">Qty: {{ $item->quantity }} • Harga: Rp {{ number_format($item->unit_price,0,',','.') }}</p>
                                    </div>
                                    <div class="text-xs font-semibold text-fuchsia-300">Rp {{ number_format($item->line_total,0,',','.') }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-[#181d23] border border-white/10 rounded-xl sm:rounded-2xl p-4 sm:p-5 lg:p-6 space-y-4 sm:space-y-6">
                        <h2 class="text-sm font-semibold text-white">Pembayaran</h2>
                        @if(isset($setting) && $setting && $order->canUploadProof())
                            <div class="p-3 sm:p-4 lg:p-5 rounded-xl bg-[#1f252c] border border-white/5 space-y-4 sm:space-y-5 text-sm">
                                <div class="flex items-center justify-between">
                                    <p class="text-[11px] font-semibold tracking-wide text-gray-400">INSTRUKSI TRANSFER</p>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-fuchsia-600/10 text-fuchsia-300 border border-fuchsia-500/30">Manual</span>
                                </div>
                                <div class="space-y-3">
                                    <div class="group flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                                        <div class="w-full sm:w-36 text-[10px] uppercase tracking-wide font-semibold text-gray-500">Provider</div>
                                        <div class="flex items-center gap-2 flex-1">
                                            <div class="flex-1 font-medium text-gray-200 truncate">{{ $setting->payment_provider ?: '-' }}</div>
                                            <button type="button" data-copy="{{ $setting->payment_provider }}" class="copy-btn inline-flex items-center gap-1 text-[10px] px-2 py-1 rounded-md bg-[#262e37] border border-white/10 text-gray-400 hover:text-white hover:border-fuchsia-500/40 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/40" title="Copy">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                                <span class="copy-label">Copy</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="group flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                                        <div class="w-full sm:w-36 text-[10px] uppercase tracking-wide font-semibold text-gray-500">Atas Nama</div>
                                        <div class="flex items-center gap-2 flex-1">
                                            <div class="flex-1 font-medium text-gray-200 truncate">{{ $setting->account_name ?: '-' }}</div>
                                            <button type="button" data-copy="{{ $setting->account_name }}" class="copy-btn inline-flex items-center gap-1 text-[10px] px-2 py-1 rounded-md bg-[#262e37] border border-white/10 text-gray-400 hover:text-white hover:border-fuchsia-500/40 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/40" title="Copy">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                                <span class="copy-label">Copy</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="group flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                                        <div class="w-full sm:w-36 text-[10px] uppercase tracking-wide font-semibold text-gray-500">No. Rekening</div>
                                        <div class="flex items-center gap-2 flex-1">
                                            <div class="flex-1 font-semibold text-fuchsia-300 tracking-wide text-sm">{{ $setting->account_number ?: '-' }}</div>
                                            <button type="button" data-copy="{{ $setting->account_number }}" class="copy-btn inline-flex items-center gap-1 text-[10px] px-2 py-1 rounded-md bg-fuchsia-600/15 border border-fuchsia-500/40 text-fuchsia-300 hover:bg-fuchsia-600/25 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/40" title="Copy">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                                <span class="copy-label">Copy</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="group flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                                        <div class="w-full sm:w-36 text-[10px] uppercase tracking-wide font-semibold text-gray-500">Nominal</div>
                                        <div class="flex items-center gap-2 flex-1">
                                            <div class="flex-1 font-semibold text-emerald-300 text-sm">Rp {{ number_format($order->grand_total,0,',','.') }}</div>
                                            <button type="button" data-copy="{{ $order->grand_total }}" class="copy-btn inline-flex items-center gap-1 text-[10px] px-2 py-1 rounded-md bg-emerald-600/15 border border-emerald-500/40 text-emerald-300 hover:bg-emerald-600/25 focus:outline-none focus:ring-2 focus:ring-emerald-500/40" title="Copy">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                                <span class="copy-label">Copy</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-[11px] leading-relaxed text-gray-500">
                                    Transfer sesuai nominal. Setelah itu upload bukti di bawah ini untuk verifikasi. Proses konfirmasi bisa memakan waktu beberapa menit.
                                </div>
                            </div>
                        @endif
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

                    <div class="bg-[#181d23] border border-white/10 rounded-xl sm:rounded-2xl p-4 sm:p-5 lg:p-6 space-y-3 sm:space-y-4">
                        <h2 class="text-sm font-semibold text-white">Info Pelanggan</h2>
                        <div class="grid sm:grid-cols-2 gap-3 sm:gap-4 text-sm">
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
                <div class="space-y-6 lg:space-y-8 lg:flex lg:flex-col lg:items-stretch lg:pl-2 order-1 lg:order-2">
                    <div class="bg-[#181d23] border border-white/10 rounded-xl sm:rounded-2xl p-4 sm:p-5 lg:p-7 space-y-4 lg:space-y-5 lg:sticky lg:top-24 lg:max-w-xs xl:max-w-sm lg:ml-auto shadow-lg shadow-black/20">
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
    <script>
        // Enhanced copy-to-clipboard: swaps icon temporarily to a check mark
        document.addEventListener('click', function(e){
            const btn = e.target.closest('[data-copy]');
            if(!btn) return;
            const val = btn.getAttribute('data-copy');
            if(!val) return;
            navigator.clipboard.writeText(val.trim()).then(()=>{
                if(!btn.dataset.original){ btn.dataset.original = btn.innerHTML; }
                btn.innerHTML = '<svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg><span class="copy-label text-emerald-400">Copied</span>';
                btn.classList.add('border-emerald-500/50');
                setTimeout(()=>{ btn.innerHTML = btn.dataset.original; btn.classList.remove('border-emerald-500/50'); }, 1600);
            });
        });
    </script>
</x-app-layout>
