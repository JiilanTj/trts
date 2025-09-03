<x-app-layout>
    @php($user = auth()->user())
    @php(
        $frontUrl = $kycRequest->ktp_front_url ?? ($kycRequest->ktp_front ? \Illuminate\Support\Facades\Storage::url($kycRequest->ktp_front) : null)
    )
    @php(
        $backUrl = $kycRequest->ktp_back_url ?? ($kycRequest->ktp_back ? \Illuminate\Support\Facades\Storage::url($kycRequest->ktp_back) : null)
    )
    @php(
        $selfieUrl = $kycRequest->selfie_ktp_url ?? ($kycRequest->selfie_ktp ? \Illuminate\Support\Facades\Storage::url($kycRequest->selfie_ktp) : null)
    )
    @php(
        $isImg = function($url){ if(!$url) return false; $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION)); return in_array($ext,['jpg','jpeg','png','webp','gif']); }
    )
    <div class="min-h-screen bg-[#1a1d21] text-neutral-100 pb-24">
        <div class="sticky top-0 z-30 backdrop-blur bg-[#1f2226]/95 border-b border-neutral-800/70 px-4 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-lg font-semibold">Detail Pengajuan</h1>
                <a href="{{ route('user.kyc.requests.index') }}" class="text-xs text-neutral-400 hover:text-neutral-200">Kembali</a>
            </div>
        </div>
        <div class="px-4 py-6 space-y-6 max-w-xl mx-auto">
            <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4 space-y-4">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium">Status</p>
                    <span class="text-xs px-2 py-1 rounded-md bg-neutral-700/40 border border-neutral-600 capitalize">{{ $kycRequest->status_kyc }}</span>
                </div>
                <div class="grid grid-cols-2 gap-4 text-[13px]">
                    <div>
                        <p class="text-neutral-500 text-[11px] uppercase tracking-wide">Nama</p>
                        <p class="font-medium mt-0.5">{{ $kycRequest->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-neutral-500 text-[11px] uppercase tracking-wide">NIK</p>
                        <p class="font-medium mt-0.5">{{ $kycRequest->nik ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-neutral-500 text-[11px] uppercase tracking-wide">Tempat / Tgl Lahir</p>
                        <p class="font-medium mt-0.5">{{ $kycRequest->birth_place ?? '-' }}, {{ $kycRequest->birth_date ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-neutral-500 text-[11px] uppercase tracking-wide">Pekerjaan</p>
                        <p class="font-medium mt-0.5">{{ $kycRequest->occupation ?? '-' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-neutral-500 text-[11px] uppercase tracking-wide">Alamat</p>
                        <p class="font-medium mt-0.5">{{ $kycRequest->address ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-neutral-500 text-[11px] uppercase tracking-wide">Desa</p>
                        <p class="font-medium mt-0.5">{{ $kycRequest->village ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-neutral-500 text-[11px] uppercase tracking-wide">Kecamatan</p>
                        <p class="font-medium mt-0.5">{{ $kycRequest->district ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-neutral-500 text-[11px] uppercase tracking-wide">Agama</p>
                        <p class="font-medium mt-0.5">{{ $kycRequest->religion ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-neutral-500 text-[11px] uppercase tracking-wide">Status</p>
                        <p class="font-medium mt-0.5">{{ $kycRequest->marital_status ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-neutral-500 text-[11px] uppercase tracking-wide">Warga Negara</p>
                        <p class="font-medium mt-0.5">{{ $kycRequest->nationality ?? '-' }}</p>
                    </div>
                </div>
                @if($kycRequest->admin_notes)
                    <div class="pt-2 border-t border-neutral-700">
                        <p class="text-[11px] uppercase tracking-wide text-neutral-500">Catatan Admin</p>
                        <p class="text-sm mt-1 text-neutral-300">{{ $kycRequest->admin_notes }}</p>
                    </div>
                @endif
            </div>

            <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-medium">Dokumen</h2>
                    <p class="text-[10px] text-neutral-500">Klik untuk buka ukuran penuh</p>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    {{-- KTP Depan --}}
                    <div class="space-y-2">
                        <p class="text-[11px] text-neutral-400 text-center">KTP Depan</p>
                        @if($frontUrl)
                            @if($isImg($frontUrl))
                                <a href="{{ $frontUrl }}" target="_blank" class="group block relative rounded-md overflow-hidden bg-neutral-800 border border-neutral-700 hover:border-neutral-500 focus:outline-none">
                                    <img src="{{ $frontUrl }}" alt="KTP Depan" class="w-full h-full object-cover aspect-[3/2] group-hover:opacity-95" />
                                </a>
                            @else
                                <a href="{{ $frontUrl }}" target="_blank" class="flex flex-col items-center justify-center aspect-[3/2] rounded-md bg-neutral-800 border border-neutral-700 text-[10px] hover:border-neutral-500">
                                    <span class="text-neutral-300 font-medium">PDF</span>
                                    <span class="text-neutral-500 mt-0.5">KTP Depan</span>
                                </a>
                            @endif
                        @else
                            <div class="aspect-[3/2] rounded-md bg-neutral-800/40 border border-dashed border-neutral-700 flex items-center justify-center text-[10px] text-neutral-500">Tidak Ada</div>
                        @endif
                    </div>

                    {{-- KTP Belakang --}}
                    <div class="space-y-2">
                        <p class="text-[11px] text-neutral-400 text-center">KTP Belakang</p>
                        @if($backUrl)
                            @if($isImg($backUrl))
                                <a href="{{ $backUrl }}" target="_blank" class="group block relative rounded-md overflow-hidden bg-neutral-800 border border-neutral-700 hover:border-neutral-500 focus:outline-none">
                                    <img src="{{ $backUrl }}" alt="KTP Belakang" class="w-full h-full object-cover aspect-[3/2] group-hover:opacity-95" />
                                </a>
                            @else
                                <a href="{{ $backUrl }}" target="_blank" class="flex flex-col items-center justify-center aspect-[3/2] rounded-md bg-neutral-800 border border-neutral-700 text-[10px] hover:border-neutral-500">
                                    <span class="text-neutral-300 font-medium">PDF</span>
                                    <span class="text-neutral-500 mt-0.5">KTP Belakang</span>
                                </a>
                            @endif
                        @else
                            <div class="aspect-[3/2] rounded-md bg-neutral-800/40 border border-dashed border-neutral-700 flex items-center justify-center text-[10px] text-neutral-500">Tidak Ada</div>
                        @endif
                    </div>

                    {{-- Selfie + KTP --}}
                    <div class="space-y-2">
                        <p class="text-[11px] text-neutral-400 text-center">Selfie</p>
                        @if($selfieUrl)
                            @if($isImg($selfieUrl))
                                <a href="{{ $selfieUrl }}" target="_blank" class="group block relative rounded-md overflow-hidden bg-neutral-800 border border-neutral-700 hover:border-neutral-500 focus:outline-none">
                                    <img src="{{ $selfieUrl }}" alt="Selfie KTP" class="w-full h-full object-cover aspect-[3/2] group-hover:opacity-95" />
                                </a>
                            @else
                                <a href="{{ $selfieUrl }}" target="_blank" class="flex flex-col items-center justify-center aspect-[3/2] rounded-md bg-neutral-800 border border-neutral-700 text-[10px] hover:border-neutral-500">
                                    <span class="text-neutral-300 font-medium">PDF</span>
                                    <span class="text-neutral-500 mt-0.5">Selfie</span>
                                </a>
                            @endif
                        @else
                            <div class="aspect-[3/2] rounded-md bg-neutral-800/40 border border-dashed border-neutral-700 flex items-center justify-center text-[10px] text-neutral-500">Tidak Ada</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
