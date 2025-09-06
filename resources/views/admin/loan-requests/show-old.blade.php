<x-admin-layout>
    <x-slot name="title">Detail Pengajuan Pinjaman #{{ $loanRequest->id }}</x-slot>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Detail Pengajuan Pinjaman #{{ $loanRequest->id }}</h2>
                    <p class="text-sm text-gray-600 mt-1">Status: 
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            @if($loanRequest->status === 'approved') bg-green-100 text-green-800
                            @elseif($loanRequest->status === 'rejected') bg-red-100 text-red-800
                            @elseif($loanRequest->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($loanRequest->status === 'under_review') bg-blue-100 text-blue-800
                            @elseif($loanRequest->status === 'disbursed') bg-purple-100 text-purple-800
                            @elseif($loanRequest->status === 'active') bg-indigo-100 text-indigo-800
                            @elseif($loanRequest->status === 'completed') bg-green-100 text-green-800
                            @elseif($loanRequest->status === 'defaulted') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $loanRequest->status_label }}
                        </span>
                    </p>
                </div>
                <a href="{{ route('admin.loan-requests.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali ke Daftar
                </a>
            </div>
        </div>
        <!-- Main Content -->
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Loan Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Loan Details -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pinjaman</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">Jumlah Pinjaman</label>
                                <p class="text-2xl font-bold text-gray-900">{{ $loanRequest->formatted_amount }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">Cicilan Bulanan</label>
                                <p class="text-2xl font-bold text-blue-600">{{ $loanRequest->formatted_monthly_payment }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">Jangka Waktu</label>
                                <p class="text-lg font-medium text-gray-900">{{ $loanRequest->duration_months }} bulan</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">Suku Bunga</label>
                                <p class="text-lg font-medium text-gray-900">{{ number_format($loanRequest->interest_rate, 2) }}% per tahun</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">Tujuan Pinjaman</label>
                                <p class="text-lg font-medium text-gray-900">{{ $loanRequest->purpose_label }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">Tanggal Pengajuan</label>
                                <p class="text-lg font-medium text-gray-900">{{ $loanRequest->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        
                        @if($loanRequest->purpose_description)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-600 mb-2">Deskripsi Detail</label>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <p class="text-gray-700">{{ $loanRequest->purpose_description }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($loanRequest->due_date)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-600 mb-2">Tanggal Jatuh Tempo</label>
                            <p class="text-lg font-medium text-gray-900">{{ $loanRequest->due_date->format('d M Y') }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- User Information -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pemohon</h3>
                        
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($loanRequest->user->full_name ?? $loanRequest->user->username, 0, 1)) }}
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-900">{{ $loanRequest->user->full_name ?? $loanRequest->user->username }}</h4>
                                <p class="text-gray-600">{{ $loanRequest->user->email }}</p>
                                @if($loanRequest->user->isSeller() && $loanRequest->user->sellerInfo)
                                    <p class="text-sm text-blue-600">Seller: {{ $loanRequest->user->sellerInfo->store_name }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">User ID</label>
                                <p class="text-gray-900">#{{ $loanRequest->user->id }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Tipe Akun</label>
                                <p class="text-gray-900">{{ $loanRequest->user->isSeller() ? 'Seller' : 'Regular User' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Bergabung Sejak</label>
                                <p class="text-gray-900">{{ $loanRequest->user->created_at->format('d M Y') }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Saldo Saat Ini</label>
                                <p class="text-gray-900">Rp {{ number_format($loanRequest->user->balance ?? 0) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Documents -->
                    @if($loanRequest->documents && count($loanRequest->documents) > 0)
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Dokumen Pendukung</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($loanRequest->documents as $index => $document)
                                    <div class="bg-white border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $document['name'] }}</p>
                                                <p class="text-xs text-gray-600">{{ number_format($document['size'] / 1024) }} KB</p>
                                            </div>
                                        </div>
                                        <a href="{{ route('admin.loan-requests.download-document', [$loanRequest, $index]) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Download
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Timeline/History -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Timeline</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="w-3 h-3 bg-blue-600 rounded-full mt-2 mr-4"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Pengajuan dibuat</p>
                                    <p class="text-xs text-gray-600">{{ $loanRequest->created_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                            
                            @if($loanRequest->approved_at)
                                <div class="flex items-start">
                                    <div class="w-3 h-3 bg-green-600 rounded-full mt-2 mr-4"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Pengajuan disetujui</p>
                                        <p class="text-xs text-gray-600">{{ $loanRequest->approved_at->format('d M Y H:i') }}</p>
                                        @if($loanRequest->approvedBy)
                                            <p class="text-xs text-gray-500">oleh {{ $loanRequest->approvedBy->username }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            
                            @if($loanRequest->disbursed_at)
                                <div class="flex items-start">
                                    <div class="w-3 h-3 bg-purple-600 rounded-full mt-2 mr-4"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Dana dicairkan</p>
                                        <p class="text-xs text-gray-600">{{ $loanRequest->disbursed_at->format('d M Y H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Cepat</h3>
                        
                        @if(in_array($loanRequest->status, ['pending', 'under_review']))
                            <div class="space-y-3">
                                <button onclick="openStatusModal('approved')" 
                                        class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                    ✓ Setujui Pinjaman
                                </button>
                                
                                <button onclick="openStatusModal('rejected')" 
                                        class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                    ✗ Tolak Pinjaman
                                </button>
                                
                                @if($loanRequest->status === 'pending')
                                    <button onclick="openStatusModal('under_review')" 
                                            class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                        👁 Mulai Tinjauan
                                    </button>
                                @endif
                            </div>
                        @elseif($loanRequest->status === 'approved')
                            <button onclick="openStatusModal('disbursed')" 
                                    class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                                💰 Cairkan Dana
                            </button>
                        @elseif($loanRequest->status === 'disbursed')
                            <button onclick="openStatusModal('active')" 
                                    class="w-full px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                                🔄 Aktifkan Pinjaman
                            </button>
                        @endif
                    </div>

                    <!-- Admin Notes -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Catatan Admin</h3>
                        
                        @if($loanRequest->admin_notes)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
                                <p class="text-gray-700 text-sm">{{ $loanRequest->admin_notes }}</p>
                            </div>
                        @else
                            <p class="text-gray-600 text-sm mb-4">Belum ada catatan admin.</p>
                        @endif
                        
                        @if($loanRequest->rejection_reason)
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-red-800 mb-2">Alasan Penolakan:</h4>
                                <p class="text-red-700 text-sm">{{ $loanRequest->rejection_reason }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Credit Assessment -->
                    @if($loanRequest->credit_assessment)
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Penilaian Kredit</h3>
                            
                            <div class="space-y-3">
                                @foreach($loanRequest->credit_assessment as $key => $value)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 text-sm">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                        <span class="text-gray-900 text-sm font-medium">{{ $value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="status-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4" id="modal-title">Update Status</h3>
            
            <form method="POST" action="{{ route('admin.loan-requests.update-status', $loanRequest) }}">
                @csrf
                <input type="hidden" name="status" id="modal-status">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Admin</label>
                    <textarea name="admin_notes" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Tambahkan catatan...">{{ $loanRequest->admin_notes }}</textarea>
                </div>
                
                <div id="rejection-section" class="mb-4" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="rejection_reason" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Jelaskan alasan penolakan...">{{ $loanRequest->rejection_reason }}</textarea>
                </div>
                
                <div id="interest-section" class="mb-4" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Suku Bunga (% per tahun) <span class="text-red-500">*</span></label>
                    <input type="number" name="interest_rate" step="0.01" min="1" max="50" 
                           value="{{ $loanRequest->interest_rate }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div id="due-date-section" class="mb-4" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Jatuh Tempo <span class="text-red-500">*</span></label>
                    <input type="date" name="due_date" 
                           value="{{ $loanRequest->due_date ? $loanRequest->due_date->format('Y-m-d') : '' }}"
                           min="{{ now()->addDays(1)->format('Y-m-d') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" onclick="closeStatusModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openStatusModal(status) {
            const modal = document.getElementById('status-modal');
            const modalTitle = document.getElementById('modal-title');
            const modalStatus = document.getElementById('modal-status');
            const rejectionSection = document.getElementById('rejection-section');
            const interestSection = document.getElementById('interest-section');
            const dueDateSection = document.getElementById('due-date-section');
            
            modalStatus.value = status;
            
            // Reset sections
            rejectionSection.style.display = 'none';
            interestSection.style.display = 'none';
            dueDateSection.style.display = 'none';
            
            switch(status) {
                case 'approved':
                    modalTitle.textContent = 'Setujui Pinjaman';
                    interestSection.style.display = 'block';
                    break;
                case 'rejected':
                    modalTitle.textContent = 'Tolak Pinjaman';
                    rejectionSection.style.display = 'block';
                    break;
                case 'disbursed':
                    modalTitle.textContent = 'Cairkan Dana';
                    dueDateSection.style.display = 'block';
                    break;
                case 'under_review':
                    modalTitle.textContent = 'Mulai Tinjauan';
                    break;
                case 'active':
                    modalTitle.textContent = 'Aktifkan Pinjaman';
                    break;
                default:
                    modalTitle.textContent = 'Update Status';
            }
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        
        function closeStatusModal() {
            const modal = document.getElementById('status-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</x-admin-layout>
