<x-admin-layout>
    <x-slot name="title">Detail Kode Undangan {{ $invitationCode->code }}</x-slot>

    @php
        $remainingUses = max($invitationCode->max_usage - $invitationCode->used_count, 0);
        $usagePercent = $invitationCode->max_usage > 0
            ? min(100, round(($invitationCode->used_count / $invitationCode->max_usage) * 100, 2))
            : 0;
        $isExpired = $invitationCode->expires_at && $invitationCode->expires_at->isPast();
        $expiresLabel = $invitationCode->expires_at
            ? $invitationCode->expires_at->format('d M Y, H:i')
            : 'Tidak ada (Tanpa batas)';
    @endphp

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <div class="text-sm uppercase tracking-wide text-gray-500">Kode Undangan</div>
                    <div class="flex items-center mt-2">
                        <span class="text-2xl font-mono font-semibold text-gray-900 bg-gray-100 px-3 py-1 rounded-lg">
                            {{ $invitationCode->code }}
                        </span>
                        <span class="ml-3 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $invitationCode->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $invitationCode->is_active ? 'Aktif' : 'Non-aktif' }}
                        </span>
                        @if($isExpired)
                            <span class="ml-2 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                Kadaluarsa
                            </span>
                        @elseif($invitationCode->expires_at)
                            <span class="ml-2 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                Berlaku sampai {{ $invitationCode->expires_at->diffForHumans() }}
                            </span>
                        @endif
                        @if($remainingUses === 0)
                            <span class="ml-2 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                Batas Penggunaan Tercapai
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.invitation-codes.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Kembali
                    </a>

                    <form action="{{ route('admin.invitation-codes.update-status', $invitationCode) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white {{ $invitationCode->is_active ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-600 hover:bg-green-700' }} rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2a8.001 8.001 0 00-15.938 0m15.938 0H20v5m0 0h-5m5 0v-5"></path>
                            </svg>
                            {{ $invitationCode->is_active ? 'Non-aktifkan' : 'Aktifkan' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.invitation-codes.destroy', $invitationCode) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kode ini? Tindakan tidak dapat dibatalkan.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-6">
            <!-- Messages -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="text-sm text-gray-500">Status Validitas</div>
                    <div class="mt-2 flex items-center space-x-2">
                        <span class="text-lg font-semibold text-gray-900">
                            {{ $invitationCode->isValid() ? 'Dapat Digunakan' : 'Tidak Valid' }}
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $invitationCode->isValid() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $invitationCode->isValid() ? 'Valid' : 'Tidak Valid' }}
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                        {{ $invitationCode->isValid() ? 'Kode masih aktif dan belum mencapai batas penggunaan.' : 'Kode tidak dapat digunakan karena sudah non-aktif, kadaluarsa, atau mencapai batas penggunaan.' }}
                    </p>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="text-sm text-gray-500">Penggunaan</div>
                    <div class="mt-2 text-lg font-semibold text-gray-900">
                        {{ $invitationCode->used_count }} / {{ $invitationCode->max_usage }} kali
                    </div>
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $usagePercent }}%"></div>
                    </div>
                    <div class="mt-2 text-sm text-gray-500">
                        Sisa penggunaan: <span class="font-semibold text-gray-900">{{ $remainingUses }}</span>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="text-sm text-gray-500">Kadaluarsa</div>
                    <div class="mt-2 text-lg font-semibold text-gray-900">{{ $expiresLabel }}</div>
                    @if($invitationCode->expires_at)
                        <div class="mt-1 text-sm text-gray-500">
                            {{ $isExpired ? 'Kadaluarsa ' . $invitationCode->expires_at->diffForHumans() : 'Kadaluarsa ' . $invitationCode->expires_at->diffForHumans() }}
                        </div>
                    @else
                        <div class="mt-1 text-sm text-gray-500">Kode tidak memiliki batas masa berlaku.</div>
                    @endif
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="text-sm text-gray-500">Dibuat Pada</div>
                    <div class="mt-2 text-lg font-semibold text-gray-900">{{ $invitationCode->created_at->format('d M Y, H:i') }}</div>
                    <div class="mt-1 text-sm text-gray-500">Terakhir diperbarui: {{ $invitationCode->updated_at->diffForHumans() }}</div>
                </div>
            </div>

            <!-- Details Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Informasi Pembuat -->
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 lg:col-span-1">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Informasi Pembuat
                    </h3>
                    @if($invitationCode->user)
                        <div class="space-y-2 text-sm text-gray-600">
                            <div>
                                <span class="font-medium text-gray-900">Nama Lengkap:</span>
                                    <div>{{ $invitationCode->user->full_name }}</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">Username:</span>
                                    <div>{{ $invitationCode->user->username }}</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">Role:</span>
                                    <div class="capitalize">{{ $invitationCode->user->role }}</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">Dibuat Pada:</span>
                                    <div>{{ $invitationCode->user->created_at->format('d M Y') }}</div>
                            </div>
                        </div>
                    @else
                        <div class="text-sm text-gray-500 italic">
                            Pengguna pembuat tidak ditemukan. Kemungkinan akun sudah dihapus.
                        </div>
                    @endif
                </div>

                <!-- Analisis & Catatan -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Ringkasan Status
                        </h3>
                        <ul class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>
                                    Kode ini telah digunakan sebanyak <span class="font-semibold text-gray-900">{{ $invitationCode->used_count }}</span> kali
                                    dan dapat digunakan hingga <span class="font-semibold text-gray-900">{{ $invitationCode->max_usage }}</span> kali.
                                </span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>
                                    {{ $invitationCode->is_active ? 'Kode saat ini aktif dan dapat dibagikan.' : 'Kode saat ini non-aktif. Aktifkan kembali jika ingin digunakan.' }}
                                </span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>
                                    {{ $invitationCode->expires_at
                                        ? ($isExpired
                                            ? 'Kadaluarsa sejak ' . $invitationCode->expires_at->diffForHumans() . '.'
                                            : 'Akan kadaluarsa ' . $invitationCode->expires_at->diffForHumans() . '.')
                                        : 'Tidak memiliki masa kadaluarsa.' }}
                                </span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>
                                    Sisa penggunaan tersedia: <span class="font-semibold text-gray-900">{{ $remainingUses }}</span> kali.
                                </span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"></path>
                            </svg>
                            Detail Teknis
                        </h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm text-gray-600">
                            <div>
                                <dt class="font-medium text-gray-900">ID Internal</dt>
                                <dd>#{{ $invitationCode->id }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-900">Status</dt>
                                <dd>{{ $invitationCode->is_active ? 'Aktif' : 'Non-aktif' }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-900">Digunakan Oleh</dt>
                                <dd>{{ $invitationCode->used_count }} pengguna</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-900">Sisa Batas Penggunaan</dt>
                                <dd>{{ $remainingUses }} kali</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-900">Dibuat Tanggal</dt>
                                <dd>{{ $invitationCode->created_at->format('d M Y, H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-900">Terakhir Diperbarui</dt>
                                <dd>{{ $invitationCode->updated_at->format('d M Y, H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
