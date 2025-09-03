<x-app-layout>
    @php($user = auth()->user())
    <div class="min-h-screen bg-[#1a1d21] text-neutral-100 pb-24">
        <div class="sticky top-0 z-30 backdrop-blur bg-[#1f2226]/95 border-b border-neutral-800/70 px-4 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-lg font-semibold">Data KYC Terverifikasi</h1>
                <a href="{{ route('user.profile.index') }}" class="text-xs text-neutral-400 hover:text-neutral-200">Kembali</a>
            </div>
        </div>
        <div class="px-4 py-6 space-y-6 max-w-xl mx-auto">
            @if(!$kyc)
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-6 text-center space-y-3">
                    <p class="text-sm text-neutral-300">Belum ada data KYC terverifikasi.</p>
                    <a href="{{ route('user.kyc.requests.index') }}" class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] text-black">Ajukan KYC</a>
                </div>
            @else
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium">Status</p>
                            <p class="text-xs text-neutral-400 mt-0.5">Terverifikasi {{ $kyc->verified_at?->diffForHumans() }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-emerald-500/20 text-emerald-300 text-xs border border-emerald-600/40">Verified</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-[13px]">
                        <div>
                            <p class="text-neutral-500 text-[11px] uppercase tracking-wide">Nama</p>
                            <p class="font-medium mt-0.5">{{ $kyc->full_name }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-500 text-[11px] uppercase tracking-wide">NIK</p>
                            <p class="font-medium mt-0.5">{{ $kyc->nik ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-500 text-[11px] uppercase tracking-wide">Tempat / Tgl Lahir</p>
                            <p class="font-medium mt-0.5">{{ $kyc->birth_place ?? '-' }}, {{ $kyc->birth_date ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-500 text-[11px] uppercase tracking-wide">Pekerjaan</p>
                            <p class="font-medium mt-0.5">{{ $kyc->occupation ?? '-' }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-neutral-500 text-[11px] uppercase tracking-wide">Alamat</p>
                            <p class="font-medium mt-0.5">{{ $kyc->address ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-500 text-[11px] uppercase tracking-wide">Desa</p>
                            <p class="font-medium mt-0.5">{{ $kyc->village ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-500 text-[11px] uppercase tracking-wide">Kecamatan</p>
                            <p class="font-medium mt-0.5">{{ $kyc->district ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-500 text-[11px] uppercase tracking-wide">Agama</p>
                            <p class="font-medium mt-0.5">{{ $kyc->religion ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-500 text-[11px] uppercase tracking-wide">Status</p>
                            <p class="font-medium mt-0.5">{{ $kyc->marital_status ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-500 text-[11px] uppercase tracking-wide">Warga Negara</p>
                            <p class="font-medium mt-0.5">{{ $kyc->nationality ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4 space-y-4">
                    <h2 class="text-sm font-medium">Dokumen</h2>
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="space-y-2">
                            <p class="text-[11px] text-neutral-400">KTP Depan</p>
                            <a target="_blank" href="{{ $kyc->ktp_front_path ? Storage::url($kyc->ktp_front_path) : '#' }}" class="flex w-full aspect-[3/2] rounded-md bg-neutral-800 border border-neutral-700 text-[10px] items-center justify-center hover:border-neutral-500">Lihat</a>
                        </div>
                        <div class="space-y-2">
                            <p class="text-[11px] text-neutral-400">KTP Belakang</p>
                            <a target="_blank" href="{{ $kyc->ktp_back_path ? Storage::url($kyc->ktp_back_path) : '#' }}" class="flex w-full aspect-[3/2] rounded-md bg-neutral-800 border border-neutral-700 text-[10px] items-center justify-center hover:border-neutral-500">Lihat</a>
                        </div>
                        <div class="space-y-2">
                            <p class="text-[11px] text-neutral-400">Selfie</p>
                            <a target="_blank" href="{{ $kyc->selfie_ktp_path ? Storage::url($kyc->selfie_ktp_path) : '#' }}" class="flex w-full aspect-[3/2] rounded-md bg-neutral-800 border border-neutral-700 text-[10px] items-center justify-center hover:border-neutral-500">Lihat</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
