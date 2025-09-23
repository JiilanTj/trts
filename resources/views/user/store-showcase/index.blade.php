<x-app-layout>
    <div class="min-h-screen bg-[#0f1115] text-gray-200 px-4 py-6 space-y-10 relative overflow-hidden">
        <!-- background accents -->
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_25%_15%,rgba(236,72,153,0.07),transparent_60%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_85%,rgba(59,130,246,0.08),transparent_65%)]"></div>

        <!-- Header -->
        <div class="flex items-center gap-3 relative z-10">
            <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:bg-white/5 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60" aria-label="Kembali">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <div class="flex-1 min-w-0">
                <h1 class="text-base font-semibold text-white leading-tight line-clamp-1">Etalase Saya</h1>
                <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1">Kelola produk di etalase toko Anda.</p>
            </div>
            <div class="flex items-center gap-2">
                <!-- Share Etalase Button -->
                {{-- <button id="shareEtalaseBtn" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] text-white hover:opacity-90 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" /></svg>
                    Share Etalase
                </button> --}}
                
                <!-- Tambah Produk Button -->
                <a href="{{ route('user.showcases.create') }}" class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-gradient-to-r from-fuchsia-500 via-rose-500 to-cyan-500 text-white hover:from-fuchsia-500/90 hover:via-rose-500/90 hover:to-cyan-500/90 shadow-sm shadow-fuchsia-500/30 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                    Tambah Produk
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 relative z-10">
            <div class="rounded-2xl border border-white/10 bg-[#181d23] p-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-fuchsia-500/20 to-rose-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-fuchsia-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-white">{{ $totalShowcases }}</p>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wide">Total Produk</p>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-white/10 bg-[#181d23] p-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500/20 to-cyan-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-white">{{ $activeShowcases }}</p>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wide">Aktif</p>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-white/10 bg-[#181d23] p-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-500/20 to-orange-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-white">{{ $featuredShowcases }}</p>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wide">Featured</p>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-white/10 bg-[#181d23] p-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-gray-500/20 to-slate-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-white">{{ $totalShowcases - $activeShowcases }}</p>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wide">Draft</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="relative z-10">
                <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-4">
                    <p class="text-sm text-emerald-400 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Products Grid -->
        <div class="relative z-10">
            @if($showcases->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($showcases as $showcase)
                        <div class="group relative rounded-2xl p-4 bg-[#181d23] border border-white/5 hover:border-fuchsia-500/60 transition flex flex-col overflow-hidden">
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-gradient-to-br from-fuchsia-500/10 via-transparent to-cyan-500/10 transition pointer-events-none"></div>
                            
                            <!-- Product Image -->
                            <div class="relative aspect-square w-full bg-[#1f252c] border border-white/5 rounded-xl flex items-center justify-center mb-3 overflow-hidden group-hover:border-fuchsia-400/40">
                                @if($showcase->product->image_url)
                                    <img src="{{ $showcase->product->image_url }}" alt="{{ $showcase->product->name }}" class="object-cover w-full h-full">
                                @else
                                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                @endif
                                
                                <!-- Status badges -->
                                <div class="absolute top-2 left-2 flex flex-col gap-1">
                                    @if($showcase->is_featured && $showcase->is_featured_active)
                                        <span class="px-2 py-1 text-[9px] font-medium rounded-full bg-amber-500 text-white">Featured</span>
                                    @endif
                                    @if(!$showcase->is_active)
                                        <span class="px-2 py-1 text-[9px] font-medium rounded-full bg-gray-600 text-white">Draft</span>
                                    @endif
                                </div>
                                
                                <!-- Actions -->
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition flex flex-col gap-1">
                                    <button type="button" onclick="toggleActive({{ $showcase->id }})" class="w-7 h-7 rounded-lg {{ $showcase->is_active ? 'bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30' : 'bg-gray-500/20 text-gray-400 hover:bg-gray-500/30' }} flex items-center justify-center transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </button>
                                    <button type="button" onclick="toggleFeatured({{ $showcase->id }})" class="w-7 h-7 rounded-lg {{ $showcase->is_featured ? 'bg-amber-500/20 text-amber-400 hover:bg-amber-500/30' : 'bg-gray-500/20 text-gray-400 hover:bg-gray-500/30' }} flex items-center justify-center transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Product Info -->
                            <div class="relative flex-1">
                                <a href="{{ route('user.showcases.show', $showcase) }}" class="block">
                                    <h3 class="text-sm font-medium text-gray-200 group-hover:text-white hover:text-cyan-400 mb-1 line-clamp-2 leading-snug transition">{{ $showcase->product->name }}</h3>
                                </a>
                                <p class="text-xs text-gray-500 mb-2">{{ $showcase->product->category->name }}</p>
                                <p class="text-sm font-semibold bg-clip-text text-transparent bg-gradient-to-r from-fuchsia-400 via-rose-400 to-cyan-400 mb-3">Rp {{ number_format($showcase->product->harga_jual, 0, ',', '.') }}</p>
                                
                                <!-- Actions -->
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('user.showcases.show', $showcase) }}" class="flex-1 py-2 px-2 rounded-lg text-xs font-medium text-cyan-400 bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/20 hover:border-cyan-500/30 transition text-center">
                                        Lihat
                                    </a>
                                    <a href="{{ route('user.showcases.edit', $showcase) }}" class="flex-1 py-2 px-2 rounded-lg text-xs font-medium text-gray-300 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 transition text-center">
                                        Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($showcases->hasPages())
                    <div class="mt-8">
                        {{ $showcases->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-gray-500/20 to-slate-500/20 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-300 mb-2">Etalase Masih Kosong</h3>
                    <p class="text-sm text-gray-500 mb-6">Mulai tambahkan produk ke etalase toko Anda.</p>
                    <a href="{{ route('user.showcases.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-gradient-to-r from-fuchsia-500 via-rose-500 to-cyan-500 text-white hover:from-fuchsia-500/90 hover:via-rose-500/90 hover:to-cyan-500/90 shadow-sm shadow-fuchsia-500/30 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        Tambah Produk Pertama
                    </a>
                </div>
            @endif
        </div>

        <!-- Mobile Add Button -->
        <div class="sm:hidden fixed bottom-6 right-6 z-20">
            <a href="{{ route('user.showcases.create') }}" class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-r from-fuchsia-500 via-rose-500 to-cyan-500 text-white shadow-lg shadow-fuchsia-500/30 hover:shadow-xl hover:shadow-fuchsia-500/40 transition focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
            </a>
        </div>
    </div>

    <!-- JavaScript for AJAX actions -->
    <script>
        function toggleActive(showcaseId) {
            fetch(`/etalase/${showcaseId}/toggle-active`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal mengubah status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            });
        }

        function toggleFeatured(showcaseId) {
            fetch(`/etalase/${showcaseId}/toggle-featured`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal mengubah status featured');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            });
        }

        // Share Etalase functionality
        let shareModal = null;
        let currentShareUrl = @json(Auth::user()->etalase_share_url ?? null);
        
        function openShareModal() {
            // Create modal if not exists
            if (!shareModal) {
                shareModal = document.createElement('div');
                shareModal.className = 'fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4';
                shareModal.innerHTML = `
                    <div class="bg-[#1a1d21] border border-white/10 rounded-2xl p-6 max-w-md w-full mx-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-white">Share Etalase Saya</h3>
                            <button onclick="closeShareModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-gray-400 hover:text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <p class="text-sm text-gray-400">Bagikan seluruh etalase Anda dengan link khusus. User lain yang sudah login bisa melihat semua produk di etalase Anda.</p>
                            
                            <div class="space-y-2">
                                <label class="text-xs font-medium text-gray-300">Link Sharing Etalase</label>
                                <div class="flex gap-2">
                                    <input type="text" id="shareUrlInput" class="flex-1 px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-fuchsia-500/60" readonly>
                                    <button onclick="copyShareUrl()" class="px-4 py-2 bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] text-white text-sm font-medium rounded-lg hover:opacity-90 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="flex gap-2 pt-2">
                                <button onclick="generateNewShareUrl()" class="flex-1 py-2 bg-white/10 hover:bg-white/20 text-white text-sm font-medium rounded-lg transition">
                                    Generate Ulang
                                </button>
                                <button onclick="closeShareModal()" class="flex-1 py-2 bg-gray-600/20 hover:bg-gray-600/30 text-gray-300 text-sm font-medium rounded-lg transition">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                // Add click outside to close
                shareModal.addEventListener('click', function(e) {
                    if (e.target === shareModal) {
                        closeShareModal();
                    }
                });
                
                document.body.appendChild(shareModal);
            }
            
            // Update share URL in input
            const shareUrlInput = shareModal.querySelector('#shareUrlInput');
            if (currentShareUrl) {
                shareUrlInput.value = currentShareUrl;
            } else {
                shareUrlInput.value = 'Generating...';
                generateNewShareUrl();
            }
            
            shareModal.style.display = 'flex';
        }
        
        function closeShareModal() {
            if (shareModal) {
                shareModal.style.display = 'none';
            }
        }
        
        function generateNewShareUrl() {
            const shareUrlInput = document.querySelector('#shareUrlInput');
            shareUrlInput.value = 'Generating...';
            
            fetch(`{{ route('user.showcases.generate-etalase-share-token') }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                // Get response text first for debugging
                return response.text().then(text => {
                    console.log('Response text:', text);
                    
                    // Check if response is OK
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}. Response: ${text}`);
                    }
                    
                    // Try to parse as JSON
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error(`Failed to parse JSON. Response: ${text}`);
                    }
                });
            })
            .then(data => {
                console.log('Response data:', data);
                
                if (data.success) {
                    currentShareUrl = data.share_url;
                    shareUrlInput.value = currentShareUrl;
                    // Show success notification
                    showNotification('Link sharing etalase berhasil diperbarui!', 'success');
                } else {
                    shareUrlInput.value = 'Error generating URL';
                    showNotification(data.message || 'Gagal generate link sharing etalase', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                shareUrlInput.value = 'Error generating URL';
                showNotification('Terjadi kesalahan saat generate link: ' + error.message, 'error');
            });
        }
        
        function copyShareUrl() {
            const shareUrlInput = document.querySelector('#shareUrlInput');
            if (shareUrlInput.value && shareUrlInput.value !== 'Generating...' && shareUrlInput.value !== 'Error generating URL') {
                shareUrlInput.select();
                shareUrlInput.setSelectionRange(0, 99999); // For mobile devices
                
                try {
                    document.execCommand('copy');
                    showNotification('Link berhasil disalin!', 'success');
                } catch (err) {
                    // Fallback for modern browsers
                    navigator.clipboard.writeText(shareUrlInput.value).then(function() {
                        showNotification('Link berhasil disalin!', 'success');
                    }, function(err) {
                        showNotification('Gagal menyalin link', 'error');
                    });
                }
            }
        }
        
        function showNotification(message, type = 'success') {
            // Create notification
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-[60] px-4 py-3 rounded-lg font-medium text-sm transform transition-all duration-300 ${
                type === 'success' 
                    ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-400' 
                    : 'bg-red-500/10 border border-red-500/20 text-red-400'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
                notification.style.opacity = '1';
            }, 100);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }
        
        // Bind share button click
        document.getElementById('shareEtalaseBtn').addEventListener('click', openShareModal);
    </script>
</x-app-layout>
