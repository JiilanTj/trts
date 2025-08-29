<x-admin-layout>
    <x-slot name="title">Pengaturan Website</x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">Pengaturan Website</h1>
                <p class="text-sm text-gray-500 mt-1">Informasi dasar platform & metode pembayaran.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="p-6 grid md:grid-cols-3 gap-8">
            <div class="md:col-span-2 space-y-8">
                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide">Nama Bank / E-Wallet</label>
                            <input type="text" name="payment_provider" value="{{ old('payment_provider', $setting?->payment_provider) }}" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="BCA / BRI / OVO / DANA">
                            @error('payment_provider')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide">Atas Nama</label>
                            <input type="text" name="account_name" value="{{ old('account_name', $setting?->account_name) }}" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nama Pemilik Rekening">
                            @error('account_name')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide">Nomor Rekening / E-Wallet</label>
                            <input type="text" name="account_number" value="{{ old('account_number', $setting?->account_number) }}" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="1234567890">
                            @error('account_number')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide">Logo Website</label>
                            <input type="file" name="logo" accept="image/*" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('logo')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                            @if($setting?->logo_url)
                                <div class="mt-3 flex items-center gap-3">
                                    <img src="{{ $setting->logo_url }}" alt="Logo" class="h-14 w-14 object-cover rounded-lg border">
                                    <p class="text-[11px] text-gray-500">Logo saat ini</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="pt-2">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
            <div class="space-y-6">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                    <h2 class="text-sm font-semibold text-gray-800 mb-3">Informasi</h2>
                    <p class="text-xs text-gray-600 leading-relaxed">Halaman ini digunakan untuk memperbarui informasi dasar platform yang ditampilkan ke pengguna, termasuk identitas pembayaran dan logo.</p>
                    <ul class="mt-4 space-y-2 text-xs text-gray-600">
                        <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 mt-1.5 rounded-full bg-blue-500"></span>Pastikan data sesuai agar proses verifikasi transfer mudah.</li>
                        <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 mt-1.5 rounded-full bg-blue-500"></span>Logo disarankan rasio persegi (PNG transparan).</li>
                        <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 mt-1.5 rounded-full bg-blue-500"></span>Hanya satu baris pengaturan yang digunakan.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
