@php /** @var \App\Models\KycRequest $kycRequest */ @endphp
<x-admin-layout>
    <x-slot name="title">KYC Request #{{ $kycRequest->id }}</x-slot>

    @php
        $statusColors = [
            'pending' => 'bg-gray-100 text-gray-800',
            'review' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
        ];
    @endphp

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Permintaan KYC #{{ $kycRequest->id }}</h2>
                <p class="text-sm text-gray-500">Dibuat {{ $kycRequest->created_at->format('d M Y H:i') }}</p>
            </div>
            <div class="flex flex-wrap gap-2 items-center">
                <a href="{{ route('admin.kyc.requests.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Kembali</a>
                @if(in_array($kycRequest->status_kyc,['pending']))
                    <form method="POST" action="{{ route('admin.kyc.requests.start-review',$kycRequest) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Mulai Review</button>
                    </form>
                @endif
                @if(in_array($kycRequest->status_kyc,['pending','review']))
                    <form method="POST" action="{{ route('admin.kyc.requests.approve',$kycRequest) }}" class="inline" onsubmit="return confirm('Approve KYC ini?');">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Approve</button>
                    </form>
                    <button type="button" onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Reject</button>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="p-6 space-y-10">
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Status & Review</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status Saat Ini</label>
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $statusColors[$kycRequest->status_kyc] ?? 'bg-gray-100 text-gray-800' }}">{{ $kycRequest->statusLabel() }}</span>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Direview Oleh</label>
                        <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kycRequest->reviewer?->full_name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Waktu Review</label>
                        <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kycRequest->reviewed_at?->format('d M Y H:i') ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Data Pribadi</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Nama Lengkap</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kycRequest->full_name }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">NIK</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md font-mono">{{ $kycRequest->nik }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Tempat / Tanggal Lahir</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kycRequest->birth_place }}, {{ optional($kycRequest->birth_date)->format('d M Y') }}</p>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-gray-600">Alamat</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kycRequest->address }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">RT / RW</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kycRequest->rt_rw }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Kelurahan</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kycRequest->village }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Kecamatan</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kycRequest->district }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Agama</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kycRequest->religion }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Status Perkawinan</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kycRequest->marital_status }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Pekerjaan</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kycRequest->occupation }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Kewarganegaraan</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kycRequest->nationality }}</p>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Dokumen</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">KTP Depan</label>
                        @if($kycRequest->ktp_front_path)
                            <a href="{{ Storage::url($kycRequest->ktp_front_path) }}" target="_blank" class="inline-flex items-center px-3 py-2 text-xs bg-white border border-gray-300 rounded-md hover:bg-gray-50">Lihat</a>
                        @else
                            <p class="text-xs text-gray-500">-</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">KTP Belakang</label>
                        @if($kycRequest->ktp_back_path)
                            <a href="{{ Storage::url($kycRequest->ktp_back_path) }}" target="_blank" class="inline-flex items-center px-3 py-2 text-xs bg-white border border-gray-300 rounded-md hover:bg-gray-50">Lihat</a>
                        @else
                            <p class="text-xs text-gray-500">-</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Selfie + KTP</label>
                        @if($kycRequest->selfie_ktp_path)
                            <a href="{{ Storage::url($kycRequest->selfie_ktp_path) }}" target="_blank" class="inline-flex items-center px-3 py-2 text-xs bg-white border border-gray-300 rounded-md hover:bg-gray-50">Lihat</a>
                        @else
                            <p class="text-xs text-gray-500">-</p>
                        @endif
                    </div>
                </div>
            </div>

            @if($kycRequest->admin_notes)
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Catatan Admin</h3>
                    <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kycRequest->admin_notes }}</p>
                </div>
            @endif

            @if($kycRequest->kyc && $kycRequest->status_kyc==='approved')
                <div class="bg-green-50 border border-green-200 rounded-md p-4">
                    <p class="text-xs text-green-700">Snapshot KYC final telah dibuat (ID: {{ $kycRequest->kyc->id }}).</p>
                    <a href="{{ route('admin.kyc.snapshots.show',$kycRequest->kyc) }}" class="inline-block mt-2 text-xs font-medium text-green-700 hover:text-green-900 underline">Lihat Snapshot</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white w-full max-w-md rounded-lg shadow-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Tolak KYC</h2>
            <form method="POST" action="{{ route('admin.kyc.requests.reject',$kycRequest) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Catatan (opsional)</label>
                    <textarea name="admin_notes" rows="4" class="w-full rounded-md border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Alasan ditolak / koreksi..."></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="px-4 py-2 text-sm rounded-md border bg-white hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm rounded-md bg-yellow-600 text-white font-medium hover:bg-yellow-700">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
