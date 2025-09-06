<x-app-layout>
    @php($user = auth()->user())
    @php($initials = collect(explode(' ', trim($user->full_name ?: $user->username)))->filter()->take(2)->map(fn($p)=> strtoupper(mb_substr($p,0,1)))->implode(''))
    <div class="min-h-screen bg-[#1a1d21] text-neutral-100">
        <!-- Header Section with User Info -->
        <div class="sticky top-0 z-40 backdrop-blur bg-[#1f2226]/95 border-b border-neutral-800/70">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
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
                                <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center bg-[#FE2C55] text-white text-xs font-bold shadow-[0_0_0_2px_#000]">+</div>
                            </div>
                            <div>
                                @if(auth()->user()->isSeller() && auth()->user()->sellerInfo)
                                    <h1 class="text-xl font-semibold">{{ auth()->user()->full_name }}</h1>
                                    <p class="text-sm font-medium bg-clip-text text-transparent bg-gradient-to-r from-[#FE2C55] to-[#25F4EE]">{{ auth()->user()->sellerInfo->store_name }}</p>
                                @else
                                    <h1 class="text-xl font-semibold">{{ auth()->user()->full_name }}</h1>
                                @endif
                                <!-- Page Title -->
                                <div class="flex items-center space-x-6 mt-2">
                                    <div class="text-left">
                                        <span class="text-sm font-semibold">Customer Service</span>
                                        <p class="text-[10px] uppercase tracking-wide text-neutral-400 mt-0.5">Chat & Bantuan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-end space-y-1">
                            <!-- Status Badge -->
                            <div class="flex items-center space-x-1 px-2 py-1 rounded-md bg-emerald-500/15 text-emerald-400 text-xs font-medium">
                                <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                                <span>Online</span>
                            </div>
                            @if(auth()->user()->isSeller())
                                <!-- Seller Badge -->
                                <div class="flex items-center space-x-1 px-2 py-1 rounded-md bg-orange-500/15 text-orange-400 text-xs font-medium">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    <span>Seller</span>
                                </div>
                            @endif
                            <!-- Level Badge -->
                            <div class="flex items-center space-x-1 px-2 py-1 rounded-md bg-blue-500/15 text-blue-400 text-xs font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <span>Lv {{ auth()->user()->level }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <!-- Chat Options Card -->
            <div class="rounded-xl mb-6 border border-[#2c3136] bg-[#23272b] shadow-sm relative overflow-hidden">
                <div class="h-1 w-full bg-gradient-to-r from-[#fe2c55] via-[#fe2c55]/40 to-[#25f4ee]"></div>
                
                <!-- Customer Service Section -->
                <div class="p-5 pb-0">
                    <h3 class="text-xs font-semibold mb-4 text-neutral-400 uppercase tracking-wide">Customer Service</h3>
                </div>
                
                <div class="px-5 space-y-1">
                    <!-- Live Chat dengan CS -->
                    <button id="start-chat-btn" class="w-full flex items-center justify-between py-3 px-4 rounded-lg bg-neutral-800/40 border border-neutral-700/50 hover:border-[#FE2C55]/30 hover:bg-neutral-800/60 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#FE2C55]/20 to-[#25F4EE]/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-neutral-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4-.8L3 20l1.22-2.44A7.793 7.793 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-100 group-hover:text-white">Live Chat</p>
                                <p class="text-xs text-neutral-400">Chat langsung dengan CS kami</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                            <svg class="w-4 h-4 text-neutral-400 group-hover:text-[#FE2C55]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </button>

                    <!-- Ticket Support -->
                    <a href="#" class="flex items-center justify-between py-3 px-4 rounded-lg bg-neutral-800/40 border border-neutral-700/50 hover:border-amber-500/30 hover:bg-neutral-800/60 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-500/20 to-orange-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-neutral-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-100 group-hover:text-white">Buat Tiket</p>
                                <p class="text-xs text-neutral-400">Buat tiket support baru</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-neutral-400 group-hover:text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>

                <!-- Divider -->
                <div class="mx-5 my-6">
                    <div class="border-t border-neutral-700/60"></div>
                </div>

                <!-- FAQ & Help Section -->
                <div class="px-5 pb-0">
                    <h3 class="text-xs font-semibold mb-4 text-neutral-400 uppercase tracking-wide">Bantuan & FAQ</h3>
                </div>

                <div class="px-5 pb-5 space-y-1">
                    <!-- FAQ -->
                    <a href="#" class="flex items-center justify-between py-3 px-4 rounded-lg bg-neutral-800/40 border border-neutral-700/50 hover:border-purple-500/30 hover:bg-neutral-800/60 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500/20 to-pink-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-neutral-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-100 group-hover:text-white">FAQ</p>
                                <p class="text-xs text-neutral-400">Pertanyaan yang sering diajukan</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-neutral-400 group-hover:text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <!-- Panduan Pengguna -->
                    <a href="#" class="flex items-center justify-between py-3 px-4 rounded-lg bg-neutral-800/40 border border-neutral-700/50 hover:border-indigo-500/30 hover:bg-neutral-800/60 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500/20 to-blue-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-neutral-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-100 group-hover:text-white">Panduan Pengguna</p>
                                <p class="text-xs text-neutral-400">Cara menggunakan platform</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-neutral-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Active Chat Rooms -->
            @if($chatRooms->count() > 0)
                <div class="rounded-xl mb-6 border border-[#2c3136] bg-[#23272b] shadow-sm relative overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-[#fe2c55] via-[#fe2c55]/40 to-[#25f4ee]"></div>
                    
                    <div class="p-5 pb-0">
                        <h3 class="text-xs font-semibold mb-4 text-neutral-400 uppercase tracking-wide">Chat Aktif</h3>
                    </div>
                    
                    <div class="px-5 pb-5 space-y-3">
                        @foreach($chatRooms as $chatRoom)
                            <a href="{{ route('user.chat.show', $chatRoom) }}" class="block p-4 rounded-lg bg-neutral-800/40 border border-neutral-700/50 hover:border-[#FE2C55]/30 hover:bg-neutral-800/60 transition group">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <h4 class="text-sm font-medium text-neutral-100 group-hover:text-white truncate">{{ $chatRoom->subject }}</h4>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $chatRoom->getStatusColor() }}">
                                                {{ ucfirst($chatRoom->status) }}
                                            </span>
                                        </div>
                                        
                                        @if($chatRoom->latestMessage)
                                            <p class="text-xs text-neutral-400 truncate mb-2">
                                                {{ $chatRoom->latestMessage->message ?: 'File attachment' }}
                                            </p>
                                        @endif
                                        
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $chatRoom->getPriorityColor() }}">
                                                    {{ ucfirst($chatRoom->priority) }}
                                                </span>
                                                @if($chatRoom->admin)
                                                    <span class="text-xs text-neutral-500">dengan {{ $chatRoom->admin->full_name ?? $chatRoom->admin->username }}</span>
                                                @endif
                                            </div>
                                            
                                            <div class="text-right">
                                                <p class="text-xs text-neutral-500">
                                                    {{ $chatRoom->last_message_at ? $chatRoom->last_message_at->diffForHumans() : $chatRoom->created_at->diffForHumans() }}
                                                </p>
                                                @if($chatRoom->unread_messages_count > 0)
                                                    <span class="inline-flex items-center justify-center w-5 h-5 bg-[#FE2C55] text-white text-xs font-bold rounded-full">
                                                        {{ $chatRoom->unread_messages_count }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="ml-3 flex-shrink-0">
                                        <svg class="w-4 h-4 text-neutral-400 group-hover:text-[#FE2C55]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                        
                        @if($chatRooms->hasPages())
                            <div class="mt-4">
                                {{ $chatRooms->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Status Info Card -->
            <div class="rounded-xl border border-[#2c3136] bg-[#23272b]/50 p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center">
                            <div class="w-3 h-3 bg-emerald-400 rounded-full animate-pulse"></div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-neutral-200">Customer Service Online</p>
                            <p class="text-xs text-neutral-400">Rata-rata respon: 2-5 menit</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-neutral-400">Jam Operasional</p>
                        <p class="text-sm font-medium text-neutral-200">24/7</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Chat Modal -->
    <div id="new-chat-modal" class="fixed inset-0 z-50 hidden">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
        
        <!-- Modal -->
        <div class="fixed inset-x-4 top-1/2 -translate-y-1/2 max-w-md mx-auto">
            <div class="bg-[#23272b] border border-[#2c3136] rounded-xl shadow-xl">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-neutral-100">Mulai Chat Baru</h3>
                        <button id="close-modal" class="p-2 rounded-lg hover:bg-neutral-800/50 transition">
                            <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form id="new-chat-form">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="subject" class="block text-sm font-medium text-neutral-300 mb-2">Subjek</label>
                                <input 
                                    type="text" 
                                    id="subject" 
                                    name="subject" 
                                    placeholder="Masalah apa yang ingin Anda tanyakan?"
                                    class="w-full px-3 py-2 rounded-lg bg-neutral-800/60 border border-neutral-700/50 focus:border-[#FE2C55]/50 focus:ring-2 focus:ring-[#FE2C55]/20 text-neutral-100 placeholder-neutral-400"
                                    required
                                    maxlength="255"
                                >
                            </div>
                            
                            <div>
                                <label for="priority" class="block text-sm font-medium text-neutral-300 mb-2">Prioritas</label>
                                <select 
                                    id="priority" 
                                    name="priority"
                                    class="w-full px-3 py-2 rounded-lg bg-neutral-800/60 border border-neutral-700/50 focus:border-[#FE2C55]/50 focus:ring-2 focus:ring-[#FE2C55]/20 text-neutral-100"
                                >
                                    <option value="low">Rendah</option>
                                    <option value="medium" selected>Sedang</option>
                                    <option value="high">Tinggi</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="message" class="block text-sm font-medium text-neutral-300 mb-2">Pesan</label>
                                <textarea 
                                    id="message" 
                                    name="message" 
                                    placeholder="Jelaskan masalah atau pertanyaan Anda..."
                                    class="w-full px-3 py-2 rounded-lg bg-neutral-800/60 border border-neutral-700/50 focus:border-[#FE2C55]/50 focus:ring-2 focus:ring-[#FE2C55]/20 text-neutral-100 placeholder-neutral-400 resize-none"
                                    rows="4"
                                    required
                                    maxlength="1000"
                                ></textarea>
                            </div>
                            
                            <div class="flex justify-end space-x-3">
                                <button 
                                    type="button" 
                                    id="cancel-btn"
                                    class="px-4 py-2 rounded-lg bg-neutral-700/50 hover:bg-neutral-600/50 text-neutral-300 transition"
                                >
                                    Batal
                                </button>
                                <button 
                                    type="submit" 
                                    id="submit-btn"
                                    class="px-4 py-2 rounded-lg bg-gradient-to-r from-[#FE2C55] to-[#FE2C55]/80 hover:from-[#FE2C55]/90 hover:to-[#FE2C55]/70 text-white transition disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    Mulai Chat
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        const csrfToken = '{{ csrf_token() }}';
        
        // DOM elements
        const startChatBtn = document.getElementById('start-chat-btn');
        const newChatModal = document.getElementById('new-chat-modal');
        const closeModal = document.getElementById('close-modal');
        const cancelBtn = document.getElementById('cancel-btn');
        const newChatForm = document.getElementById('new-chat-form');
        const submitBtn = document.getElementById('submit-btn');

        // Show modal
        startChatBtn.addEventListener('click', () => {
            newChatModal.classList.remove('hidden');
            document.getElementById('subject').focus();
        });

        // Hide modal
        function hideModal() {
            newChatModal.classList.add('hidden');
            newChatForm.reset();
        }

        closeModal.addEventListener('click', hideModal);
        cancelBtn.addEventListener('click', hideModal);

        // Close modal on backdrop click
        newChatModal.addEventListener('click', (e) => {
            if (e.target === newChatModal) {
                hideModal();
            }
        });

        // Handle form submission
        newChatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(newChatForm);
            
            // Disable button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Memproses...';
            
            try {
                const response = await fetch('{{ route("user.chat.create") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Redirect to new chat room
                    window.location.href = `/chat/${data.chat_room.id}`;
                } else {
                    alert('Gagal membuat chat. Silakan coba lagi.');
                }
            } catch (error) {
                console.error('Error creating chat:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
            
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.textContent = 'Mulai Chat';
        });

        // ESC key to close modal
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !newChatModal.classList.contains('hidden')) {
                hideModal();
            }
        });
    </script>
</x-app-layout>
