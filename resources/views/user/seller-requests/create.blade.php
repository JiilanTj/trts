<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
        <!-- Header / Toolbar -->
        <div class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
            <div class="px-4 py-3 flex items-center gap-3">
                <a href="{{ route('seller-requests.index') }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200 text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition" aria-label="Kembali">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-semibold text-gray-900 leading-tight">Ajukan Menjadi Seller</h1>
                    <p class="text-[11px] text-gray-500 mt-0.5">Isi formulir untuk mengajukan diri menjadi seller.</p>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if($errors->any())
            <div class="px-4 pt-4">
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Form -->
        <div class="px-4 py-6">
            <form method="POST" action="{{ route('seller-requests.store') }}" class="max-w-2xl mx-auto">
                @csrf
                
                <div class="bg-white border border-gray-200 rounded-2xl p-6 space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Informasi Toko</h2>
                        <p class="text-sm text-gray-600">Lengkapi data toko yang akan Anda buat.</p>
                    </div>

                    <!-- Store Name -->
                    <div>
                        <label for="store_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Toko <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="store_name" 
                               name="store_name" 
                               value="{{ old('store_name') }}"
                               placeholder="Masukkan nama toko Anda"
                               required
                               class="w-full px-4 py-3 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 {{ $errors->has('store_name') ? 'border-red-300 bg-red-50' : 'bg-gray-50 hover:bg-white' }} transition">
                        @error('store_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Invitation Code -->
                    <div>
                        <label for="invite_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Kode Undangan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="invite_code" 
                               name="invite_code" 
                               value="{{ old('invite_code') }}"
                               placeholder="Masukkan kode undangan yang valid"
                               required
                               class="w-full px-4 py-3 text-sm font-mono border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 {{ $errors->has('invite_code') ? 'border-red-300 bg-red-50' : 'bg-gray-50 hover:bg-white' }} transition">
                        @error('invite_code')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Dapatkan kode undangan dari admin atau seller lain.</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi Toko
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  placeholder="Ceritakan tentang toko yang akan Anda buat..."
                                  class="w-full px-4 py-3 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 {{ $errors->has('description') ? 'border-red-300 bg-red-50' : 'bg-gray-50 hover:bg-white' }} transition resize-none">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Opsional. Jelaskan jenis produk yang akan dijual.</p>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="flex gap-3">
                            <div class="shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-blue-900 mb-1">Informasi Penting</h4>
                                <ul class="text-xs text-blue-700 space-y-1">
                                    <li>• Pengajuan akan direview oleh admin dalam 1-3 hari kerja</li>
                                    <li>• Pastikan kode undangan yang Anda masukkan valid dan aktif</li>
                                    <li>• Setelah disetujui, Anda dapat mulai mengelola toko</li>
                                    <li>• Nama toko harus unik dan belum digunakan</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 pt-4">
                        <button type="submit" 
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-xl text-sm shadow-sm transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Kirim Pengajuan
                        </button>
                        <a href="{{ route('seller-requests.index') }}" 
                           class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl text-sm transition">
                            Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
