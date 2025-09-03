@php /** @var \App\Models\Kyc $kyc */ @endphp
<x-admin-layout>
    <x-slot name="title">KYC Snapshot #{{ $kyc->id }}</x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Snapshot KYC #{{ $kyc->id }}</h2>
                <p class="text-sm text-gray-500">Diverifikasi {{ $kyc->verified_at?->format('d M Y H:i') }} oleh {{ $kyc->verifier?->full_name ?? '-' }}</p>
            </div>
            <div class="flex flex-wrap gap-2 items-center">
                <a href="{{ route('admin.kyc.snapshots.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Kembali</a>
                @if($kyc->request)
                    <a href="{{ route('admin.kyc.requests.show',$kyc->request) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Lihat Request Asal</a>
                @endif
            </div>
        </div>

        <div class="p-6 space-y-10">
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Data Pribadi (Snapshot)</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Nama Lengkap</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kyc->full_name }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">NIK</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md font-mono">{{ $kyc->nik }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Tempat / Tanggal Lahir</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kyc->birth_place }}, {{ optional($kyc->birth_date)->format('d M Y') }}</p>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-gray-600">Alamat</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kyc->address }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">RT / RW</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kyc->rt_rw }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Kelurahan</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kyc->village }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Kecamatan</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kyc->district }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Agama</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kyc->religion }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Status Perkawinan</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kyc->marital_status }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Pekerjaan</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kyc->occupation }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Kewarganegaraan</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $kyc->nationality }}</p>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Dokumen</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">KTP Depan</label>
                        @if($kyc->ktp_front_path)
                            <a href="{{ Storage::url($kyc->ktp_front_path) }}" target="_blank" class="inline-flex items-center px-3 py-2 text-xs bg-white border border-gray-300 rounded-md hover:bg-gray-50">Lihat</a>
                        @else
                            <p class="text-xs text-gray-500">-</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">KTP Belakang</label>
                        @if($kyc->ktp_back_path)
                            <a href="{{ Storage::url($kyc->ktp_back_path) }}" target="_blank" class="inline-flex items-center px-3 py-2 text-xs bg-white border border-gray-300 rounded-md hover:bg-gray-50">Lihat</a>
                        @else
                            <p class="text-xs text-gray-500">-</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Selfie + KTP</label>
                        @if($kyc->selfie_ktp_path)
                            <a href="{{ Storage::url($kyc->selfie_ktp_path) }}" target="_blank" class="inline-flex items-center px-3 py-2 text-xs bg-white border border-gray-300 rounded-md hover:bg-gray-50">Lihat</a>
                        @else
                            <p class="text-xs text-gray-500">-</p>
                        @endif
                    </div>
                </div>
            </div>

            @if($kyc->meta)
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Meta Data</h3>
                    <pre class="text-xs bg-gray-50 border border-gray-200 rounded-md p-4 overflow-auto max-h-64">{{ json_encode($kyc->meta, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
