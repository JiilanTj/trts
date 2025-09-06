<x-admin-layout>
    <x-slot name="title">Manajemen Pinjaman</x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Manajemen Pinjaman</h2>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-8 gap-6 mb-8">
                <div class="bg-gray-50 rounded-lg p-6 col-span-1">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_requests']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-[#23272b] border border-[#2c3136] rounded-xl p-6 col-span-1">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-neutral-400">Menunggu</p>
                            <p class="text-2xl font-bold text-white">{{ number_format($stats['pending_requests']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-[#23272b] border border-[#2c3136] rounded-xl p-6 col-span-1">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-neutral-400">Tinjauan</p>
                            <p class="text-2xl font-bold text-white">{{ number_format($stats['under_review']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-[#23272b] border border-[#2c3136] rounded-xl p-6 col-span-1">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-neutral-400">Disetujui</p>
                            <p class="text-2xl font-bold text-white">{{ number_format($stats['approved_requests']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-[#23272b] border border-[#2c3136] rounded-xl p-6 col-span-1">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-neutral-400">Ditolak</p>
                            <p class="text-2xl font-bold text-white">{{ number_format($stats['rejected_requests']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-[#23272b] border border-[#2c3136] rounded-xl p-6 col-span-1">
                    <div class="flex items-center">
                        <div class="p-2 bg-emerald-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-neutral-400">Aktif</p>
                            <p class="text-2xl font-bold text-white">{{ number_format($stats['active_loans']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-[#23272b] border border-[#2c3136] rounded-xl p-6 col-span-2">
                    <div class="flex items-center">
                        <div class="p-2 bg-indigo-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-neutral-400">Total Nilai Dicairkan</p>
                            <p class="text-2xl font-bold text-white">Rp {{ number_format($stats['total_disbursed']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Actions -->
            <div class="bg-[#23272b] border border-[#2c3136] rounded-xl p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <!-- Search -->
                        <form method="GET" action="{{ route('admin.loan-requests.index') }}" class="flex gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Cari nama atau email..." 
                                   class="px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg text-white placeholder-neutral-400 focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent">
                            
                            <!-- Status Filter -->
                            <select name="status" class="px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg text-white focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                                <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>Dalam Tinjauan</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                <option value="disbursed" {{ request('status') === 'disbursed' ? 'selected' : '' }}>Dicairkan</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="defaulted" {{ request('status') === 'defaulted' ? 'selected' : '' }}>Gagal Bayar</option>
                            </select>
                            
                            <button type="submit" class="px-4 py-2 bg-[#FE2C55] text-white rounded-lg hover:bg-[#FE2C55]/80 transition">
                                Filter
                            </button>
                            
                            @if(request()->hasAny(['search', 'status']))
                                <a href="{{ route('admin.loan-requests.index') }}" 
                                   class="px-4 py-2 bg-neutral-700 text-neutral-300 rounded-lg hover:bg-neutral-600 transition">
                                    Reset
                                </a>
                            @endif
                        </form>
                    </div>
                    
                    <div class="flex gap-2">
                        <a href="{{ route('admin.loan-requests.analytics') }}" 
                           class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:shadow-lg transition">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Analytics
                        </a>
                    </div>
                </div>
            </div>

            <!-- Loan Requests Table -->
            <div class="bg-[#23272b] border border-[#2c3136] rounded-xl overflow-hidden">
                <div class="h-1 w-full bg-gradient-to-r from-[#fe2c55] via-[#fe2c55]/40 to-[#25f4ee]"></div>
                
                @if($loanRequests->count() > 0)
                    <!-- Bulk Actions -->
                    <div class="p-4 border-b border-neutral-700 bg-neutral-800/30">
                        <form id="bulk-form" method="POST" action="{{ route('admin.loan-requests.bulk-update') }}">
                            @csrf
                            <div class="flex items-center gap-4">
                                <label class="flex items-center text-sm text-neutral-300">
                                    <input type="checkbox" id="select-all" class="mr-2 rounded bg-neutral-700 border-neutral-600 text-[#FE2C55] focus:ring-[#FE2C55]">
                                    Pilih Semua
                                </label>
                                
                                <select name="bulk_action" id="bulk-action" class="px-3 py-1 bg-neutral-700 border border-neutral-600 rounded text-white text-sm">
                                    <option value="">Pilih Aksi</option>
                                    <option value="under_review">Tandai Dalam Tinjauan</option>
                                    <option value="approve">Setujui</option>
                                    <option value="reject">Tolak</option>
                                </select>
                                
                                <button type="button" id="bulk-submit" class="px-4 py-1 bg-[#FE2C55] text-white rounded text-sm hover:bg-[#FE2C55]/80 transition">
                                    Terapkan
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-neutral-800/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">
                                        <input type="checkbox" class="rounded bg-neutral-700 border-neutral-600 text-[#FE2C55] focus:ring-[#FE2C55]">
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Pemohon</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Tujuan</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Durasi</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-700">
                                @foreach($loanRequests as $loanRequest)
                                    <tr class="hover:bg-neutral-800/30 transition">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" name="loan_request_ids[]" value="{{ $loanRequest->id }}" 
                                                   class="loan-checkbox rounded bg-neutral-700 border-neutral-600 text-[#FE2C55] focus:ring-[#FE2C55]">
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#FE2C55] to-[#25F4EE] flex items-center justify-center text-white font-bold text-sm">
                                                    {{ strtoupper(substr($loanRequest->user->full_name ?? $loanRequest->user->username, 0, 1)) }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-white">{{ $loanRequest->user->full_name ?? $loanRequest->user->username }}</div>
                                                    <div class="text-sm text-neutral-400">{{ $loanRequest->user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-white">{{ $loanRequest->formatted_amount }}</div>
                                            <div class="text-sm text-neutral-400">{{ $loanRequest->formatted_monthly_payment }}/bulan</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-neutral-300">{{ $loanRequest->purpose_label }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-neutral-300">{{ $loanRequest->duration_months }} bulan</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $loanRequest->status_color }}">
                                                {{ $loanRequest->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-neutral-300">{{ $loanRequest->created_at->format('d M Y') }}</div>
                                            <div class="text-xs text-neutral-400">{{ $loanRequest->created_at->format('H:i') }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('admin.loan-requests.show', $loanRequest) }}" 
                                               class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-neutral-700">
                        {{ $loanRequests->withQueryString()->links() }}
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-neutral-800 flex items-center justify-center">
                            <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-neutral-300 mb-2">Tidak ada pengajuan pinjaman</h3>
                        <p class="text-neutral-400">Belum ada pengajuan pinjaman yang ditemukan dengan filter yang dipilih.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bulk Action Modal -->
    <div id="bulk-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-[#23272b] rounded-xl p-6 max-w-md w-full mx-4 border border-[#2c3136]">
            <h3 class="text-lg font-semibold text-white mb-4">Konfirmasi Aksi Massal</h3>
            <p class="text-neutral-300 mb-4">Apakah Anda yakin ingin menerapkan aksi ini pada item yang dipilih?</p>
            
            <div class="mb-4" id="bulk-notes-section" style="display: none;">
                <label class="block text-sm font-medium text-neutral-300 mb-2">Catatan Admin (Opsional)</label>
                <textarea name="bulk_admin_notes" form="bulk-form" rows="3" 
                          class="w-full px-3 py-2 bg-neutral-800 border border-neutral-700 rounded-lg text-white placeholder-neutral-400 focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent"
                          placeholder="Tambahkan catatan..."></textarea>
            </div>
            
            <div class="mb-4" id="bulk-rejection-section" style="display: none;">
                <label class="block text-sm font-medium text-neutral-300 mb-2">Alasan Penolakan <span class="text-red-400">*</span></label>
                <textarea name="bulk_rejection_reason" form="bulk-form" rows="3" 
                          class="w-full px-3 py-2 bg-neutral-800 border border-neutral-700 rounded-lg text-white placeholder-neutral-400 focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent"
                          placeholder="Jelaskan alasan penolakan..."></textarea>
            </div>
            
            <div class="flex items-center justify-end space-x-3">
                <button type="button" id="bulk-cancel" class="px-4 py-2 bg-neutral-700 text-neutral-300 rounded-lg hover:bg-neutral-600 transition">
                    Batal
                </button>
                <button type="submit" form="bulk-form" class="px-4 py-2 bg-[#FE2C55] text-white rounded-lg hover:bg-[#FE2C55]/80 transition">
                    Konfirmasi
                </button>
            </div>
        </div>
    </div>

    <script>
        // Bulk selection
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.loan-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Bulk action modal
        document.getElementById('bulk-submit').addEventListener('click', function() {
            const selected = document.querySelectorAll('.loan-checkbox:checked');
            const action = document.getElementById('bulk-action').value;
            
            if (selected.length === 0) {
                alert('Pilih minimal satu item');
                return;
            }
            
            if (!action) {
                alert('Pilih aksi yang akan diterapkan');
                return;
            }
            
            // Show/hide additional fields based on action
            const notesSection = document.getElementById('bulk-notes-section');
            const rejectionSection = document.getElementById('bulk-rejection-section');
            
            if (action === 'reject') {
                rejectionSection.style.display = 'block';
                notesSection.style.display = 'block';
            } else {
                rejectionSection.style.display = 'none';
                notesSection.style.display = 'block';
            }
            
            document.getElementById('bulk-modal').classList.remove('hidden');
            document.getElementById('bulk-modal').classList.add('flex');
        });

        document.getElementById('bulk-cancel').addEventListener('click', function() {
            document.getElementById('bulk-modal').classList.add('hidden');
            document.getElementById('bulk-modal').classList.remove('flex');
        });
    </script>
</x-admin-layout>
