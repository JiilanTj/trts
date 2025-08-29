<x-admin-layout>
    <x-slot name="title">Dasbor</x-slot>

    @php
        // Fallback jika route tidak mengirimkan variabel (hanya role user)
        $totalUsers = $totalUsers ?? \App\Models\User::where('role','user')->count();
        $recentUsers = $recentUsers ?? \App\Models\User::where('role','user')->latest()->take(8)->get();
        // Fallback perhitungan pertumbuhan (untuk akses langsung tanpa variabel)
        if(!isset($userGrowthPercent) || !isset($userGrowthText)) {
            $currentMonthUsers = \App\Models\User::where('role','user')
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count();
            $lastMonthUsers = \App\Models\User::where('role','user')
                ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
                ->count();
            if ($lastMonthUsers === 0) {
                if ($currentMonthUsers === 0) {
                    $userGrowthPercent = 0.0;
                    $userGrowthText = '0%';
                } else {
                    $userGrowthPercent = 100.0;
                    $userGrowthText = '+100%';
                }
            } else {
                $userGrowthPercent = (($currentMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100;
                $userGrowthText = ($userGrowthPercent >= 0 ? '+' : '') . number_format($userGrowthPercent, 1, ',', '.') . '%';
            }
        }
    @endphp

    <!-- Kartu Statistik Dasbor -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Pengguna -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600 mb-1">Total Pengguna</p>
                    <p class="text-3xl font-bold text-slate-900">{{ number_format($totalUsers, 0, ',', '.') }}</p>
                    <p class="text-sm font-medium {{ $userGrowthPercent >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $userGrowthText }} dari bulan lalu</p>
                </div>
                <div class="p-3 rounded-xl bg-blue-50">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600 mb-1">Total Pendapatan</p>
                    <p class="text-3xl font-bold text-slate-900">Rp50M</p>
                    <p class="text-sm text-green-600 font-medium">+8% dari bulan lalu</p>
                </div>
                <div class="p-3 rounded-xl bg-green-50">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Sesi Aktif -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600 mb-1">Sesi Aktif</p>
                    <p class="text-3xl font-bold text-slate-900">12</p>
                    <p class="text-sm text-yellow-600 font-medium">Sedang aktif</p>
                </div>
                <div class="p-3 rounded-xl bg-purple-50">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Status Sistem -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600 mb-1">Status Sistem</p>
                    <p class="text-3xl font-bold text-green-600">Online</p>
                    <p class="text-sm text-green-600 font-medium">98.5% waktu aktif</p>
                </div>
                <div class="p-3 rounded-xl bg-orange-50">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Baris Grafik -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Aktivitas Pengguna -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-slate-900">Aktivitas Pengguna</h3>
                <span class="text-sm text-slate-500">(7 Hari Terakhir)</span>
            </div>
            <div class="h-80 bg-slate-50 rounded-xl flex items-center justify-center border border-slate-100">
                <div class="text-center">
                    <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="text-slate-500 font-medium">Placeholder Grafik</p>
                    <p class="text-sm text-slate-400">Grafik Aktivitas</p>
                </div>
            </div>
        </div>

        <!-- Tren Pendapatan -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-slate-900">Tren Pendapatan</h3>
                <span class="text-sm text-slate-500">(Bulanan)</span>
            </div>
            <div class="h-80 bg-slate-50 rounded-xl flex items-center justify-center border border-slate-100">
                <div class="text-center">
                    <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                    <p class="text-slate-500 font-medium">Placeholder Grafik</p>
                    <p class="text-sm text-slate-400">Grafik Pendapatan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pengguna Terbaru -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 mb-8">
        <div class="px-6 py-4 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Pengguna Terbaru</h3>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">Lihat Semua</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Pengguna</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Peran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Saldo</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Level</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Bergabung</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($recentUsers as $u)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-lg bg-slate-100 flex items-center justify-center overflow-hidden">
                                        @if($u->photo_url)
                                            <img src="{{ $u->photo_url }}" class="h-10 w-10 object-cover" alt="avatar">
                                        @else
                                            <span class="text-sm font-semibold text-slate-600">{{ strtoupper(substr($u->full_name,0,2)) }}</span>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-slate-900">{{ $u->full_name }}</div>
                                        <div class="text-sm text-slate-500">{{ '@' . $u->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $u->role==='admin' ? 'bg-red-100 text-red-800':'bg-green-100 text-green-800' }}">{{ ucfirst($u->role) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">Rp{{ number_format($u->balance,0,',','.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">Level {{ $u->level }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $u->created_at->locale('id')->diffForHumans() }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-3">
                                    <a href="{{ route('admin.users.show', $u) }}" class="text-blue-600 hover:text-blue-900">Lihat</a>
                                    <a href="{{ route('admin.users.edit', $u) }}" class="text-indigo-600 hover:text-indigo-900">Ubah</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500 text-sm">Belum ada pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Baris Bawah - Info Sistem & Aktivitas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Informasi Sistem -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-lg font-semibold text-slate-900 mb-6">Informasi Sistem</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between py-2">
                    <span class="text-sm font-medium text-slate-600">Status Server</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Online</span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-sm font-medium text-slate-600">Basis Data</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Terhubung</span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-sm font-medium text-slate-600">Cache</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-sm font-medium text-slate-600">Queue</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Berjalan</span>
                </div>
            </div>
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-lg font-semibold text-slate-900 mb-6">Aktivitas Terbaru</h3>
            <div class="space-y-4">
                <div class="flex items-start space-x-3">
                    <div class="w-3 h-3 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-slate-900">Pengguna baru terdaftar</p>
                        <p class="text-xs text-slate-500 mt-1">2 menit yang lalu</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-3 h-3 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-slate-900">Pembayaran diproses</p>
                        <p class="text-xs text-slate-500 mt-1">5 menit yang lalu</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-3 h-3 bg-orange-500 rounded-full mt-2 flex-shrink-0"></div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-slate-900">Cadangan sistem selesai</p>
                        <p class="text-xs text-slate-500 mt-1">1 jam yang lalu</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tindakan Cepat -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-lg font-semibold text-slate-900 mb-6">Tindakan Cepat</h3>
            <div class="space-y-3">
                <button class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    Tambah Pengguna Baru
                </button>
                <button class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                    Buat Laporan
                </button>
                <button class="w-full px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm font-medium">
                    Kirim Siaran
                </button>
                <button class="w-full px-4 py-3 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors text-sm font-medium">
                    Cadangan Sistem
                </button>
            </div>
        </div>
    </div>
</x-admin-layout>
