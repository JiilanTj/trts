@php /** @var \Illuminate\Pagination\LengthAwarePaginator $withdrawals */ @endphp
<x-admin-layout>
    <x-slot name="title">Manajemen Penarikan Saldo</x-slot>

    @php
        $statusColors = [
            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'processing' => 'bg-blue-100 text-blue-800 border-blue-200',
            'completed' => 'bg-green-100 text-green-800 border-green-200',
            'rejected' => 'bg-red-100 text-red-800 border-red-200',
            'cancelled' => 'bg-gray-100 text-gray-800 border-gray-200',
        ];
        $statusLabels = [
            'pending' => 'Pending',
            'processing' => 'Diproses',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
        ];
    @endphp

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['pending'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Diproses</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['processing'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Selesai</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['completed'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Nilai</dt>
                        <dd class="text-lg font-medium text-gray-900">Rp {{ number_format($totalAmount ?? 0, 0, ',', '.') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Permintaan Penarikan Saldo</h2>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-500">Total: {{ $withdrawals->total() }} permintaan</span>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="mt-4">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                        <select name="status" class="px-3 py-2 text-sm rounded-lg bg-white border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua</option>
                            <option value="pending" @selected(request('status')==='pending')>Pending</option>
                            <option value="processing" @selected(request('status')==='processing')>Diproses</option>
                            <option value="completed" @selected(request('status')==='completed')>Selesai</option>
                            <option value="rejected" @selected(request('status')==='rejected')>Ditolak</option>
                            <option value="cancelled" @selected(request('status')==='cancelled')>Dibatalkan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Bank</label>
                        <input type="text" name="bank" value="{{ request('bank') }}" placeholder="Nama bank..." class="px-3 py-2 text-sm rounded-lg bg-white border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 text-sm rounded-lg bg-white border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-2 text-sm rounded-lg bg-white border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Filter</button>
                        <a href="{{ route('admin.withdrawals.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Reset</a>
                    </div>
                </form>
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

        <!-- Bulk Actions (for pending/processing requests) -->
        @if($withdrawals->whereIn('status', ['pending', 'processing'])->count() > 0)
            <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                <form id="bulkForm" method="POST" action="{{ route('admin.withdrawals.bulk') }}">
                    @csrf
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" id="selectAll" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="selectAll" class="text-sm font-medium text-gray-700">Pilih Semua</label>
                            <span id="selectedCount" class="text-sm text-gray-500">0 dipilih</span>
                        </div>
                        <div class="flex items-center space-x-2" id="bulkActions" style="display: none;">
                            <textarea name="admin_notes" placeholder="Catatan admin..." class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="1"></textarea>
                            <button type="button" onclick="submitBulkAction('process')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Proses</button>
                            <button type="button" onclick="submitBulkAction('complete')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Selesaikan</button>
                            <button type="button" onclick="submitBulkAction('reject')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Tolak</button>
                        </div>
                    </div>
                    <input type="hidden" name="action" id="bulkAction">
                </form>
            </div>
        @endif

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        @if($withdrawals->whereIn('status', ['pending', 'processing'])->count() > 0)
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </th>
                        @endif
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rekening</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($withdrawals as $withdrawal)
                        <tr class="hover:bg-gray-50">
                            @if(in_array($withdrawal->status, ['pending', 'processing']))
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="withdrawal_ids[]" value="{{ $withdrawal->id }}" class="withdrawal-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </td>
                            @else
                                <td class="px-6 py-4 whitespace-nowrap"></td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">#{{ $withdrawal->id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ $withdrawal->user ? strtoupper(substr($withdrawal->user->full_name, 0, 2)) : '??' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $withdrawal->user->full_name ?? 'User Tidak Ditemukan' }}</div>
                                        <div class="text-sm text-gray-500">{{ $withdrawal->user->email ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $withdrawal->account_name }}</div>
                                <div class="text-sm text-gray-500">{{ $withdrawal->bank_name }} - {{ $withdrawal->account_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</div>
                                @if($withdrawal->admin_fee > 0)
                                    <div class="text-xs text-gray-500">Fee: Rp {{ number_format($withdrawal->admin_fee, 0, ',', '.') }}</div>
                                    <div class="text-xs text-gray-600 font-medium">Total: Rp {{ number_format($withdrawal->total_deducted, 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusColors[$withdrawal->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$withdrawal->status] ?? ucfirst($withdrawal->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $withdrawal->created_at->format('d M Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $withdrawal->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.withdrawals.show', $withdrawal) }}" class="text-blue-600 hover:text-blue-900 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    @if($withdrawal->status === 'pending')
                                        <button onclick="quickAction('{{ $withdrawal->id }}', 'process')" class="text-blue-600 hover:text-blue-900 transition-colors" title="Proses">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </button>
                                    @endif
                                    @if($withdrawal->status === 'processing')
                                        <button onclick="quickAction('{{ $withdrawal->id }}', 'complete')" class="text-green-600 hover:text-green-900 transition-colors" title="Selesaikan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    @endif
                                    @if(in_array($withdrawal->status, ['pending', 'processing']))
                                        <button onclick="quickAction('{{ $withdrawal->id }}', 'reject')" class="text-red-600 hover:text-red-900 transition-colors" title="Tolak">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data penarikan</h3>
                                    <p class="mt-1 text-sm text-gray-500">Belum ada permintaan penarikan saldo yang masuk.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($withdrawals->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $withdrawals->links() }}
            </div>
        @endif
    </div>

    <!-- Quick Action Modal -->
    <div id="quickActionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Konfirmasi Aksi</h3>
                <form id="quickActionForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Admin (Opsional)</label>
                        <textarea name="admin_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Tuliskan catatan jika diperlukan..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeQuickActionModal()" class="px-4 py-2 text-gray-500 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">Batal</button>
                        <button type="submit" id="confirmButton" class="px-4 py-2 text-white rounded-md transition-colors">Konfirmasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Bulk actions
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.withdrawal-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        updateSelectedCount();
    });

    document.querySelectorAll('.withdrawal-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    function updateSelectedCount() {
        const selected = document.querySelectorAll('.withdrawal-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = selected + ' dipilih';
        document.getElementById('bulkActions').style.display = selected > 0 ? 'flex' : 'none';
    }

    function submitBulkAction(action) {
        const selected = document.querySelectorAll('.withdrawal-checkbox:checked');
        if (selected.length === 0) {
            alert('Pilih minimal 1 item');
            return;
        }
        
        if (confirm(`Yakin ${action === 'process' ? 'memproses' : action === 'complete' ? 'menyelesaikan' : 'menolak'} ${selected.length} permintaan?`)) {
            document.getElementById('bulkAction').value = action;
            document.getElementById('bulkForm').submit();
        }
    }

    // Quick actions
    function quickAction(withdrawalId, action) {
        const titles = {
            'process': 'Proses Penarikan',
            'complete': 'Selesaikan Penarikan',
            'reject': 'Tolak Penarikan'
        };
        
        const colors = {
            'process': 'bg-blue-600 hover:bg-blue-700',
            'complete': 'bg-green-600 hover:bg-green-700',
            'reject': 'bg-red-600 hover:bg-red-700'
        };

        document.getElementById('modalTitle').textContent = titles[action];
        document.getElementById('confirmButton').className = `px-4 py-2 text-white rounded-md transition-colors ${colors[action]}`;
        document.getElementById('quickActionForm').action = `/admin/withdrawals/${withdrawalId}/${action}`;
        document.getElementById('quickActionModal').classList.remove('hidden');
    }

    function closeQuickActionModal() {
        document.getElementById('quickActionModal').classList.add('hidden');
        document.getElementById('quickActionForm').reset();
    }

    // Close modal on outside click
    document.getElementById('quickActionModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeQuickActionModal();
        }
    });
    </script>
</x-admin-layout>
