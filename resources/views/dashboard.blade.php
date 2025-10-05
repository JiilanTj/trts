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
                                <!-- User Stats -->
                                <div class="flex items-center space-x-6 mt-2">
                                    @if(auth()->user()->isSeller() && auth()->user()->sellerInfo)
                                        <!-- Seller Stats -->
                                        <div class="text-center">
                                            <span class="text-sm font-semibold">{{ auth()->user()->formatted_followers }}</span>
                                            <p class="text-[10px] uppercase tracking-wide text-neutral-400 mt-0.5">Mengikuti</p>
                                        </div>
                                        <div class="text-center">
                                            <span class="text-sm font-semibold">{{ auth()->user()->formatted_visitors }}</span>
                                            <p class="text-[10px] uppercase tracking-wide text-neutral-400 mt-0.5">Kunjungan</p>
                                        </div>
                                        <div class="text-center">
                                            <span class="text-sm font-semibold">{{ auth()->user()->credit_score ?? 0 }}</span>
                                            <p class="text-[10px] uppercase tracking-wide text-neutral-400 mt-0.5">Skor kredit</p>
                                        </div>
                                    @else
                                        <!-- Regular User Stats -->
                                        <div class="text-center">
                                            <span class="text-sm font-semibold">{{ auth()->user()->orders()->count() }}</span>
                                            <p class="text-[10px] uppercase tracking-wide text-neutral-400 mt-0.5">Order</p>
                                        </div>
                                        <div class="text-center">
                                            <span class="text-sm font-semibold">{{ auth()->user()->formatted_visitors }}</span>
                                            <p class="text-[10px] uppercase tracking-wide text-neutral-400 mt-0.5">Kunjungan</p>
                                        </div>
                                        <div class="text-center">
                                            <span class="text-sm font-semibold">{{ auth()->user()->formatted_followers }}</span>
                                            <p class="text-[10px] uppercase tracking-wide text-neutral-400 mt-0.5">Mengikuti</p>
                                        </div>
                                        <div class="text-center">
                                            <span class="text-sm font-semibold">{{ auth()->user()->credit_score ?? 0 }}</span>
                                            <p class="text-[10px] uppercase tracking-wide text-neutral-400 mt-0.5">Skor kredit</p>
                                        </div>
                        <div class="text-center">
                            <span class="text-sm font-semibold">{{ auth()->user()->credit_score ?? 0 }}</span>
                            <p class="text-[10px] uppercase tracking-wide text-neutral-400 mt-0.5">Skor kredit</p>
                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-end space-y-1">
                            <!-- Status Badge -->
                            <div class="flex items-center space-x-1 px-2 py-1 rounded-md bg-emerald-500/15 text-emerald-400 text-xs font-medium">
                                <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                                <span>Active</span>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                                <span>
                                    @if(auth()->user()->level == 6)
                                        Toko dari Mulut ke Mulut
                                    @else
                                        Bintang {{ auth()->user()->level }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <!-- Wallet Card (toned down) -->
            <div class="rounded-xl mb-6 border border-[#2c3136] bg-[#23272b] shadow-sm relative overflow-hidden">
                <div class="h-1 w-full bg-gradient-to-r from-[#fe2c55] via-[#fe2c55]/40 to-[#25f4ee]"></div>
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-base font-semibold mb-1 text-neutral-100">Dompet Balance</h3>
                            <p class="text-xs text-neutral-400 mb-4">Saldo Promosi: Rp0</p>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-[11px] tracking-wide text-neutral-500 font-medium mb-1 uppercase">Saldo</p>
                                    <p class="text-3xl font-bold text-neutral-50">Rp{{ number_format(auth()->user()->balance, 0, ',', '.') }}</p>
                                </div>
                                <button class="relative inline-flex items-center justify-center px-5 py-2 text-sm font-medium text-neutral-100 rounded-full transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-0 focus-visible:ring-[#fe2c55]/70 bg-[#2d3237] hover:bg-[#343a40] active:bg-[#3a4248]">
                                    <span class="absolute inset-0 rounded-full ring-1 ring-neutral-600/60"></span>
                                    <span class="relative">MEMOP...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Pintasan Cepat Component -->
            @include('components.quick-shortcuts')
            
            <!-- Level Progress Component -->
            @include('components.level-progress')
        </div>
    </div>

    <script>
        // Global function untuk paksa trigger PWA install
        window.triggerPWAInstall = function() {
            console.log('🚀 Paksa trigger PWA install');
            
            // Detect browser type
            const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
            const isAndroid = /Android/.test(navigator.userAgent);
            const isChrome = /Chrome/.test(navigator.userAgent) && !isSafari;
            
            console.log('Browser detected:', { isSafari, isIOS, isAndroid, isChrome });
            
            // For Safari/iOS - manual instruction
            if (isSafari || isIOS) {
                alert('📱 Install Aplikasi (Safari/iOS):\n\n1. Klik tombol "Share" (📤)\n2. Scroll ke bawah\n3. Pilih "Add to Home Screen"\n4. Klik "Add"\n\n✨ Aplikasi akan muncul di home screen!');
                return;
            }
            
            // For Chrome/Android - try automatic
            if (window.pwaDeferred) {
                console.log('✅ Using deferred prompt');
                window.pwaDeferred.prompt().then(function() {
                    console.log('📲 PWA install prompt shown');
                }).catch(function(err) {
                    console.log('❌ Error showing prompt:', err);
                    showManualInstructions();
                });
            } else {
                console.log('❌ No deferred prompt, showing manual instructions');
                showManualInstructions();
            }
        };
        
        function showManualInstructions() {
            const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
            const isAndroid = /Android/.test(navigator.userAgent);
            
            let instructions = '';
            
            if (isSafari || isIOS) {
                instructions = '📱 Safari/iOS:\n1. Klik tombol Share (📤)\n2. Pilih "Add to Home Screen"\n3. Klik "Add"';
            } else if (isAndroid) {
                instructions = '🤖 Android Chrome:\n1. Klik menu (⋮)\n2. Pilih "Add to Home Screen"\n3. Atau "Install App"';
            } else {
                instructions = '💻 Desktop:\n1. Klik menu browser (⋮)\n2. Pilih "Install [App Name]"\n3. Atau "Add to Home Screen"';
            }
            
            alert(`Install PWA Manual:\n\n${instructions}\n\n💡 Refresh halaman jika tidak ada opsi install`);
        }

        // Simplified PWA detection - hanya capture event, tidak auto-show popup
        let pwaDeferred = null;
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('🎉 PWA install tersedia!');
            e.preventDefault();
            pwaDeferred = e;
            window.pwaDeferred = e;
        });

        // Track jika sudah install
        window.matchMedia('(display-mode: standalone)').addEventListener('change', (e) => {
            if (e.matches) {
                console.log('📱 PWA berhasil diinstall!');
                localStorage.setItem('pwaInstalled','1');
            }
        });
    </script>
</x-app-layout>
