<x-app-layout>
    @php($user = auth()->user()->load('detail'))
    <div class="min-h-screen bg-[#1a1d21] text-neutral-100 pb-24">
        <div class="sticky top-0 z-30 backdrop-blur bg-[#1f2226]/95 border-b border-neutral-800/70 px-4 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-lg font-semibold">Edit Profil</h1>
                <a href="{{ route('user.profile.index') }}" class="text-xs text-neutral-400 hover:text-neutral-200">Kembali</a>
            </div>
        </div>

        <div class="px-4 py-6 space-y-8 max-w-xl mx-auto">
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

            <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-10">
                @csrf
                @method('PUT')

                {{-- Data Akun Utama --}}
                <div class="space-y-4">
                    <h2 class="text-sm font-semibold tracking-wide text-neutral-300">Data Akun</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-medium tracking-wide text-neutral-400 mb-1">Nama Lengkap</label>
                            <input name="full_name" value="{{ old('full_name',$user->full_name) }}" class="w-full rounded-lg bg-[#1f2226] border border-neutral-700/70 px-3 py-2 text-sm focus:border-neutral-500 focus:ring-1 focus:ring-neutral-500/50" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium tracking-wide text-neutral-400 mb-1">Username</label>
                            <input name="username" value="{{ old('username',$user->username) }}" class="w-full rounded-lg bg-[#1f2226] border border-neutral-700/70 px-3 py-2 text-sm focus:border-neutral-500 focus:ring-1 focus:ring-neutral-500/50" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium tracking-wide text-neutral-400 mb-1">Foto Profil</label>
                            <input type="file" name="photo" accept="image/*" class="w-full text-[11px] text-neutral-300 file:mr-2 file:rounded-md file:border-0 file:bg-neutral-700 file:px-3 file:py-2 file:text-[11px] file:font-medium file:text-neutral-100 hover:file:bg-neutral-600 cursor-pointer" />
                            @if($user->photo_url)
                                <img src="{{ $user->photo_url }}" class="mt-3 w-20 h-20 object-cover rounded-full ring-2 ring-neutral-700" />
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Data Profil (Disederhanakan) --}}
                <div class="space-y-4">
                    <h2 class="text-sm font-semibold tracking-wide text-neutral-300">Kontak & Alamat</h2>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-medium tracking-wide text-neutral-400 mb-1">Telepon</label>
                                <input name="phone" value="{{ old('phone', optional($user->detail)->phone) }}" class="w-full rounded-lg bg-[#1f2226] border border-neutral-700/70 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-medium tracking-wide text-neutral-400 mb-1">Telepon 2</label>
                                <input name="secondary_phone" value="{{ old('secondary_phone', optional($user->detail)->secondary_phone) }}" class="w-full rounded-lg bg-[#1f2226] border border-neutral-700/70 px-3 py-2 text-sm" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium tracking-wide text-neutral-400 mb-1">Alamat Lengkap</label>
                            <textarea name="address_line" rows="2" class="w-full rounded-lg bg-[#1f2226] border border-neutral-700/70 px-3 py-2 text-sm">{{ old('address_line', optional($user->detail)->address_line) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button class="inline-flex items-center px-5 py-2.5 rounded-md text-sm font-medium bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] text-black shadow hover:opacity-90 active:opacity-80 transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
