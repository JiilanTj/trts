@php /** @var \App\Models\TopupRequest $topupRequest */ @endphp
<x-admin-layout>
    <x-slot name="title">Detail Topup #{{ $topupRequest->id }}</x-slot>

    @php
        $statusColors = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
        ];
        $statusLabels = [
            'pending' => 'Pending',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];
    @endphp

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Detail Permintaan Topup #{{ $topupRequest->id }}</h2>
                    <p class="text-sm text-gray-500">Dibuat {{ $topupRequest->created_at->format('d M Y H:i') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.topup.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Kembali ke Topup
                    </a>
                    @if($topupRequest->isPending())
                        <button type="button" onclick="document.getElementById('approveModal').classList.remove('hidden')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Setujui
                        </button>
                        <button type="button" onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Tolak
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="px-6 py-3 bg-green-50 border-b border-green-200">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-green-800 text-sm font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="px-6 py-3 bg-red-50 border-b border-red-200">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-red-800 text-sm font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Main Content -->
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: User & Transfer Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- User Information -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi User</h3>
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                <span class="text-lg font-bold text-white">{{ strtoupper(substr($topupRequest->user->full_name ?? $topupRequest->user->username, 0, 2)) }}</span>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $topupRequest->user->full_name ?? $topupRequest->user->username }}</div>
                                <div class="text-sm text-gray-500">@ {{ $topupRequest->user->username }}</div>
                                <div class="text-sm text-gray-500">Saldo saat ini: <span class="font-medium text-green-600">Rp {{ number_format($topupRequest->user->balance, 0, ',', '.') }}</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Information -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Detail Transfer</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Topup</label>
                                <div class="text-lg font-bold text-gray-900">{{ $topupRequest->formatted_amount }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bank Tujuan</label>
                                <div class="text-gray-900">{{ $topupRequest->bank_name }}</div>
                                <div class="text-sm text-gray-500">{{ $topupRequest->bank_account }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Transfer</label>
                                <div class="text-gray-900">{{ $topupRequest->transfer_date ? $topupRequest->transfer_date->format('d M Y H:i') : '-' }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$topupRequest->status] }}">
                                    {{ $statusLabels[$topupRequest->status] }}
                                </span>
                            </div>
                        </div>
                        
                        @if($topupRequest->notes)
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan User</label>
                                <div class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $topupRequest->notes }}</div>
                            </div>
                        @endif
                    </div>

                    <!-- Admin Notes (if processed) -->
                    @if($topupRequest->admin_notes)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Catatan Admin</h3>
                            <div class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $topupRequest->admin_notes }}</div>
                            @if($topupRequest->approvedBy)
                                <div class="mt-2 text-sm text-gray-500">
                                    Disetujui oleh {{ $topupRequest->approvedBy->full_name }} pada {{ $topupRequest->approved_at->format('d M Y H:i') }}
                                </div>
                            @elseif($topupRequest->rejectedBy)
                                <div class="mt-2 text-sm text-gray-500">
                                    Ditolak oleh {{ $topupRequest->rejectedBy->full_name }} pada {{ $topupRequest->rejected_at->format('d M Y H:i') }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Right Column: Payment Proof -->
                <div class="lg:col-span-1">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Bukti Transfer</h3>
                        @if($topupRequest->payment_proof)
                            <div class="space-y-3">
                                @php
                                    $fileExtension = pathinfo($topupRequest->payment_proof, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']);
                                @endphp
                                
                                @if($isImage)
                                    <div class="relative">
                                        <img src="{{ Storage::url($topupRequest->payment_proof) }}" 
                                             alt="Bukti Transfer" 
                                             class="w-full h-auto rounded-lg border border-gray-200"
                                             onclick="openImageModal(this.src)">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-10 transition-all duration-200 rounded-lg cursor-pointer flex items-center justify-center">
                                            <svg class="w-8 h-8 text-white opacity-0 hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center justify-center h-32 bg-gray-100 rounded-lg border border-gray-200">
                                        <div class="text-center">
                                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <div class="text-sm text-gray-500">File PDF</div>
                                        </div>
                                    </div>
                                @endif
                                
                                <a href="{{ Storage::url($topupRequest->payment_proof) }}" 
                                   target="_blank" 
                                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg text-sm font-medium transition-colors">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Download Bukti
                                </a>
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-sm">Tidak ada bukti transfer</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form method="POST" action="{{ route('admin.topup.approve', $topupRequest) }}">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Setujui Permintaan Topup</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Anda akan menyetujui permintaan topup sebesar <strong>{{ $topupRequest->formatted_amount }}</strong> untuk user <strong>{{ $topupRequest->user->full_name ?? $topupRequest->user->username }}</strong>.
                                    </p>
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Admin (Opsional)</label>
                                        <textarea name="admin_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Setujui Topup
                        </button>
                        <button type="button" onclick="document.getElementById('approveModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form method="POST" action="{{ route('admin.topup.reject', $topupRequest) }}">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Tolak Permintaan Topup</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Anda akan menolak permintaan topup sebesar <strong>{{ $topupRequest->formatted_amount }}</strong> untuk user <strong>{{ $topupRequest->user->full_name ?? $topupRequest->user->username }}</strong>.
                                    </p>
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                                        <textarea name="admin_notes" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Jelaskan alasan penolakan..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Tolak Topup
                        </button>
                        <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50" onclick="closeImageModal()">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative max-w-4xl max-h-full">
                <img id="modalImage" src="" alt="Bukti Transfer" class="max-w-full max-h-full object-contain">
                <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeImageModal();
                document.getElementById('approveModal').classList.add('hidden');
                document.getElementById('rejectModal').classList.add('hidden');
            }
        });
    </script>
</x-admin-layout>
