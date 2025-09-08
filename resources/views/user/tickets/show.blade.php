<x-app-layout>
    @php($user = auth()->user())
    @php($initials = collect(explode(' ', trim($user->full_name ?: $user->username)))->filter()->take(2)->map(fn($p)=> strtoupper(mb_substr($p,0,1)))->implode(''))
    <div class="min-h-screen bg-[#1a1d21] text-neutral-100">
        <!-- Header Section -->
        <div class="sticky top-0 z-40 backdrop-blur bg-[#1f2226]/95 border-b border-neutral-800/70">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="py-4">
                    <div class="flex items-center space-x-3">
                        <!-- Back Button -->
                        <a href="{{ route('user.tickets.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-neutral-700 text-neutral-400 hover:text-white hover:bg-neutral-700 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        
                        <!-- Profile Photo with Badge -->
                        <div class="relative">
                            @if($user->photo_url)
                                <div class="w-12 h-12 rounded-full p-0.5 bg-gradient-to-br from-[#FE2C55] to-[#25F4EE]">
                                    <img src="{{ $user->photo_url }}" alt="Avatar" class="w-full h-full rounded-full object-cover ring-1 ring-black/40" />
                                </div>
                            @else
                                <div class="w-12 h-12 rounded-full flex items-center justify-center bg-gradient-to-br from-[#FE2C55] to-[#25F4EE] p-0.5">
                                    <div class="w-full h-full rounded-full bg-black flex items-center justify-center text-sm font-semibold tracking-wide">{{ $initials }}</div>
                                </div>
                            @endif
                            <!-- Badge on Profile -->
                            <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center bg-[#FE2C55] text-white text-xs font-bold shadow-[0_0_0_2px_#000]">🎫</div>
                        </div>
                        
                        <div>
                            <h1 class="text-xl font-semibold">{{ auth()->user()->full_name }}</h1>
                            <!-- Page Title -->
                            <div class="flex items-center space-x-6 mt-1">
                                <div class="text-left">
                                    <span class="text-sm font-semibold">Tiket #{{ $ticket->ticket_number }}</span>
                                    <p class="text-[10px] uppercase tracking-wide text-neutral-400 mt-0.5">{{ ucfirst($ticket->category) }} - {{ ucfirst($ticket->priority) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="max-w-4xl mx-auto">
                <!-- Ticket Header Card -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] overflow-hidden mb-6">
                    <div class="h-1 w-full bg-gradient-to-r from-[#fe2c55] via-[#fe2c55]/40 to-[#25f4ee]"></div>
                    
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-4">
                                    <h1 class="text-xl font-semibold text-neutral-100">{{ $ticket->title }}</h1>
                                    
                                    @if($ticket->status === 'open')
                                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-blue-500/15 text-blue-300 border border-blue-600/30">
                                            Terbuka
                                        </span>
                                    @elseif($ticket->status === 'in_progress')
                                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-purple-500/15 text-purple-300 border border-purple-600/30">
                                            Sedang Diproses
                                        </span>
                                    @elseif($ticket->status === 'resolved')
                                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-500/15 text-green-300 border border-green-600/30">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-gray-500/15 text-gray-300 border border-gray-600/30">
                                            Ditutup
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="prose prose-invert max-w-none">
                                    <p class="text-neutral-300 leading-relaxed">{{ $ticket->description }}</p>
                                </div>
                                
                                <!-- Attachments -->
                                @if($ticket->attachments && count($ticket->attachments) > 0)
                                    <div class="mt-4">
                                        <h4 class="text-sm font-medium text-neutral-200 mb-2">Lampiran:</h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            @foreach($ticket->attachments as $index => $attachment)
                                                <a href="{{ route('user.tickets.download', [$ticket, 'ticket', $index]) }}" 
                                                   class="inline-flex items-center p-3 bg-neutral-800 rounded-lg border border-neutral-700 hover:bg-neutral-700 transition">
                                                    <svg class="w-5 h-5 text-[#25F4EE] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                    </svg>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-neutral-200 truncate">{{ $attachment['name'] }}</p>
                                                        <p class="text-xs text-neutral-400">{{ number_format($attachment['size'] / 1024, 1) }} KB</p>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="ml-6 text-right">
                                <div class="space-y-2">
                                    @if($ticket->priority === 'urgent')
                                        <span class="inline-block px-3 py-1 text-sm font-medium rounded-full bg-red-500/15 text-red-300 border border-red-600/30">
                                            Mendesak
                                        </span>
                                    @elseif($ticket->priority === 'high')
                                        <span class="inline-block px-3 py-1 text-sm font-medium rounded-full bg-orange-500/15 text-orange-300 border border-orange-600/30">
                                            Tinggi
                                        </span>
                                    @elseif($ticket->priority === 'medium')
                                        <span class="inline-block px-3 py-1 text-sm font-medium rounded-full bg-yellow-500/15 text-yellow-300 border border-yellow-600/30">
                                            Sedang
                                        </span>
                                    @else
                                        <span class="inline-block px-3 py-1 text-sm font-medium rounded-full bg-green-500/15 text-green-300 border border-green-600/30">
                                            Rendah
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="mt-4 text-sm text-neutral-400">
                                    <p>Dibuat: {{ $ticket->created_at->format('d M Y, H:i') }}</p>
                                    @if($ticket->assignedTo)
                                        <p>Ditangani: {{ $ticket->assignedTo->full_name }}</p>
                                    @endif
                                    @if($ticket->resolved_at)
                                        <p>Diselesaikan: {{ $ticket->resolved_at->format('d M Y, H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-[#25f4ee] via-[#25f4ee]/40 to-[#fe2c55]"></div>
                    
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-neutral-100 mb-6">Percakapan</h2>
                        
                        <!-- Comments List -->
                        <div class="space-y-6">
                            @forelse($ticket->publicComments as $comment)
                                <div class="flex space-x-4">
                                    <!-- Avatar -->
                                    <div class="flex-shrink-0">
                                        @if($comment->user->photo_url)
                                            <img src="{{ $comment->user->photo_url }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover border-2 border-neutral-700" />
                                        @else
                                            @php($commentInitials = collect(explode(' ', trim($comment->user->full_name ?: $comment->user->username)))->filter()->take(2)->map(fn($p)=> strtoupper(mb_substr($p,0,1)))->implode(''))
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gradient-to-br from-[#FE2C55] to-[#25F4EE] text-white text-sm font-semibold">
                                                {{ $commentInitials }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Comment Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="bg-neutral-800 rounded-lg p-4 border border-neutral-700">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="text-sm font-medium text-neutral-200">{{ $comment->user->full_name }}</h4>
                                                <span class="text-xs text-neutral-400">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="prose prose-invert prose-sm max-w-none">
                                                <p class="text-neutral-300">{{ $comment->comment }}</p>
                                            </div>
                                            
                                            <!-- Comment Attachments -->
                                            @if($comment->attachments && count($comment->attachments) > 0)
                                                <div class="mt-3 pt-3 border-t border-neutral-700">
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                        @foreach($comment->attachments as $index => $attachment)
                                                            <a href="{{ route('user.tickets.download', [$ticket, 'comment', $comment->id]) }}" 
                                                               class="inline-flex items-center p-2 bg-neutral-700 rounded border border-neutral-600 hover:bg-neutral-600 transition text-sm">
                                                                <svg class="w-4 h-4 text-[#25F4EE] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                                </svg>
                                                                <span class="text-neutral-200 truncate">{{ $attachment['name'] }}</span>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-gradient-to-br from-[#FE2C55]/20 to-[#25F4EE]/20 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-neutral-400">Belum ada percakapan. Mulai diskusi dengan menambahkan komentar.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Add Comment Form -->
                        @if(in_array($ticket->status, ['open', 'in_progress']))
                            <div class="mt-8 pt-6 border-t border-neutral-700">
                                <h3 class="text-base font-medium text-neutral-200 mb-4">Tambahkan Balasan</h3>
                                
                                <form action="{{ route('user.tickets.comment', $ticket) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                    @csrf
                                    
                                    <div>
                                        <textarea name="comment" rows="4" required
                                                  class="w-full px-4 py-3 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-100 placeholder-neutral-400 focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent"
                                                  placeholder="Tulis balasan Anda..."></textarea>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-neutral-200 mb-2">Lampiran (Opsional)</label>
                                        <input type="file" name="attachments[]" multiple
                                               class="w-full px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-100 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-[#FE2C55] file:text-white hover:file:bg-[#FE2C55]/80 focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent"
                                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                    </div>
                                    
                                    <div class="flex justify-end">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] text-white text-sm font-medium rounded-lg hover:shadow-lg transition-all duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                            </svg>
                                            Kirim Balasan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="mt-8 pt-6 border-t border-neutral-700">
                                <div class="bg-neutral-800 rounded-lg p-4 border border-neutral-700">
                                    <div class="flex items-center space-x-2 text-neutral-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 0h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        <span class="text-sm">Tiket ini sudah {{ $ticket->status === 'resolved' ? 'diselesaikan' : 'ditutup' }}. Tidak dapat menambahkan balasan.</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
