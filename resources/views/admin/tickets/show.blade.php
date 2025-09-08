<x-admin-layout>
    <x-slot name="title">Detail Tiket #{{ $ticket->ticket_number }}</x-slot>

    <div class="space-y-6">
        <!-- Back Button -->
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.tickets.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Tiket
            </a>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Ticket Details -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-start justify-between">
                            <div>
                                <h1 class="text-xl font-semibold text-gray-900">{{ $ticket->title }}</h1>
                                <p class="text-sm text-gray-500 mt-1">Tiket #{{ $ticket->ticket_number }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($ticket->status === 'open')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Terbuka
                                    </span>
                                @elseif($ticket->status === 'in_progress')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Sedang Diproses
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
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4">
                        <div class="prose max-w-none">
                            <p class="text-gray-700 leading-relaxed">{{ $ticket->description }}</p>
                        </div>

                        <!-- Ticket Attachments -->
                        @if($ticket->attachments && count($ticket->attachments) > 0)
                            <div class="mt-6">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Lampiran Tiket:</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($ticket->attachments as $index => $attachment)
                                        <a href="{{ route('admin.tickets.download', [$ticket, 'ticket', $index]) }}" 
                                           class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                                            <svg class="w-5 h-5 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                            </svg>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment['name'] }}</p>
                                                <p class="text-xs text-gray-500">{{ number_format($attachment['size'] / 1024, 1) }} KB</p>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Ticket Info -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <dt class="font-medium text-gray-500">Kategori</dt>
                                    <dd class="mt-1 text-gray-900">{{ ucfirst($ticket->category) }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-500">Dibuat</dt>
                                    <dd class="mt-1 text-gray-900">{{ $ticket->created_at->format('d M Y, H:i') }}</dd>
                                </div>
                                @if($ticket->resolved_at)
                                    <div>
                                        <dt class="font-medium text-gray-500">Diselesaikan</dt>
                                        <dd class="mt-1 text-gray-900">{{ $ticket->resolved_at->format('d M Y, H:i') }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Comments/Conversation -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Percakapan</h2>
                    </div>

                    <div class="px-6 py-4">
                        @if($ticket->comments->count() > 0)
                            <div class="space-y-6">
                                @foreach($ticket->comments as $comment)
                                    <div class="flex space-x-4">
                                        <!-- Avatar -->
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-{{ $comment->user->role === 'admin' ? 'blue' : 'green' }}-500 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-bold text-white">{{ strtoupper(substr($comment->user->full_name, 0, 1)) }}</span>
                                            </div>
                                        </div>

                                        <!-- Comment Content -->
                                        <div class="flex-1 min-w-0">
                                            <div class="bg-gray-50 rounded-lg p-4 {{ $comment->is_internal ? 'border-l-4 border-orange-400' : '' }}">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div class="flex items-center space-x-2">
                                                        <h4 class="text-sm font-medium text-gray-900">{{ $comment->user->full_name }}</h4>
                                                        @if($comment->user->role === 'admin')
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                                Admin
                                                            </span>
                                                        @endif
                                                        @if($comment->is_internal)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                                                Internal
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                                </div>
                                                <div class="prose prose-sm max-w-none">
                                                    <p class="text-gray-700">{{ $comment->comment }}</p>
                                                </div>

                                                <!-- Comment Attachments -->
                                                @if($comment->attachments && count($comment->attachments) > 0)
                                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                            @foreach($comment->attachments as $index => $attachment)
                                                                <a href="{{ route('admin.tickets.download', [$ticket, 'comment', $comment->id]) }}" 
                                                                   class="flex items-center p-2 bg-white rounded border border-gray-200 hover:bg-gray-50 transition text-sm">
                                                                    <svg class="w-4 h-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                                    </svg>
                                                                    <span class="text-gray-700 truncate">{{ $attachment['name'] }}</span>
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4-.8L3 20l1.22-2.44A7.793 7.793 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <p class="text-gray-500">Belum ada percakapan</p>
                            </div>
                        @endif

                        <!-- Add Comment Form -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <h3 class="text-base font-medium text-gray-900 mb-4">Tambahkan Balasan</h3>
                            
                            <form action="{{ route('admin.tickets.comment', $ticket) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                
                                <div>
                                    <textarea name="comment" rows="4" required
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                                              placeholder="Tulis balasan Anda..."></textarea>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <label class="flex items-center">
                                            <input type="checkbox" name="is_internal" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-600">Komentar internal (tidak terlihat oleh user)</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Lampiran (Opsional)</label>
                                    <input type="file" name="attachments[]" multiple
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                </div>
                                
                                <div class="flex justify-end">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                                        Kirim Balasan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- User Info -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informasi User</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                                <span class="text-sm font-bold text-white">{{ strtoupper(substr($ticket->user->full_name, 0, 2)) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $ticket->user->full_name }}</p>
                                <p class="text-sm text-gray-500">{{ $ticket->user->email }}</p>
                            </div>
                        </div>
                        <dl class="space-y-2 text-sm">
                            <div>
                                <dt class="font-medium text-gray-500">Username</dt>
                                <dd class="text-gray-900">{{ $ticket->user->username }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Level</dt>
                                <dd class="text-gray-900">{{ $ticket->user->level }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Saldo</dt>
                                <dd class="text-gray-900">Rp {{ number_format($ticket->user->balance) }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Bergabung</dt>
                                <dd class="text-gray-900">{{ $ticket->user->created_at->format('d M Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Aksi</h3>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <!-- Assign Admin -->
                        <div>
                            <form action="{{ route('admin.tickets.assign', $ticket) }}" method="POST">
                                @csrf
                                <label class="block text-sm font-medium text-gray-700 mb-2">Assign ke Admin</label>
                                <div class="flex space-x-2">
                                    <select name="assigned_to" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Pilih Admin</option>
                                        @foreach($admins as $admin)
                                            <option value="{{ $admin->id }}" {{ $ticket->assigned_to == $admin->id ? 'selected' : '' }}>
                                                {{ $admin->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition">
                                        Assign
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Update Status -->
                        <div>
                            <form action="{{ route('admin.tickets.update-status', $ticket) }}" method="POST">
                                @csrf
                                <label class="block text-sm font-medium text-gray-700 mb-2">Update Status</label>
                                <div class="space-y-2">
                                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Terbuka</option>
                                        <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>Sedang Diproses</option>
                                        <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Selesai</option>
                                        <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Ditutup</option>
                                    </select>
                                    <textarea name="admin_notes" rows="2" placeholder="Catatan admin (opsional)" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"></textarea>
                                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition">
                                        Update Status
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
