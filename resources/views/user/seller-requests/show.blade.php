<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
        <!-- Header / Toolbar -->
        <div class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
            <div class="px-4 py-3 flex items-center gap-3">
                <a href="{{ route('seller-requests.index') }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200 text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition" aria-label="Kembali">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-semibold text-gray-900 leading-tight">Detail Permintaan Seller</h1>
                    <p class="text-[11px] text-gray-500 mt-0.5">Detail pengajuan menjadi seller.</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 py-6">
            <div class="max-w-2xl mx-auto">
                <!-- Status Card -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pengajuan</h2>
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-gray-600 w-24">Status:</span>
                                    @if($sellerRequest->status === 'pending')
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                            Menunggu Review
                                        </span>
                                    @elseif($sellerRequest->status === 'approved')
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Disetujui
                                        </span>
                                    @else
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                            Ditolak
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-gray-600 w-24">Nama Toko:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $sellerRequest->store_name }}</span>
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-gray-600 w-24">Kode Undangan:</span>
                                    <span class="text-sm font-mono bg-gray-100 px-2 py-1 rounded">{{ $sellerRequest->invite_code }}</span>
                                </div>
                                
                                <div class="flex items-start gap-3">
                                    <span class="text-sm text-gray-600 w-24">Deskripsi:</span>
                                    <span class="text-sm text-gray-900 flex-1">{{ $sellerRequest->description ?: 'Tidak ada deskripsi' }}</span>
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-gray-600 w-24">Tanggal:</span>
                                    <span class="text-sm text-gray-900">{{ $sellerRequest->created_at->format('d M Y H:i') }}</span>
                                </div>
                                
                                @if($sellerRequest->admin_notes)
                                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                        <p class="text-xs font-medium text-gray-700 mb-1">Catatan Admin:</p>
                                        <p class="text-sm text-gray-600">{{ $sellerRequest->admin_notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="shrink-0">
                            @if($sellerRequest->status === 'pending')
                                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @elseif($sellerRequest->status === 'approved')
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                @if($sellerRequest->status === 'rejected')
                    <div class="bg-white border border-gray-200 rounded-2xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Ajukan Ulang</h3>
                        <p class="text-sm text-gray-600 mb-4">Pengajuan Anda ditolak. Anda dapat mengajukan ulang dengan memperbaiki data yang diperlukan.</p>
                        <a href="{{ route('seller-requests.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-2.836A3 3 0 008.25 4.5c0-1.41.6-2.68 1.56-3.62a3.001 3.001 0 011.94.6 3 3 0 013.7 2.75c0 1.41.6 2.68 1.56 3.62.75.75 1.92.75 2.67 0 .96-.94 1.56-2.21 1.56-3.62a3 3 0 013.75-2.836A3.001 3.001 0 0116.5 2.85M8.25 9.75h7.5" />
                            </svg>
                            Ajukan Ulang
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
