<x-admin-layout>
    <x-slot name="title">User Detail</x-slot>
    <div class="container mx-auto px-4 py-6 max-w-2xl">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold">User Detail</h1>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:underline">Back</a>
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
                    <p class="text-sm text-gray-500">@{{ $user->username }}</p>
                    <p class="text-xs mt-1"><span class="px-2 py-1 rounded text-white {{ $user->role==='admin' ? 'bg-purple-600':'bg-blue-600' }}">{{ ucfirst($user->role) }}</span></p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="font-medium">Level</p>
                    <p>{{ $user->level }}</p>
                </div>
                <div>
                    <p class="font-medium">Balance</p>
                    <p>Rp {{ number_format($user->balance,0,',','.') }}</p>
                </div>
                <div>
                    <p class="font-medium">Created At</p>
                    <p>{{ $user->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <div>
                    <p class="font-medium">Updated At</p>
                    <p>{{ $user->updated_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>
            <div class="flex space-x-2 pt-4">
                <a href="{{ route('admin.users.edit', $user) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Edit</a>
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user?');">
                    @csrf
                    @method('DELETE')
                    <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Delete</button>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
