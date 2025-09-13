<x-admin-layout>
    <x-slot name="title">Manajemen Etalase</x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Manajemen Etalase Toko</h2>
                    <p class="text-sm text-gray-500 mt-1">Kelola semua etalase toko dan produk unggulan</p>
                </div>
                <button type="button" 
                        onclick="getStats()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Statistik
                </button>
            </div>
        </div>

        <div class="p-6">
            <!-- Filters -->
            <div class="mb-6">
                <form method="GET" action="{{ route('admin.showcases.index') }}" class="flex flex-col md:flex-row gap-3" id="filterForm">
                    <div class="flex-1">
                        <input type="text" 
                               name="search_user" 
                               placeholder="Cari pengguna (nama, username, atau nama toko)..." 
                               value="{{ request('search_user') }}"
                               class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                               id="searchUser">
                    </div>
                    <div class="w-full md:w-48">
                        <select name="status" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                            <option value="featured" {{ request('status') == 'featured' ? 'selected' : '' }}>Unggulan</option>
                        </select>
                    </div>
                    @if(request()->anyFilled(['search_user', 'status']))
                        <a href="{{ route('admin.showcases.index') }}" 
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Reset Filter
                        </a>
                    @endif
                </form>
            </div>

            <!-- Bulk Actions -->
            <div id="bulkActionsRow" class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200" style="display: none;">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-blue-800 font-medium mr-2">Aksi Massal:</span>
                    <button type="button" 
                            onclick="bulkAction('activate')" 
                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors">
                        Aktifkan
                    </button>
                    <button type="button" 
                            onclick="bulkAction('deactivate')" 
                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition-colors">
                        Nonaktifkan
                    </button>
                    <button type="button" 
                            onclick="bulkAction('feature')" 
                            class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm transition-colors">
                        Jadikan Unggulan
                    </button>
                    <button type="button" 
                            onclick="bulkAction('unfeature')" 
                            class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm transition-colors">
                        Batal Unggulan
                    </button>
                    <button type="button" 
                            onclick="bulkAction('delete')" 
                            class="bg-red-700 hover:bg-red-800 text-white px-3 py-1 rounded text-sm transition-colors">
                        Hapus
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna/Toko</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unggulan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($showcases as $showcase)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="showcase_ids[]" value="{{ $showcase->id }}" 
                                           class="showcase-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        @if($showcase->product && $showcase->product->image_url)
                                            <img src="{{ $showcase->product->image_url }}" 
                                                 alt="{{ $showcase->product->name }}" 
                                                 class="h-12 w-12 rounded-lg object-cover mr-3">
                                        @else
                                            <div class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $showcase->product->name ?? 'Produk Tidak Ditemukan' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $showcase->product->category->name ?? 'Tanpa Kategori' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $showcase->user->full_name }}</div>
                                    <div class="text-sm text-gray-500">
                                        @if($showcase->user->sellerInfo)
                                            {{ $showcase->user->sellerInfo->store_name }}
                                        @else
                                            Belum Ada Toko
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-semibold text-green-600">
                                        Rp {{ number_format($showcase->price, 0, ',', '.') }}
                                    </div>
                                    @if($showcase->original_price > $showcase->price)
                                        <div class="text-sm text-gray-500 line-through">
                                            Rp {{ number_format($showcase->original_price, 0, ',', '.') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <button type="button" 
                                            onclick="toggleActive({{ $showcase->id }})"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $showcase->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $showcase->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </button>
                                </td>
                                <td class="px-4 py-3">
                                    <button type="button" 
                                            onclick="toggleFeatured({{ $showcase->id }})"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $showcase->is_featured ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                        @if($showcase->is_featured)
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            Unggulan
                                        @else
                                            Biasa
                                        @endif
                                    </button>
                                    @if($showcase->is_featured && $showcase->featured_until)
                                        <div class="text-xs text-gray-500 mt-1">
                                            Sampai: {{ $showcase->featured_until->format('d M Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $showcase->created_at->format('d M Y') }}<br>
                                    {{ $showcase->created_at->format('H:i') }}
                                </td>
                                <td class="px-4 py-3 text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.showcases.show', $showcase->user_id) }}" 
                                           class="text-blue-600 hover:text-blue-900" 
                                           title="Lihat Semua Etalase User">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.showcases.user-showcase', $showcase->user_id) }}" 
                                           class="text-green-600 hover:text-green-900" 
                                           title="Lihat Tampilan Publik" 
                                           target="_blank">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                        <button type="button" 
                                                onclick="deleteShowcase({{ $showcase->id }})"
                                                class="text-red-600 hover:text-red-900" 
                                                title="Hapus Showcase">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293L16 17.586a1 1 0 01-1.414 0L12 15l-2.586 2.586a1 1 0 01-1.414 0L5.414 15.293A1 1 0 004.828 15H2"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada etalase ditemukan</h3>
                                    <p class="text-gray-500">
                                    @if(request()->anyFilled(['search_user', 'status']))
                                            Coba sesuaikan filter atau 
                                            <a href="{{ route('admin.showcases.index') }}" class="text-blue-600 hover:text-blue-500">reset semua filter</a>
                                        @else
                                            Belum ada etalase yang dibuat.
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($showcases->hasPages())
                <div class="mt-6 flex justify-center">
                    {{ $showcases->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Statistics Modal -->
    <div id="statisticsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between pb-3 border-b">
                <h3 class="text-lg font-semibold">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Statistik Etalase
                </h3>
                <button onclick="closeStatsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="statisticsContent" class="mt-4">
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                    <p class="mt-2 text-gray-500">Memuat statistik...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Select All functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.showcase-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            toggleBulkActions();
        });

        // Individual checkbox change
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('showcase-checkbox')) {
                toggleBulkActions();
                updateSelectAll();
            }
        });

        function toggleBulkActions() {
            const selectedCheckboxes = document.querySelectorAll('.showcase-checkbox:checked');
            const bulkActionsRow = document.getElementById('bulkActionsRow');
            
            if (selectedCheckboxes.length > 0) {
                bulkActionsRow.style.display = 'block';
            } else {
                bulkActionsRow.style.display = 'none';
            }
        }

        function updateSelectAll() {
            const allCheckboxes = document.querySelectorAll('.showcase-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.showcase-checkbox:checked');
            const selectAllCheckbox = document.getElementById('selectAll');
            
            if (checkedCheckboxes.length === allCheckboxes.length && allCheckboxes.length > 0) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedCheckboxes.length > 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
        }

        // Toggle Active Status
        function toggleActive(showcaseId) {
            fetch(`/admin/etalase/${showcaseId}/toggle-active`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating status');
            });
        }

        // Toggle Featured Status
        function toggleFeatured(showcaseId) {
            fetch(`/admin/etalase/${showcaseId}/toggle-featured`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating featured status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating featured status');
            });
        }

        // Delete Showcase
        function deleteShowcase(showcaseId) {
            if (confirm('Apakah Anda yakin ingin menghapus etalase ini? Tindakan ini tidak dapat dibatalkan.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/etalase/${showcaseId}`;
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(tokenInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Bulk Actions
        function bulkAction(action) {
            const selectedCheckboxes = document.querySelectorAll('.showcase-checkbox:checked');
            
            if (selectedCheckboxes.length === 0) {
                alert('Pilih setidaknya satu etalase');
                return;
            }
            
            const showcaseIds = Array.from(selectedCheckboxes).map(cb => cb.value);
            
            let confirmMessage = `Apakah Anda yakin ingin ${action === 'activate' ? 'mengaktifkan' : action === 'deactivate' ? 'menonaktifkan' : action === 'feature' ? 'menjadikan unggulan' : action === 'unfeature' ? 'membatalkan unggulan' : 'menghapus'} ${selectedCheckboxes.length} etalase?`;
            if (action === 'delete') {
                confirmMessage = `Apakah Anda yakin ingin menghapus ${selectedCheckboxes.length} etalase? Tindakan ini tidak dapat dibatalkan.`;
            }
            
            if (confirm(confirmMessage)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.showcases.bulk-action") }}';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = action;
                form.appendChild(actionInput);
                
                showcaseIds.forEach(id => {
                    const idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = 'showcase_ids[]';
                    idInput.value = id;
                    form.appendChild(idInput);
                });
                
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(tokenInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Get Statistics
        function getStats() {
            const modal = document.getElementById('statisticsModal');
            modal.classList.remove('hidden');
            
            fetch('{{ route("admin.showcases.stats") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('statisticsContent').innerHTML = `
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-blue-500 rounded-lg p-6 text-white text-center">
                                <div class="text-3xl font-bold">${data.total_showcases}</div>
                                <div class="text-blue-100">Total Etalase</div>
                            </div>
                            <div class="bg-green-500 rounded-lg p-6 text-white text-center">
                                <div class="text-3xl font-bold">${data.active_showcases}</div>
                                <div class="text-green-100">Etalase Aktif</div>
                            </div>
                            <div class="bg-yellow-500 rounded-lg p-6 text-white text-center">
                                <div class="text-3xl font-bold">${data.featured_showcases}</div>
                                <div class="text-yellow-100">Etalase Unggulan</div>
                            </div>
                            <div class="bg-purple-500 rounded-lg p-6 text-white text-center">
                                <div class="text-3xl font-bold">${data.total_users_with_showcases}</div>
                                <div class="text-purple-100">User dengan Etalase</div>
                            </div>
                        </div>
                    `;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('statisticsContent').innerHTML = `
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <strong>Error!</strong> Gagal memuat statistik. Silakan coba lagi.
                        </div>
                    `;
                });
        }

        function closeStatsModal() {
            document.getElementById('statisticsModal').classList.add('hidden');
        }

        // Close modal on background click
        document.getElementById('statisticsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeStatsModal();
            }
        });

        // Search functionality with debounce
        let searchTimeout;
        document.getElementById('searchUser').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const form = document.getElementById('filterForm');
            
            searchTimeout = setTimeout(() => {
                form.submit();
            }, 500); // Wait 500ms after user stops typing
        });

        // Submit form when status changes
        document.querySelector('select[name="status"]').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    </script>
</x-admin-layout>
