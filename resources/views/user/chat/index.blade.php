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
                    <a href="#" class="flex items-center justify-between py-3 px-4 rounded-lg bg-neutral-800/40 border border-neutral-700/50 hover:border-[#FE2C55]/30 hover:bg-neutral-800/60 transition group">
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
                    </a>

                    <!-- WhatsApp Customer Service -->
                    <a href="#" class="flex items-center justify-between py-3 px-4 rounded-lg bg-neutral-800/40 border border-neutral-700/50 hover:border-emerald-500/30 hover:bg-neutral-800/60 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500/20 to-green-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-neutral-300 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-100 group-hover:text-white">WhatsApp CS</p>
                                <p class="text-xs text-neutral-400">Hubungi via WhatsApp</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-neutral-400 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <!-- Email Support -->
                    <a href="#" class="flex items-center justify-between py-3 px-4 rounded-lg bg-neutral-800/40 border border-neutral-700/50 hover:border-blue-500/30 hover:bg-neutral-800/60 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500/20 to-cyan-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-neutral-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-100 group-hover:text-white">Email Support</p>
                                <p class="text-xs text-neutral-400">Kirim email ke support kami</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-neutral-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

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
</x-app-layout>
