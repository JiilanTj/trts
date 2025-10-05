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

    <!-- PWA Install Prompt (Custom) -->
    <div x-data="pwaInstallPrompt()" x-show="visible" x-cloak class="fixed inset-0 z-[200] flex items-end sm:items-center justify-center">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="dismiss()"></div>
        <div class="relative w-full sm:max-w-sm mx-auto bg-[#1e2226] border border-neutral-700/70 rounded-t-2xl sm:rounded-2xl p-5 shadow-2xl shadow-black/40 animate-[fadeIn_.25s_ease]">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 rounded-xl overflow-hidden ring-1 ring-white/10 bg-[#121416] flex items-center justify-center">
                    <img src="/icons/icon-192.png" alt="TT Shop" class="w-11 h-11 object-contain" />
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold mb-1">Install Aplikasi</h3>
                    <p class="text-xs text-neutral-400 leading-relaxed">Tambah ke layar utama untuk akses lebih cepat & pengalaman seperti aplikasi native.</p>
                </div> 
                <button @click="dismiss()" class="text-neutral-400 hover:text-neutral-200 transition" aria-label="Tutup">&times;</button>
            </div>
            <div class="mt-4 flex items-center space-x-3">
                <button @click="install()" :disabled="installing" class="px-4 py-2 rounded-md text-sm font-medium bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] text-white shadow disabled:opacity-50 disabled:cursor-wait">Install</button>
                <button @click="later()" class="px-3 py-2 text-xs font-medium rounded-md bg-neutral-700/40 hover:bg-neutral-600/40 text-neutral-300">Nanti</button>
            </div>
            <template x-if="error">
                <p class="mt-3 text-xs text-rose-400" x-text="error"></p>
            </template>
        </div>
    </div>

    <script>
        // Global function untuk trigger PWA install
        window.triggerPWAInstall = function() {
            console.log('🚀 triggerPWAInstall dipanggil');
            
            // Debug info
            console.log('Debug PWA Info:', {
                isHttps: location.protocol === 'https:',
                isLocalhost: location.hostname === 'localhost' || location.hostname === '127.0.0.1',
                userAgent: navigator.userAgent,
                isStandalone: window.matchMedia('(display-mode: standalone)').matches,
                hasServiceWorker: 'serviceWorker' in navigator,
                hasDeferred: !!window.pwaDeferred
            });
            
            const pwaComponent = Alpine.$data(document.querySelector('[x-data*="pwaInstallPrompt"]'));
            if (pwaComponent) {
                console.log('🎯 Alpine component found, calling triggerInstall');
                pwaComponent.triggerInstall();
            } else {
                console.log('⚠️ PWA component not found, trying direct install...');
                // Fallback jika Alpine component belum ready
                if (window.pwaDeferred) {
                    console.log('✅ Using deferred prompt');
                    window.pwaDeferred.prompt();
                } else {
                    console.log('❌ No deferred prompt available');
                    
                    // Cek berbagai kondisi
                    let reason = 'Tidak diketahui';
                    if (location.protocol !== 'https:' && !['localhost', '127.0.0.1'].includes(location.hostname)) {
                        reason = 'PWA memerlukan HTTPS';
                    } else if (window.matchMedia('(display-mode: standalone)').matches) {
                        reason = 'App sudah berjalan dalam mode standalone';
                    } else if (!('serviceWorker' in navigator)) {
                        reason = 'Browser tidak mendukung Service Worker';
                    } else {
                        reason = 'Browser belum mendeteksi PWA atau sudah dismiss/install sebelumnya';
                    }
                    
                    alert(`Install PWA tidak tersedia.\n\nAlasan: ${reason}\n\nCoba:\n1. Refresh halaman\n2. Pastikan menggunakan HTTPS\n3. Tunggu beberapa detik lalu coba lagi`);
                }
            }
        };

        document.addEventListener('alpine:init', () => {
            Alpine.data('pwaInstallPrompt', () => ({
                deferred: null,
                visible: false,
                installing: false,
                error: null,
                init() {
                    console.log('🎬 PWA Component initialized');
                    
                    // Reset kondisi untuk testing
                    // localStorage.removeItem('pwaInstallPromptDismissed');
                    
                    // Jangan tampilkan jika sudah standalone / pernah dismiss
                    if (this.isStandalone()) {
                        console.log('🏠 App already in standalone mode');
                        return;
                    }
                    
                    if (localStorage.getItem('pwaInstalled') === '1') {
                        console.log('✅ PWA marked as installed in localStorage');
                        return;
                    }
                    
                    if (localStorage.getItem('pwaInstallPromptDismissed') === '1') {
                        console.log('🚫 PWA install prompt was dismissed before');
                        // Jangan return di sini, biarkan user bisa trigger manual
                    }
                    
                    window.addEventListener('beforeinstallprompt', (e) => {
                        console.log('🎉 beforeinstallprompt event received!');
                        e.preventDefault();
                        this.deferred = e;
                        window.pwaDeferred = e; // Store globally untuk fallback
                        
                        // Hanya auto-show jika belum pernah dismiss
                        if (localStorage.getItem('pwaInstallPromptDismissed') !== '1') {
                            // Tunda sedikit agar tidak mengganggu initial paint
                            setTimeout(() => { 
                                console.log('⏰ Auto-showing PWA prompt');
                                this.visible = true; 
                            }, 800);
                        }
                    });
                    
                    window.matchMedia('(display-mode: standalone)').addEventListener('change', (e) => {
                        if (e.matches) {
                            console.log('📱 App switched to standalone mode');
                            localStorage.setItem('pwaInstalled','1');
                            this.visible = false;
                        }
                    });
                },
                triggerInstall() {
                    console.log('🔥 triggerInstall called', { 
                        isStandalone: this.isStandalone(), 
                        hasDeferred: !!this.deferred 
                    });
                    
                    if (this.isStandalone()) {
                        alert('✅ Aplikasi sudah terinstall dan berjalan!');
                        return;
                    }
                    
                    if (this.deferred) {
                        console.log('📲 Showing PWA install prompt');
                        this.visible = true;
                    } else {
                        console.log('❌ No deferred prompt available in component');
                        alert('Install PWA tidak tersedia saat ini.\n\nKemungkinan:\n1. Browser belum detect PWA criteria\n2. Sudah pernah dismiss sebelumnya\n3. Perlu refresh halaman\n\nCoba refresh dan tunggu beberapa detik.');
                    }
                },
                isStandalone() {
                    return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
                },
                async install() {
                    this.error = null;
                    if (!this.deferred) {
                        this.error = 'Install tidak tersedia di browser ini.';
                        return;
                    }
                    try {
                        this.installing = true;
                        this.deferred.prompt();
                        const choice = await this.deferred.userChoice;
                        if (choice.outcome === 'accepted') {
                            localStorage.setItem('pwaInstalled','1');
                        } else {
                            localStorage.setItem('pwaInstallPromptDismissed','1');
                        }
                        this.visible = false;
                        this.deferred = null;
                    } catch (err) {
                        this.error = 'Gagal memulai instalasi.';
                    } finally {
                        this.installing = false;
                    }
                },
                later() {
                    localStorage.setItem('pwaInstallPromptDismissed','1');
                    this.visible = false;
                },
                dismiss() { this.later(); }
            }));
        });
    </script>
</x-app-layout>
