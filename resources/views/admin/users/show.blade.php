<x-admin-layout>
    <x-slot name="title">Detail Pengguna</x-slot>
    <div class="container mx-auto px-4 py-6 max-w-2xl">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold">Detail Pengguna</h1>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:underline">Kembali</a>
        </div>
        <div class="bg-white rounded shadow p-6 space-y-4">
            <div class="flex items-center space-x-4">
                @if($user->photo_url)
                    <img src="{{ $user->photo_url }}" class="h-20 w-20 rounded object-cover" alt="avatar">
                @else
                    <div class="h-20 w-20 rounded bg-gray-200 flex items-center justify-center text-gray-500">N/A</div>
                @endif
                <div>
                    <p class="text-lg font-medium">{{ $user->full_name }}</p>
                    <p class="text-sm text-gray-500">@ {{ $user->username }}</p>
                    <div class="flex items-center space-x-2 mt-1">
                        <span class="px-2 py-1 rounded text-white text-xs {{ $user->role==='admin' ? 'bg-purple-600':'bg-blue-600' }}">{{ ucfirst($user->role) }}</span>
                        @if($user->isSeller())
                            <span class="px-2 py-1 rounded bg-green-100 text-green-800 text-xs font-medium">
                                <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Seller
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="font-medium">Level</p>
                    <p>{{ $user->level }}</p>
                </div>
                <div>
                    <p class="font-medium">Saldo</p>
                    <p>Rp {{ number_format($user->balance,0,',','.') }}</p>
                </div>
                @if($user->isSeller() && $user->sellerInfo)
                    <div>
                        <p class="font-medium">Nama Toko</p>
                        <p>{{ $user->sellerInfo->store_name }}</p>
                    </div>
                    <div>
                        <p class="font-medium">Status Seller</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $user->sellerInfo->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->sellerInfo->is_active ? 'Aktif' : 'Non-aktif' }}
                        </span>
                    </div>
                @endif
                <div>
                    <p class="font-medium">Dibuat Pada</p>
                    <p>{{ $user->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <div>
                    <p class="font-medium">Diperbarui Pada</p>
                    <p>{{ $user->updated_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>
            <div class="flex space-x-2 pt-4">
                <a href="{{ route('admin.users.edit', $user) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Ubah</a>
                @if($user->isSeller() && $user->sellerInfo)
                    <a href="{{ route('admin.showcases.user-showcase', $user->id) }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Lihat Toko</a>
                @endif
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus pengguna ini?');">
                    @csrf
                    @method('DELETE')
                    <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
