<x-admin-layout>
    <x-slot name="title">Detail Pinjaman #{{ $loanRequest->id }}</x-slot>

    @php
        $statusColors = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'under_review' => 'bg-blue-100 text-blue-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'disbursed' => 'bg-purple-100 text-purple-800',
            'active' => 'bg-indigo-100 text-indigo-800',
            'completed' => 'bg-green-100 text-green-800',
            'defaulted' => 'bg-red-100 text-red-800',
        ];
    @endphp

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Detail Pinjaman #{{ $loanRequest->id }}</h2>
                    <p class="text-sm text-gray-500">Dibuat {{ $loanRequest->created_at->format('d M Y H:i') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.loan-requests.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        Kembali ke Pinjaman
                    </a>
                    @if(in_array($loanRequest->status, ['pending', 'under_review']))
                        <button type="button" onclick="openStatusModal('approved')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Setujui</button>
                        <button type="button" onclick="openStatusModal('rejected')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Tolak</button>
                        @if($loanRequest->status === 'pending')
                            <button type="button" onclick="openStatusModal('under_review')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Mulai Tinjauan</button>
                        @endif
                    @elseif($loanRequest->status === 'approved')
                        <button type="button" onclick="openStatusModal('disbursed')" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Cairkan Dana</button>
                    @elseif($loanRequest->status === 'disbursed')
                        <button type="button" onclick="openStatusModal('active')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Aktifkan</button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
        @endif

        <!-- Body -->
        <div class="p-6 space-y-10">
            <!-- Ringkasan -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Status</label>
                            <div class="mt-1">
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $statusColors[$loanRequest->status] ?? 'bg-gray-100 text-gray-800' }}">{{ $loanRequest->status_label }}</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Jumlah Pinjaman</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $loanRequest->formatted_amount }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Jangka Waktu</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $loanRequest->duration_months }} bulan</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Pemohon</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $loanRequest->user->full_name ?? $loanRequest->user->username }} (ID {{ $loanRequest->user_id }})</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Email</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $loanRequest->user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Suku Bunga</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ number_format($loanRequest->interest_rate, 2) }}% per tahun</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Cicilan Bulanan</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $loanRequest->formatted_monthly_payment }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Tujuan</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $loanRequest->purpose_label }}</p>
                        </div>
                        @if($loanRequest->due_date)
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Jatuh Tempo</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $loanRequest->due_date->format('d M Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Detail Pengajuan -->
            @if($loanRequest->purpose_description)
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Deskripsi Tujuan</h3>
                <div class="bg-gray-50 px-4 py-3 rounded-md">
                    <p class="text-sm text-gray-700">{{ $loanRequest->purpose_description }}</p>
                </div>
            </div>
            @endif

            <!-- Catatan Admin -->
            @if($loanRequest->admin_notes || $loanRequest->rejection_reason)
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Catatan Admin</h3>
                <div class="space-y-4">
                    @if($loanRequest->admin_notes)
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Catatan</label>
                        <div class="mt-1 bg-gray-50 px-4 py-3 rounded-md">
                            <p class="text-sm text-gray-700">{{ $loanRequest->admin_notes }}</p>
                        </div>
                    </div>
                    @endif
                    @if($loanRequest->rejection_reason)
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Alasan Penolakan</label>
                        <div class="mt-1 bg-red-50 border border-red-200 px-4 py-3 rounded-md">
                            <p class="text-sm text-red-700">{{ $loanRequest->rejection_reason }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Dokumen Pendukung -->
            @if($loanRequest->documents && count($loanRequest->documents) > 0)
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Dokumen Pendukung</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($loanRequest->documents as $index => $document)
                        <div class="bg-gray-50 border border-gray-200 rounded-md p-4 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $document['name'] }}</p>
                                    <p class="text-xs text-gray-500">{{ number_format($document['size'] / 1024) }} KB</p>
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

            <!-- Timeline -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Timeline</h3>
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
