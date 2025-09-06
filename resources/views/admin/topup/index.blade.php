@php /** @var \Illuminate\Pagination\LengthAwarePaginator $topupRequests */ @endphp
<x-admin-layout>
    <x-slot name="title">Manajemen Topup Saldo</x-slot>

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
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Permintaan Topup Saldo</h2>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-500">Total: {{ $topupRequests->total() }} permintaan</span>
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
                            <option value="approved" @selected(request('status')==='approved')>Disetujui</option>
                            <option value="rejected" @selected(request('status')==='rejected')>Ditolak</option>
                        </select>
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
                        <a href="{{ route('admin.topup.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Reset</a>
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

        <!-- Bulk Actions (for pending requests) -->
        @if($topupRequests->where('status', 'pending')->count() > 0)
            <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                <form id="bulkForm" method="POST" action="{{ route('admin.topup.bulk') }}">
                    @csrf
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" id="selectAll" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="selectAll" class="text-sm font-medium text-gray-700">Pilih Semua</label>
                            <span id="selectedCount" class="text-sm text-gray-500">0 dipilih</span>
                        </div>
                        <div class="flex items-center space-x-2" id="bulkActions" style="display: none;">
                            <textarea name="admin_notes" placeholder="Catatan admin..." class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="1"></textarea>
                            <button type="button" onclick="submitBulkAction('approve')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Setujui</button>
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
                        @if($topupRequests->where('status', 'pending')->count() > 0)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank Tujuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topupRequests as $topupRequest)
                        <tr class="hover:bg-gray-50">
                            @if($topupRequests->where('status', 'pending')->count() > 0)
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($topupRequest->status === 'pending')
                                        <input type="checkbox" name="topup_ids[]" value="{{ $topupRequest->id }}" class="topup-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    @endif
                                </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-mono">#{{ $topupRequest->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                        <span class="text-xs font-bold text-white">{{ strtoupper(substr($topupRequest->user->full_name ?? $topupRequest->user->username, 0, 2)) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $topupRequest->user->full_name ?? $topupRequest->user->username }}</div>
                                        <div class="text-sm text-gray-500">@ {{ $topupRequest->user->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $topupRequest->formatted_amount }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <div>{{ $topupRequest->bank_name }}</div>
                                <div class="text-xs text-gray-500">{{ $topupRequest->bank_account }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$topupRequest->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$topupRequest->status] ?? $topupRequest->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>{{ $topupRequest->created_at->format('d M Y') }}</div>
                                <div class="text-xs">{{ $topupRequest->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.topup.show', $topupRequest) }}" class="text-blue-600 hover:text-blue-900 transition-colors">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $topupRequests->where('status', 'pending')->count() > 0 ? '8' : '7' }}" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada permintaan topup</h3>
                                <p class="mt-1 text-sm text-gray-500">Permintaan topup user akan tampil di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($topupRequests->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $topupRequests->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- JavaScript for bulk actions -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const topupCheckboxes = document.querySelectorAll('.topup-checkbox');
            const selectedCount = document.getElementById('selectedCount');
            const bulkActions = document.getElementById('bulkActions');
            const bulkForm = document.getElementById('bulkForm');

            function updateBulkActions() {
                const checkedBoxes = document.querySelectorAll('.topup-checkbox:checked');
                const count = checkedBoxes.length;
                
                selectedCount.textContent = count + ' dipilih';
                bulkActions.style.display = count > 0 ? 'flex' : 'none';
                
                // Update select all checkbox state
                if (count === 0) {
                    selectAllCheckbox.indeterminate = false;
                    selectAllCheckbox.checked = false;
                } else if (count === topupCheckboxes.length) {
                    selectAllCheckbox.indeterminate = false;
                    selectAllCheckbox.checked = true;
                } else {
                    selectAllCheckbox.indeterminate = true;
                }
            }

            // Select all functionality
            selectAllCheckbox.addEventListener('change', function() {
                topupCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkActions();
            });

            // Individual checkbox functionality
            topupCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkActions);
            });

            // Initial state
            updateBulkActions();
        });

        function submitBulkAction(action) {
            const checkedBoxes = document.querySelectorAll('.topup-checkbox:checked');
            const adminNotes = document.querySelector('textarea[name="admin_notes"]').value;
            
            if (checkedBoxes.length === 0) {
                alert('Pilih minimal satu permintaan topup.');
                return;
            }

            if (action === 'reject' && adminNotes.trim() === '') {
                alert('Catatan admin wajib diisi untuk penolakan.');
                return;
            }

            if (confirm(`Yakin ingin ${action === 'approve' ? 'menyetujui' : 'menolak'} ${checkedBoxes.length} permintaan topup?`)) {
                document.getElementById('bulkAction').value = action;
                
                // Add selected IDs to form
                checkedBoxes.forEach(checkbox => {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'topup_ids[]';
                    hiddenInput.value = checkbox.value;
                    document.getElementById('bulkForm').appendChild(hiddenInput);
                });
                
                document.getElementById('bulkForm').submit();
            }
        }
    </script>
</x-admin-layout>
