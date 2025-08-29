<x-admin-layout>
    <x-slot name="title">Buat Kode Undangan</x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Buat Kode Undangan</h2>
                <a href="{{ route('admin.invitation-codes.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="p-6">
            <form action="{{ route('admin.invitation-codes.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="max_usage" class="block text-sm font-medium text-gray-700 mb-2">
                            Maksimal Penggunaan <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="max_usage" 
                               name="max_usage" 
                               value="{{ old('max_usage', 1) }}" 
                               min="1" 
                               max="100" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('max_usage') border-red-300 @enderror"
                               required>
                        @error('max_usage')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-sm mt-1">Berapa kali kode ini bisa digunakan (1-100)</p>
                    </div>

                    <div>
                        <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Kadaluarsa
                        </label>
                        <input type="datetime-local" 
                               id="expires_at" 
                               name="expires_at" 
                               value="{{ old('expires_at') }}" 
                               min="{{ now()->format('Y-m-d\TH:i') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('expires_at') border-red-300 @enderror">
                        @error('expires_at')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-sm mt-1">Kosongkan jika tidak ingin ada batas waktu</p>
                    </div>
                </div>

                <!-- Preview Box -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Preview Kode</h3>
                    <div class="bg-white border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                        <div class="text-lg font-mono font-bold text-gray-400">KODE-XXXX</div>
                        <p class="text-sm text-gray-500 mt-1">Kode akan di-generate otomatis</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.invitation-codes.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Buat Kode
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
