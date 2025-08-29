<x-admin-layout>
    <x-slot name="title">Detail Permintaan Seller</x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Detail Permintaan Seller</h2>
                <a href="{{ route('admin.seller-requests.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Pesan Sukses / Error -->
        @if(session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Content -->
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Informasi User -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi User</h3>
                    <div class="flex items-center mb-4">
                        @if($sellerRequest->user->photo)
                            <img class="h-16 w-16 rounded-full object-cover" src="{{ $sellerRequest->user->photo_url }}" alt="{{ $sellerRequest->user->full_name }}">
                        @else
                            <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center">
                                <span class="text-xl font-medium text-gray-700">{{ substr($sellerRequest->user->full_name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="ml-4">
                            <div class="text-lg font-medium text-gray-900">{{ $sellerRequest->user->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ $sellerRequest->user->username }}</div>
                            <div class="text-sm text-gray-500">Bergabung: {{ $sellerRequest->user->created_at->format('d/m/Y') }}</div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Level:</span>
                            <span class="font-medium">{{ $sellerRequest->user->level }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Saldo:</span>
                            <span class="font-medium">Rp {{ number_format($sellerRequest->user->balance, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Role:</span>
                            <span class="font-medium">{{ ucfirst($sellerRequest->user->role) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Informasi Permintaan -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Permintaan</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Toko</label>
                            <div class="mt-1 text-lg font-medium text-gray-900">{{ $sellerRequest->store_name }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kode Undangan</label>
                            <div class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $sellerRequest->invite_code }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <div class="mt-1">
                                @if($sellerRequest->status === 'pending')
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Menunggu Review
                                    </span>
                                @elseif($sellerRequest->status === 'approved')
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                        Ditolak
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Pengajuan</label>
                            <div class="mt-1 text-gray-900">{{ $sellerRequest->created_at->format('d F Y, H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            @if($sellerRequest->description)
                <div class="mt-6 bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Deskripsi Toko</h3>
                    <p class="text-gray-700 leading-relaxed">{{ $sellerRequest->description }}</p>
                </div>
            @endif

            <!-- Admin Note -->
            @if($sellerRequest->admin_note)
                <div class="mt-6 bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Catatan Admin</h3>
                    <p class="text-gray-700 leading-relaxed">{{ $sellerRequest->admin_note }}</p>
                </div>
            @endif

            <!-- Action Buttons -->
            @if($sellerRequest->isPending())
                <div class="mt-6 bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tindakan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Approve Form -->
                        <form action="{{ route('admin.seller-requests.approve', $sellerRequest) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="approve_note" class="block text-sm font-medium text-gray-700">Catatan Persetujuan (Opsional)</label>
                                <textarea id="approve_note" name="admin_note" rows="3" 
                                    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    placeholder="Tambahkan catatan untuk persetujuan ini..."></textarea>
                            </div>
                            <button type="submit" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                                onclick="return confirm('Yakin ingin menyetujui permintaan ini? User akan langsung menjadi seller.')">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Setujui Permintaan
                            </button>
                        </form>

                        <!-- Reject Form -->
                        <form action="{{ route('admin.seller-requests.reject', $sellerRequest) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="reject_note" class="block text-sm font-medium text-gray-700">Alasan Penolakan <span class="text-red-500">*</span></label>
                                <textarea id="reject_note" name="admin_note" rows="3" 
                                    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                    placeholder="Jelaskan alasan penolakan..." required></textarea>
                            </div>
                            <button type="submit" 
                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                                onclick="return confirm('Yakin ingin menolak permintaan ini?')">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Tolak Permintaan
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
