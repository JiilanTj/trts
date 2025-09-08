<x-admin-layout>
    <x-slot name="title">Manajemen Tiket Support</x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Tiket Support</h2>
                <div class="flex items-center space-x-3">
                    <!-- Stats -->
                    <div class="flex items-center space-x-4 text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                            <span class="text-gray-600">Terbuka: <span class="font-medium">{{ $tickets->where('status', 'open')->count() }}</span></span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                            <span class="text-gray-600">Diproses: <span class="font-medium">{{ $tickets->where('status', 'in_progress')->count() }}</span></span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                            <span class="text-gray-600">Selesai: <span class="font-medium">{{ $tickets->where('status', 'resolved')->count() }}</span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="mt-4">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                        <select name="status" class="px-3 py-2 text-sm rounded-lg bg-white border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Status</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Terbuka</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Diproses</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Selesai</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Ditutup</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kategori</label>
                        <select name="category" class="px-3 py-2 text-sm rounded-lg bg-white border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Kategori</option>
                            <option value="technical" {{ request('category') == 'technical' ? 'selected' : '' }}>Teknis</option>
                            <option value="billing" {{ request('category') == 'billing' ? 'selected' : '' }}>Penagihan</option>
                            <option value="general" {{ request('category') == 'general' ? 'selected' : '' }}>Umum</option>
                            <option value="account" {{ request('category') == 'account' ? 'selected' : '' }}>Akun</option>
                            <option value="loan" {{ request('category') == 'loan' ? 'selected' : '' }}>Pinjaman</option>
                            <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Prioritas</label>
                        <select name="priority" class="px-3 py-2 text-sm rounded-lg bg-white border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Prioritas</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Rendah</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Sedang</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Tinggi</option>
                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Mendesak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Admin</label>
                        <select name="assigned_to" class="px-3 py-2 text-sm rounded-lg bg-white border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Admin</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id }}" {{ request('assigned_to') == $admin->id ? 'selected' : '' }}>{{ $admin->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Pencarian</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nomor tiket, judul, atau user..." class="px-3 py-2 text-sm rounded-lg bg-white border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-64" />
                    </div>
                    <div class="flex gap-2 items-center mt-1">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">Filter</button>
                        @if(request()->hasAny(['status', 'category', 'priority', 'assigned_to', 'search']))
                            <a href="{{ route('admin.tickets.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-medium transition">Reset</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
        @endif

        <!-- Bulk Actions -->
        @if($tickets->count() > 0)
            <div class="mx-6 mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <form id="bulk-form" method="POST" action="{{ route('admin.tickets.bulk-update') }}">
                    @csrf
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600">Pilih Semua</span>
                            </label>
                            <span id="selected-count" class="text-sm text-gray-500 hidden">0 tiket dipilih</span>
                        </div>
                        
                        <div id="bulk-actions" class="flex items-center space-x-3 hidden">
                            <select name="action" class="px-3 py-2 text-sm rounded-lg bg-white border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Pilih Aksi</option>
                                <option value="assign">Assign ke Admin</option>
                                <option value="status">Ubah Status</option>
                                <option value="priority">Ubah Prioritas</option>
                            </select>
                            
                            <select name="assigned_to" class="px-3 py-2 text-sm rounded-lg bg-white border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hidden" id="bulk-assign">
                                <option value="">Pilih Admin</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}">{{ $admin->full_name }}</option>
                                @endforeach
                            </select>
                            
                            <select name="status" class="px-3 py-2 text-sm rounded-lg bg-white border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hidden" id="bulk-status">
                                <option value="">Pilih Status</option>
                                <option value="open">Terbuka</option>
                                <option value="in_progress">Sedang Diproses</option>
                                <option value="resolved">Selesai</option>
                                <option value="closed">Ditutup</option>
                            </select>
                            
                            <select name="priority" class="px-3 py-2 text-sm rounded-lg bg-white border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hidden" id="bulk-priority">
                                <option value="">Pilih Prioritas</option>
                                <option value="low">Rendah</option>
                                <option value="medium">Sedang</option>
                                <option value="high">Tinggi</option>
                                <option value="urgent">Mendesak</option>
                            </select>
                            
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Terapkan</button>
                        </div>
                    </div>
                </form>
            </div>
        @endif

        <!-- Table -->
        <div class="overflow-x-auto mt-4">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" id="header-checkbox">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioritas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="tickets[]" value="{{ $ticket->id }}" class="ticket-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-mono">#{{ $ticket->ticket_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                <div>{{ $ticket->user->full_name ?? 'User#'.$ticket->user_id }}</div>
                                <div class="text-xs text-gray-500">{{ $ticket->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800 max-w-xs">
                                <div class="font-medium truncate">{{ $ticket->title }}</div>
                                <div class="text-xs text-gray-500 truncate">{{ Str::limit($ticket->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($ticket->category) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($ticket->priority === 'urgent')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Mendesak
                                    </span>
                                @elseif($ticket->priority === 'high')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        Tinggi
                                    </span>
                                @elseif($ticket->priority === 'medium')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Sedang
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Rendah
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($ticket->status === 'open')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Terbuka
                                    </span>
                                @elseif($ticket->status === 'in_progress')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Diproses
                                    </span>
                                @elseif($ticket->status === 'resolved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Ditutup
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                @if($ticket->assignedTo)
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-2">
                                            <span class="text-xs font-bold text-white">{{ strtoupper(substr($ticket->assignedTo->full_name, 0, 1)) }}</span>
                                        </div>
                                        <span class="text-xs">{{ $ticket->assignedTo->full_name }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">Belum ditugaskan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>{{ $ticket->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs">{{ $ticket->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.tickets.show', $ticket) }}" class="text-blue-600 hover:text-blue-900 transition">Lihat</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-sm">Tidak ada tiket ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($tickets->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $tickets->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select-all');
            const headerCheckbox = document.getElementById('header-checkbox');
            const ticketCheckboxes = document.querySelectorAll('.ticket-checkbox');
            const selectedCount = document.getElementById('selected-count');
            const bulkActions = document.getElementById('bulk-actions');
            const bulkForm = document.getElementById('bulk-form');
            const actionSelect = bulkForm.querySelector('select[name="action"]');

            // Handle select all
            function handleSelectAll() {
                const isChecked = selectAllCheckbox.checked || headerCheckbox.checked;
                ticketCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                updateSelectedCount();
            }

            selectAllCheckbox.addEventListener('change', handleSelectAll);
            headerCheckbox.addEventListener('change', () => {
                selectAllCheckbox.checked = headerCheckbox.checked;
                handleSelectAll();
            });

            // Handle individual checkboxes
            ticketCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });

            // Update selected count and show/hide bulk actions
            function updateSelectedCount() {
                const checkedBoxes = document.querySelectorAll('.ticket-checkbox:checked');
                const count = checkedBoxes.length;
                
                if (count > 0) {
                    selectedCount.textContent = `${count} tiket dipilih`;
                    selectedCount.classList.remove('hidden');
                    bulkActions.classList.remove('hidden');
                } else {
                    selectedCount.classList.add('hidden');
                    bulkActions.classList.add('hidden');
                }

                // Update select all checkbox state
                selectAllCheckbox.checked = count === ticketCheckboxes.length;
                headerCheckbox.checked = count === ticketCheckboxes.length;
            }

            // Handle action change
            actionSelect.addEventListener('change', function() {
                // Hide all conditional selects
                document.getElementById('bulk-assign').classList.add('hidden');
                document.getElementById('bulk-status').classList.add('hidden');
                document.getElementById('bulk-priority').classList.add('hidden');

                // Show relevant select
                if (this.value === 'assign') {
                    document.getElementById('bulk-assign').classList.remove('hidden');
                } else if (this.value === 'status') {
                    document.getElementById('bulk-status').classList.remove('hidden');
                } else if (this.value === 'priority') {
                    document.getElementById('bulk-priority').classList.remove('hidden');
                }
            });

            // Handle bulk form submission
            bulkForm.addEventListener('submit', function(e) {
                const checkedBoxes = document.querySelectorAll('.ticket-checkbox:checked');
                if (checkedBoxes.length === 0) {
                    e.preventDefault();
                    alert('Silakan pilih minimal satu tiket');
                    return;
                }

                // Add selected ticket IDs to form
                checkedBoxes.forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'tickets[]';
                    input.value = checkbox.value;
                    this.appendChild(input);
                });
            });
        });
    </script>
</x-admin-layout>
