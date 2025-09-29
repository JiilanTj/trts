@php /** @var \App\Models\WithdrawalRequest $withdrawal */ @endphp
<x-admin-layout>
    <x-slot name="title">Detail Penarikan Saldo #{{ $withdrawal->id }}</x-slot>

    <div class="space-y-6">
        <!-- Breadcrumb -->
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.withdrawals.index') }}" class="text-gray-700 hover:text-blue-600 text-sm font-medium">
                        Penarikan Saldo
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-500 ml-1 md:ml-2 text-sm font-medium">#{{ $withdrawal->id }}</span>
                    </div>
                </li>
            </ol>
        </nav>

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

        <!-- Header Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Penarikan #{{ $withdrawal->id }}</h1>
                        <p class="text-gray-500">Diajukan {{ $withdrawal->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex flex-col items-end space-y-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $statusColors[$withdrawal->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $statusLabels[$withdrawal->status] ?? ucfirst($withdrawal->status) }}
                    </span>
                    @if(in_array($withdrawal->status, ['pending', 'processing']))
                        <div class="flex space-x-2">
                            @if($withdrawal->status === 'pending')
                                <button onclick="showActionModal('process', '{{ $withdrawal->id }}')" class="inline-flex items-center px-3 py-2 border border-blue-300 text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Proses
                                </button>
                            @endif
                            @if($withdrawal->status === 'processing')
                                <button onclick="showActionModal('complete', '{{ $withdrawal->id }}')" class="inline-flex items-center px-3 py-2 border border-green-300 text-sm leading-4 font-medium rounded-md text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Selesaikan
                                </button>
                            @endif
                            <button onclick="showActionModal('reject', '{{ $withdrawal->id }}')" class="inline-flex items-center px-3 py-2 border border-red-300 text-sm leading-4 font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Tolak
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-green-800 text-sm font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-red-800 text-sm font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- User Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pengguna</h3>
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                            <span class="text-lg font-medium text-gray-700">
                                {{ $withdrawal->user ? strtoupper(substr($withdrawal->user->full_name, 0, 2)) : '??' }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-medium text-gray-900">{{ $withdrawal->user->full_name ?? 'User Tidak Ditemukan' }}</h4>
                            <p class="text-sm text-gray-500">{{ $withdrawal->user->email ?? 'N/A' }}</p>
                            @if($withdrawal->user && $withdrawal->user->detail && $withdrawal->user->detail->phone)
                                <p class="text-sm text-gray-500">{{ $withdrawal->user->detail->phone }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">Saldo Saat Ini</div>
                            <div class="text-lg font-medium text-gray-900">
                                Rp {{ $withdrawal->user ? number_format($withdrawal->user->balance, 0, ',', '.') : '0' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Withdrawal Details -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Penarikan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pemilik Rekening</label>
                            <p class="text-gray-900">{{ $withdrawal->account_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                            <p class="text-gray-900 font-mono">{{ $withdrawal->account_number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bank</label>
                            <p class="text-gray-900">{{ $withdrawal->bank_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusColors[$withdrawal->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$withdrawal->status] ?? ucfirst($withdrawal->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Amount Breakdown -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Rincian Jumlah</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jumlah Penarikan</span>
                            <span class="font-medium text-gray-900">Rp {{ number_format((float)$withdrawal->amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Biaya Admin</span>
                            <span class="font-medium text-yellow-600">Rp {{ number_format((float)$withdrawal->admin_fee, 0, ',', '.') }}</span>
                        </div>
                        <hr class="border-gray-200">
                        <div class="flex justify-between font-semibold">
                            <span class="text-gray-900">Total Dipotong dari Saldo</span>
                            <span class="text-red-600">Rp {{ number_format((float)$withdrawal->total_deducted, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if($withdrawal->notes || $withdrawal->admin_notes)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Catatan</h3>
                        
                        @if($withdrawal->notes)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan User</label>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-gray-700">{{ $withdrawal->notes }}</p>
                                </div>
                            </div>
                        @endif

                        @if($withdrawal->admin_notes)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Admin</label>
                                <div class="bg-blue-50 rounded-lg p-3">
                                    <p class="text-blue-700">{{ $withdrawal->admin_notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Timeline -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Timeline</h3>
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5">
                                            <div>
                                                <p class="text-sm text-gray-500">Penarikan dibuat</p>
                                                <p class="text-xs text-gray-400">{{ $withdrawal->created_at->format('d M Y H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            @if($withdrawal->status !== 'pending')
                                <li>
                                    <div class="relative {{ $withdrawal->status === 'processing' || $withdrawal->status === 'completed' ? 'pb-8' : '' }}">
                                        @if($withdrawal->status === 'processing' || $withdrawal->status === 'completed')
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full {{ $withdrawal->status === 'rejected' ? 'bg-red-500' : ($withdrawal->status === 'cancelled' ? 'bg-gray-500' : 'bg-yellow-500') }} flex items-center justify-center ring-8 ring-white">
                                                    @if($withdrawal->status === 'rejected')
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    @elseif($withdrawal->status === 'cancelled')
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                        </svg>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5">
                                                <div>
                                                    <p class="text-sm text-gray-500">
                                                        @if($withdrawal->status === 'processing')
                                                            Sedang diproses
                                                        @elseif($withdrawal->status === 'rejected')
                                                            Ditolak
                                                        @elseif($withdrawal->status === 'cancelled')
                                                            Dibatalkan
                                                        @else
                                                            Status diperbarui
                                                        @endif
                                                    </p>
                                                    <p class="text-xs text-gray-400">{{ $withdrawal->updated_at->format('d M Y H:i') }}</p>
                                                    @if($withdrawal->processed_by)
                                                        <p class="text-xs text-gray-400">oleh {{ $withdrawal->processedBy->full_name }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif

                            @if($withdrawal->status === 'completed')
                                <li>
                                    <div class="relative">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5">
                                                <div>
                                                    <p class="text-sm text-gray-500">Penarikan selesai</p>
                                                    <p class="text-xs text-gray-400">{{ $withdrawal->updated_at->format('d M Y H:i') }}</p>
                                                    @if($withdrawal->processed_by)
                                                        <p class="text-xs text-gray-400">oleh {{ $withdrawal->processedBy->full_name }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik User</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Penarikan</span>
                            <span class="font-medium text-gray-900">{{ $userStats['total_withdrawals'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Selesai</span>
                            <span class="font-medium text-green-600">{{ $userStats['completed_withdrawals'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Nilai</span>
                            <span class="font-medium text-gray-900">Rp {{ number_format($userStats['total_amount'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Bergabung</span>
                            <span class="font-medium text-gray-900">
                                {{ $withdrawal->user ? $withdrawal->user->created_at->format('M Y') : 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Modal -->
    <div id="actionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Konfirmasi Aksi</h3>
                <form id="actionForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Admin</label>
                        <textarea name="admin_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Tuliskan catatan untuk user..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeActionModal()" class="px-4 py-2 text-gray-500 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">Batal</button>
                        <button type="submit" id="confirmButton" class="px-4 py-2 text-white rounded-md transition-colors">Konfirmasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function showActionModal(action, withdrawalId) {
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
        document.getElementById('actionForm').action = `{{ route('admin.withdrawals.show', $withdrawal) }}/${action}`;
        document.getElementById('actionModal').classList.remove('hidden');
    }

    function closeActionModal() {
        document.getElementById('actionModal').classList.add('hidden');
        document.getElementById('actionForm').reset();
    }

    // Close modal on outside click
    document.getElementById('actionModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeActionModal();
        }
    });
    </script>
</x-admin-layout>
