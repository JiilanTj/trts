<x-app-layout>
    @php
        $user = auth()->user();
        $kyc = $user->kyc;
        $latestKycRequest = $user->kycRequests()->latest()->first();
        $detail = $user->detail;
        $kycStatus = $kyc ? 'verified' : ($latestKycRequest ? $latestKycRequest->status : 'none');
        $kycMap = [
            'verified' => ['label' => 'Verified', 'color' => 'emerald', 'text' => 'KYC berhasil disetujui'],
            'approved' => ['label' => 'Approved', 'color' => 'emerald', 'text' => 'Menunggu snapshot'],
            'review' => ['label' => 'Review', 'color' => 'amber', 'text' => 'Sedang ditinjau admin'],
            'pending' => ['label' => 'Pending', 'color' => 'slate', 'text' => 'Menunggu diproses'],
            'rejected' => ['label' => 'Rejected', 'color' => 'rose', 'text' => 'Ditolak – perbaiki & ajukan ulang'],
            'none' => ['label' => 'Belum KYC', 'color' => 'slate', 'text' => 'Ajukan verifikasi identitas'],
        ];
        $k = $kycMap[$kycStatus] ?? $kycMap['none'];
        $initials = Str::of($user->full_name)->trim()->explode(' ')->map(fn($p)=>Str::substr($p,0,1))->take(2)->implode('');
    @endphp

    <div class="min-h-screen bg-[#1a1d21] text-neutral-100 pb-24">
        <!-- Header / Profile Brief -->
        <div class="sticky top-0 z-30 backdrop-blur bg-[#1f2226]/95 border-b border-neutral-800/70">
            <div class="px-4 py-4 flex items-center gap-4">
                <!-- Avatar (photo or initials) -->
                <div class="relative w-14 h-14">
                    @if($user->photo_url)
                        <img src="{{ $user->photo_url }}" alt="Avatar" class="w-14 h-14 rounded-full object-cover ring-2 ring-neutral-700" />
                    @else
                        <div class="w-14 h-14 rounded-full p-0.5 bg-gradient-to-br from-[#FE2C55] to-[#25F4EE]">
                            <div class="w-full h-full rounded-full bg-[#23272b] flex items-center justify-center text-lg font-semibold tracking-wide">
                                {{ strtoupper($initials) }}
                            </div>
                        </div>
                    @endif
                    @if($kyc)
                        <span class="absolute -bottom-1 -right-1 inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-500 text-[10px] font-bold shadow ring-2 ring-[#1f2226]">✓</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <h1 class="text-lg font-semibold leading-tight truncate">{{ $user->full_name }}</h1>
                    <p class="text-xs text-neutral-400">{{ $user->username }}</p>
                    <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px]">
                        <span class="px-2 py-0.5 rounded-md bg-neutral-700/40 text-neutral-300 border border-neutral-700">Lv {{ $user->level }}</span>
                        <span class="px-2 py-0.5 rounded-md bg-neutral-700/40 text-neutral-300 border border-neutral-700">Saldo: <span class="text-neutral-100 font-medium">{{ number_format($user->balance,0,',','.') }}</span></span>
                        @if($user->isSeller())
                            <span class="px-2 py-0.5 rounded-md bg-orange-500/15 text-orange-300 border border-orange-600/30">Seller</span>
                        @endif
                        <span class="px-2 py-0.5 rounded-md bg-{{ $k['color'] === 'slate' ? 'neutral' : $k['color'] }}-500/15 text-{{ $k['color'] === 'slate' ? 'neutral' : $k['color'] }}-300 border border-{{ $k['color'] === 'slate' ? 'neutral' : $k['color'] }}-600/30">{{ $k['label'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4 py-6 space-y-8 max-w-xl mx-auto">
            <!-- Stats Row -->
            <div class="grid grid-cols-3 gap-3">
                <div class="rounded-lg bg-[#23272b] border border-[#2c3136] px-3 py-3">
                    <p class="text-[10px] uppercase tracking-wide text-neutral-400">Pesanan</p>
                    <p class="mt-1 text-sm font-semibold">{{ $user->orders()->count() }}</p>
                </div>
                <div class="rounded-lg bg-[#23272b] border border-[#2c3136] px-3 py-3">
                    <p class="text-[10px] uppercase tracking-wide text-neutral-400">KYC</p>
                    <p class="mt-1 text-sm font-semibold">{{ $k['label'] }}</p>
                </div>
                <div class="rounded-lg bg-[#23272b] border border-[#2c3136] px-3 py-3">
                    <p class="text-[10px] uppercase tracking-wide text-neutral-400">Role</p>
                    <p class="mt-1 text-sm font-semibold capitalize">{{ $user->role }}</p>
                </div>
            </div>

            <!-- Account Overview -->
            <section class="space-y-3">
                <h2 class="text-xs font-semibold tracking-wide text-neutral-400 uppercase">Akun</h2>
                <div class="rounded-xl overflow-hidden divide-y divide-neutral-800 border border-[#2c3136] bg-[#23272b]">
                    <a href="{{ route('user.profile.edit') }}" class="flex items-center justify-between px-4 py-3 hover:bg-[#272c31] transition">
                        <div>
                            <p class="text-sm font-medium">Edit Profil</p>
                            <p class="text-[11px] text-neutral-400">Ubah nama, username, detail.</p>
                        </div>
                        <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('user.orders.index') }}" class="flex items-center justify-between px-4 py-3 hover:bg-[#272c31] transition">
                        <div>
                            <p class="text-sm font-medium">Pesanan Saya</p>
                            <p class="text-[11px] text-neutral-400">Kelola dan pantau status.</p>
                        </div>
                        <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('seller-requests.index') }}" class="flex items-center justify-between px-4 py-3 hover:bg-[#272c31] transition">
                        <div>
                            <p class="text-sm font-medium">Permintaan Seller</p>
                            <p class="text-[11px] text-neutral-400">Ajukan atau cek status.</p>
                        </div>
                        <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('invitation-codes.index') }}" class="flex items-center justify-between px-4 py-3 hover:bg-[#272c31] transition">
                        <div>
                            <p class="text-sm font-medium">Kode Undangan</p>
                            <p class="text-[11px] text-neutral-400">Kelola & generate kode.</p>
                        </div>
                        <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </section>

            <!-- Extended Profile Detail -->
            <section class="space-y-3">
                <h2 class="text-xs font-semibold tracking-wide text-neutral-400 uppercase">Detail Profil</h2>
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4 space-y-3">
                    @if($detail)
                        <div class="grid grid-cols-1 gap-y-3 text-[13px]">
                            <div>
                                <span class="text-neutral-500">Telepon</span>
                                <div class="font-medium mt-0.5">{{ $detail->phone ?: '-' }}</div>
                            </div>
                            <div>
                                <span class="text-neutral-500">Telepon 2</span>
                                <div class="font-medium mt-0.5">{{ $detail->secondary_phone ?: '-' }}</div>
                            </div>
                            <div>
                                <span class="text-neutral-500">Alamat</span>
                                <div class="font-medium mt-0.5">{{ $detail->address_line ? Str::limit($detail->address_line, 80) : '-' }}</div>
                            </div>
                        </div>
                        <div class="pt-2">
                            <a href="{{ route('user.profile.edit') }}" class="inline-flex items-center text-xs px-3 py-1.5 rounded-md bg-neutral-700/40 hover:bg-neutral-600/40 transition border border-neutral-600 text-neutral-200">Perbarui</a>
                        </div>
                    @else
                        <p class="text-sm text-neutral-400">Belum ada detail kontak & alamat.</p>
                        <a href="{{ route('user.profile.edit') }}" class="inline-flex items-center text-xs px-3 py-1.5 rounded-md bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] text-black font-medium">Lengkapi Sekarang</a>
                    @endif
                </div>
            </section>

            <!-- KYC Section -->
            <section class="space-y-3">
                <h2 class="text-xs font-semibold tracking-wide text-neutral-400 uppercase">Verifikasi KYC</h2>
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4 space-y-3">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium mb-1">Status: <span class="text-{{ $k['color'] === 'slate' ? 'neutral' : $k['color'] }}-300">{{ $k['label'] }}</span></p>
                            <p class="text-[12px] text-neutral-400 leading-relaxed">{{ $k['text'] }}.</p>
                            @if($latestKycRequest && !$kyc)
                                <p class="text-[11px] text-neutral-500 mt-1">Permintaan terakhir: {{ $latestKycRequest->created_at->diffForHumans() }}</p>
                            @endif
                        </div>
                        @if(!$kyc)
                            <div class="flex flex-col gap-2">
                                @if(!$latestKycRequest || in_array($latestKycRequest->status,['rejected']))
                                    <a href="{{ route('user.kyc.requests.index') }}" class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-xs font-medium bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] text-black">Ajukan KYC</a>
                                @else
                                    <a href="{{ route('user.kyc.requests.show', $latestKycRequest) }}" class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-xs font-medium bg-neutral-700/40 text-neutral-200 border border-neutral-600 hover:bg-neutral-600/40">Lihat Permintaan</a>
                                @endif
                            </div>
                        @else
                            <a href="{{ route('user.kyc.show') }}" class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-xs font-medium bg-emerald-500/20 text-emerald-300 border border-emerald-600/40 hover:bg-emerald-500/25">Lihat Data</a>
                        @endif
                    </div>
                </div>
            </section>

            <!-- Security / Logout -->
            <section class="space-y-3">
                <h2 class="text-xs font-semibold tracking-wide text-neutral-400 uppercase">Keamanan</h2>
                <div class="rounded-xl overflow-hidden divide-y divide-neutral-800 border border-[#2c3136] bg-[#23272b]">
                    <a href="{{ route('profile.edit') }}#password" class="flex items-center justify-between px-4 py-3 hover:bg-[#272c31] transition">
                        <div>
                            <p class="text-sm font-medium">Password</p>
                            <p class="text-[11px] text-neutral-400">Ubah kata sandi akun.</p>
                        </div>
                        <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Keluar dari akun?');">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-between px-4 py-3 hover:bg-[#272c31] transition text-left">
                            <div>
                                <p class="text-sm font-medium">Keluar</p>
                                <p class="text-[11px] text-neutral-400">Akhiri sesi ini.</p>
                            </div>
                            <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </form>
                </div>
            </section>

            <div class="pt-2 pb-6 text-center text-[10px] text-neutral-500">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
