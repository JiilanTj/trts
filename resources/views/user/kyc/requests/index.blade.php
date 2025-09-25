<x-app-layout>
    @php($user = auth()->user())
    @php(
        $latest = $user->kycRequests()->latest()->first()
    )
    @php(
        $statusMap = [
            'pending' => ['label' => 'Pending', 'classes' => 'bg-neutral-700/40 border-neutral-600 text-neutral-300'],
            'review' => ['label' => 'Review', 'classes' => 'bg-amber-500/15 border-amber-600/30 text-amber-300'],
            'approved' => ['label' => 'Approved', 'classes' => 'bg-emerald-500/15 border-emerald-600/30 text-emerald-300'],
            'rejected' => ['label' => 'Rejected', 'classes' => 'bg-rose-500/15 border-rose-600/30 text-rose-300'],
        ]
    )
    <div class="min-h-screen bg-[#1a1d21] text-neutral-100 pb-24">
        <div class="sticky top-0 z-30 backdrop-blur bg-[#1f2226]/95 border-b border-neutral-800/70 px-4 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-lg font-semibold">Pengajuan Verifikasi</h1>
                <a href="{{ route('user.profile.index') }}" class="text-xs text-neutral-400 hover:text-neutral-200">Kembali</a>
            </div>
        </div>

        <div class="px-4 py-6 space-y-8 max-w-xl mx-auto">
            {{-- Flash & Validation Messages --}}
            @if(session('success'))
                <div class="rounded-lg border border-emerald-600/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="rounded-lg border border-rose-600/40 bg-rose-500/10 px-4 py-3 text-xs text-rose-300 space-y-1">
                    @foreach($errors->all() as $err)
                        <p>• {{ $err }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Status Card --}}
            <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-medium">Status Saat Ini</h2>
                    @if($user->kyc)
                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-emerald-500/15 text-emerald-300 text-[11px] border border-emerald-600/30">Verified</span>
                    @elseif($latest)
                        @php($sm = $statusMap[$latest->status_kyc] ?? null)
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-[11px] border {{ $sm['classes'] ?? 'bg-neutral-700/40 border-neutral-600 text-neutral-300' }}">{{ $sm['label'] ?? ucfirst($latest->status_kyc) }}</span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-[11px] bg-neutral-700/40 border border-neutral-600 text-neutral-300">Belum</span>
                    @endif
                </div>
                @if($user->kyc)
                    <p class="text-sm text-emerald-400">Identitas sudah diverifikasi.</p>
                    <a href="{{ route('user.kyc.show') }}" class="inline-flex items-center mt-2 px-3 py-1.5 rounded-md bg-emerald-500/20 text-emerald-300 text-xs border border-emerald-600/40 hover:bg-emerald-500/25 transition">Lihat Data KYC</a>
                @elseif($latest)
                    <p class="text-sm">Pengajuan terakhir: <span class="font-medium capitalize">{{ $latest->status_kyc }}</span></p>
                    <p class="text-xs text-neutral-400">Dikirim {{ $latest->created_at->diffForHumans() }}</p>
                    <div class="flex gap-2 pt-2">
                        <a href="{{ route('user.kyc.requests.show',$latest) }}" class="inline-flex items-center px-3 py-1.5 rounded-md bg-neutral-700/40 text-neutral-200 text-xs border border-neutral-600 hover:bg-neutral-600/40 transition">Detail</a>
                        @if(in_array($latest->status_kyc,['rejected']))
                            <button x-data x-on:click="document.getElementById('kyc-form').scrollIntoView({behavior:'smooth'});" class="inline-flex items-center px-3 py-1.5 rounded-md bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] text-black text-xs font-medium shadow-sm">Ajukan Ulang</button>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-neutral-300">Belum ada pengajuan Verifikasi.</p>
                @endif
            </div>

            {{-- Form Card --}}
            <div id="kyc-form" class="rounded-xl border border-[#2c3136] bg-[#23272b] p-5 space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-medium">Formulir Pengajuan</h2>
                    @php($locked = $user->kyc || ($latest && in_array($latest->status_kyc,['pending','review'])))
                    @if($locked)
                        <span class="text-[11px] px-2 py-1 rounded-md bg-neutral-700/40 border border-neutral-600 text-neutral-400">Terkunci</span>
                    @endif
                </div>
                @if($locked)
                    <p class="text-xs text-neutral-400 leading-relaxed">Sedang ada pengajuan berjalan atau Anda sudah terverifikasi. Tunggu proses selesai sebelum membuat pengajuan baru.</p>
                @else
                    <form action="{{ route('user.kyc.requests.store') }}" method="POST" enctype="multipart/form-data" class="space-y-7">
                        @csrf

                        {{-- Data Pribadi --}}
                        <div class="space-y-4">
                            <h3 class="text-[11px] font-semibold tracking-wide text-neutral-400 uppercase">Data Pribadi</h3>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-[11px] font-medium tracking-wide text-neutral-400 mb-1">Nama Lengkap <span class="text-rose-400">*</span></label>
                                    <input required name="full_name" value="{{ old('full_name',$user->full_name) }}" placeholder="Nama sesuai identitas" class="w-full rounded-lg bg-[#1f2226] border border-neutral-700/70 px-3 py-2 text-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500/50 placeholder:text-neutral-500" />
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[11px] font-medium tracking-wide text-neutral-400 mb-1">NIK</label>
                                        <input name="nik" value="{{ old('nik') }}" placeholder="Nomor KTP" class="w-full rounded-lg bg-[#1f2226] border border-neutral-700/70 px-3 py-2 text-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500/50 placeholder:text-neutral-500" />
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-medium tracking-wide text-neutral-400 mb-1">Tempat Lahir</label>
                                        <input name="birth_place" value="{{ old('birth_place') }}" placeholder="Kota" class="w-full rounded-lg bg-[#1f2226] border border-neutral-700/70 px-3 py-2 text-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500/50 placeholder:text-neutral-500" />
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[11px] font-medium tracking-wide text-neutral-400 mb-1">Tanggal Lahir</label>
                                        <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full rounded-lg bg-[#1f2226] border border-neutral-700/70 px-3 py-2 text-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500/50" />
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-medium tracking-wide text-neutral-400 mb-1">Pekerjaan</label>
                                        <input name="occupation" value="{{ old('occupation') }}" placeholder="Pekerjaan" class="w-full rounded-lg bg-[#1f2226] border border-neutral-700/70 px-3 py-2 text-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500/50 placeholder:text-neutral-500" />
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-medium tracking-wide text-neutral-400 mb-1">Alamat</label>
                                    <textarea name="address" rows="2" placeholder="Alamat lengkap" class="w-full rounded-lg bg-[#1f2226] border border-neutral-700/70 px-3 py-2 text-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500/50 placeholder:text-neutral-500">{{ old('address') }}</textarea>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-[11px] font-medium tracking-wide text-neutral-400 mb-1">RT/RW</label>
                                        <input name="rt_rw" value="{{ old('rt_rw') }}" placeholder="001/002" class="w-full rounded-lg bg-[#1f2226] border border-neutral-700/70 px-3 py-2 text-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500/50 placeholder:text-neutral-500" />
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-medium tracking-wide text-neutral-400 mb-1">Desa</label>
                                        <input name="village" value="{{ old('village') }}" placeholder="Desa" class="w-full rounded-lg bg-[#1f2226] border border-neutral-700/70 px-3 py-2 text-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500/50 placeholder:text-neutral-500" />
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-medium tracking-wide text-neutral-400 mb-1">Kecamatan</label>
                                        <input name="district" value="{{ old('district') }}" placeholder="Kecamatan" class="w-full rounded-lg bg-[#1f2226] border border-neutral-700/70 px-3 py-2 text-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500/50 placeholder:text-neutral-500" />
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-[11px] font-medium tracking-wide text-neutral-400 mb-1">Agama</label>
                                        <input name="religion" value="{{ old('religion') }}" placeholder="Agama" class="w-full rounded-lg bg-[#1f2226] border border-neutral-700/70 px-3 py-2 text-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500/50 placeholder:text-neutral-500" />
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-medium tracking-wide text-neutral-400 mb-1">Status Nikah</label>
                                        <input name="marital_status" value="{{ old('marital_status') }}" placeholder="Status" class="w-full rounded-lg bg-[#1f2226] border border-neutral-700/70 px-3 py-2 text-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500/50 placeholder:text-neutral-500" />
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-medium tracking-wide text-neutral-400 mb-1">Warga Negara</label>
                                        <input name="nationality" value="{{ old('nationality') }}" placeholder="Indonesia" class="w-full rounded-lg bg-[#1f2226] border border-neutral-700/70 px-3 py-2 text-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500/50 placeholder:text-neutral-500" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Dokumen (Vertical + Preview) --}}
                        <div class="space-y-4">
                            <h3 class="text-[11px] font-semibold tracking-wide text-neutral-400 uppercase">Dokumen</h3>
                            <p class="text-[11px] text-neutral-500 -mt-2">Format: JPG / JPEG / PNG / (PDF hanya ditampilkan sebagai nama file).</p>
                            <div class="space-y-5 text-[11px]">
                                {{-- KTP Depan --}}
                                <div class="space-y-2">
                                    <label class="block font-medium text-neutral-300">KTP Depan <span class="text-rose-400">*</span></label>
                                    <input required accept="image/*,application/pdf" type="file" name="ktp_front" data-preview-target="preview-ktp_front" class="w-full text-[11px] text-neutral-300 file:mr-2 file:rounded-md file:border-0 file:bg-neutral-700 file:px-3 file:py-2 file:text-[11px] file:font-medium file:text-neutral-100 hover:file:bg-neutral-600 cursor-pointer" />
                                    <div id="preview-ktp_front" class="hidden">
                                        <div class="mt-2 border border-neutral-700/70 rounded-lg overflow-hidden bg-[#1f2226] p-2 flex items-start gap-3">
                                            <img class="hidden w-28 h-20 object-cover rounded-md ring-1 ring-neutral-700/60" alt="Preview KTP Depan" />
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[11px] font-medium text-neutral-300 file-name truncate"></p>
                                                <p class="text-[10px] text-neutral-500 kind"></p>
                                                <button type="button" class="mt-2 inline-flex items-center px-2 py-1 rounded-md bg-neutral-700/40 hover:bg-neutral-600/40 text-[10px] text-neutral-300 reset-btn">Ganti</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- KTP Belakang --}}
                                <div class="space-y-2">
                                    <label class="block font-medium text-neutral-300">KTP Belakang <span class="text-rose-400">*</span></label>
                                    <input required accept="image/*,application/pdf" type="file" name="ktp_back" data-preview-target="preview-ktp_back" class="w-full text-[11px] text-neutral-300 file:mr-2 file:rounded-md file:border-0 file:bg-neutral-700 file:px-3 file:py-2 file:text-[11px] file:font-medium file:text-neutral-100 hover:file:bg-neutral-600 cursor-pointer" />
                                    <div id="preview-ktp_back" class="hidden">
                                        <div class="mt-2 border border-neutral-700/70 rounded-lg overflow-hidden bg-[#1f2226] p-2 flex items-start gap-3">
                                            <img class="hidden w-28 h-20 object-cover rounded-md ring-1 ring-neutral-700/60" alt="Preview KTP Belakang" />
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[11px] font-medium text-neutral-300 file-name truncate"></p>
                                                <p class="text-[10px] text-neutral-500 kind"></p>
                                                <button type="button" class="mt-2 inline-flex items-center px-2 py-1 rounded-md bg-neutral-700/40 hover:bg-neutral-600/40 text-[10px] text-neutral-300 reset-btn">Ganti</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Selfie + KTP --}}
                                <div class="space-y-2">
                                    <label class="block font-medium text-neutral-300">Selfie + KTP <span class="text-rose-400">*</span></label>
                                    <input required accept="image/*,application/pdf" type="file" name="selfie_ktp" data-preview-target="preview-selfie_ktp" class="w-full text-[11px] text-neutral-300 file:mr-2 file:rounded-md file:border-0 file:bg-neutral-700 file:px-3 file:py-2 file:text-[11px] file:font-medium file:text-neutral-100 hover:file:bg-neutral-600 cursor-pointer" />
                                    <div id="preview-selfie_ktp" class="hidden">
                                        <div class="mt-2 border border-neutral-700/70 rounded-lg overflow-hidden bg-[#1f2226] p-2 flex items-start gap-3">
                                            <img class="hidden w-28 h-20 object-cover rounded-md ring-1 ring-neutral-700/60" alt="Preview Selfie KTP" />
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[11px] font-medium text-neutral-300 file-name truncate"></p>
                                                <p class="text-[10px] text-neutral-500 kind"></p>
                                                <button type="button" class="mt-2 inline-flex items-center px-2 py-1 rounded-md bg-neutral-700/40 hover:bg-neutral-600/40 text-[10px] text-neutral-300 reset-btn">Ganti</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button class="inline-flex items-center px-5 py-2.5 rounded-md text-sm font-medium bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] text-black shadow hover:opacity-90 active:opacity-80 transition focus:outline-none focus:ring-2 focus:ring-offset-0 focus:ring-[#25F4EE]/40">Kirim Pengajuan</button>
                        </div>
                    </form>

                    <script>
                        (function(){
                            const inputs = document.querySelectorAll('input[type=file][data-preview-target]');
                            
                            inputs.forEach(inp => {
                                inp.addEventListener('change', () => {
                                    const file = inp.files && inp.files[0];
                                    const targetId = inp.getAttribute('data-preview-target');
                                    const wrap = document.getElementById(targetId);
                                    if(!wrap) return;
                                    const img = wrap.querySelector('img');
                                    const nameEl = wrap.querySelector('.file-name');
                                    const kindEl = wrap.querySelector('.kind');
                                    
                                    if(!file){
                                        wrap.classList.add('hidden');
                                        return;
                                    }
                                    
                                    nameEl.textContent = file.name;
                                    kindEl.textContent = file.type || 'File';
                                    
                                    // Show preview only for image/*
                                    if(file.type.startsWith('image/')){
                                        img.src = URL.createObjectURL(file);
                                        img.classList.remove('hidden');
                                    } else {
                                        img.classList.add('hidden');
                                        img.removeAttribute('src');
                                    }
                                    wrap.classList.remove('hidden');
                                    
                                    // Reset button
                                    const resetBtn = wrap.querySelector('.reset-btn');
                                    if(resetBtn){
                                        resetBtn.onclick = () => {
                                            inp.value = '';
                                            wrap.classList.add('hidden');
                                            if(img){ img.classList.add('hidden'); img.removeAttribute('src'); }
                                        };
                                    }
                                });
                            });
                        })();
                    </script>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
