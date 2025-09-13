<x-app-layout>
    <div class="min-h-screen bg-[#0f1115] text-gray-200 px-4 py-6 space-y-8 relative overflow-hidden">
        <!-- Background accents -->
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_25%_15%,rgba(236,72,153,0.07),transparent_60%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_85%,rgba(59,130,246,0.08),transparent_65%)]"></div>

        <!-- Header -->
        <div class="relative z-10 text-center mb-8">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-[#FE2C55]/20 to-[#25F4EE]/20 border border-[#FE2C55]/30 mb-4">
                <svg class="w-5 h-5 text-[#FE2C55]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                </svg>
                <span class="text-sm font-medium text-white">Etalase Shared</span>
            </div>
            
            <h1 class="text-2xl font-bold text-white mb-2">
                Etalase {{ $sellerInfo ? $sellerInfo->store_name : $seller->full_name }}
            </h1>
            
            <div class="flex items-center justify-center gap-4 text-sm text-gray-400">
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Seller: {{ $seller->full_name }}</span>
                </div>
                
                @if($sellerInfo)
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m4 0v-5a1 1 0 011-1h4a1 1 0 011 1v5m-4-5v5m0-5h4" />
                    </svg>
                    <span>{{ $sellerInfo->store_name }}</span>
                </div>
                @endif
                
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>{{ $showcases->count() }} Produk</span>
                </div>
                
                @auth
                <div class="flex items-center gap-1 text-green-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ auth()->user()->full_name }}</span>
                </div>
                @else
                <div class="flex items-center gap-1 text-orange-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span>Guest (Login untuk beli)</span>
                </div>
                @endauth
            </div>
        </div>

        <!-- Seller Info Card -->
        @if($sellerInfo)
        <div class="relative z-10 max-w-4xl mx-auto">
            <div class="bg-[#181d23]/50 border border-white/10 rounded-2xl p-6">
                <div class="flex items-start gap-4">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] flex items-center justify-center text-white text-xl font-bold">
                        {{ substr($sellerInfo->store_name, 0, 2) }}
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white">{{ $sellerInfo->store_name }}</h3>
                        <p class="text-sm text-gray-400 mt-1">{{ $seller->full_name }}</p>
                        @if($sellerInfo->store_description)
                        <p class="text-sm text-gray-300 mt-2">{{ $sellerInfo->store_description }}</p>
                        @endif
                        
                        <div class="flex items-center justify-between gap-4 mt-3">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-1 text-xs text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Bergabung {{ $seller->created_at->format('M Y') }}</span>
                                </div>
                                
                                <div class="flex items-center gap-1 text-xs text-gray-400">
                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span id="followers-count">{{ number_format($seller->followers) }} Followers</span>
                                </div>
                                
                                <div class="flex items-center gap-1 text-xs text-gray-400">
                                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>{{ number_format($seller->visitors) }} Visitors</span>
                                </div>
                                
                                @if($seller->level > 1)
                                <div class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-gradient-to-r from-yellow-500/20 to-orange-500/20 border border-yellow-500/30">
                                    <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="text-xs font-medium text-yellow-400">Level {{ $seller->level }}</span>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Follow/Unfollow Button -->
                            @auth
                                @if(auth()->id() !== $seller->id)
                                    <div id="follow-button-container">
                                        @if($isFollowing)
                                            <button id="unfollow-btn" 
                                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-red-500/20 to-pink-500/20 border border-red-500/30 text-red-400 hover:from-red-500/30 hover:to-pink-500/30 hover:border-red-400/50 transition-all duration-300"
                                                    onclick="toggleFollow({{ $seller->id }}, 'unfollow')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                <span>Unfollow</span>
                                            </button>
                                        @else
                                            <button id="follow-btn" 
                                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500/20 to-indigo-500/20 border border-blue-500/30 text-blue-400 hover:from-blue-500/30 hover:to-indigo-500/30 hover:border-blue-400/50 transition-all duration-300"
                                                    onclick="toggleFollow({{ $seller->id }}, 'follow')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                                </svg>
                                                <span>Follow</span>
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            @else
                                <div>
                                    <a href="{{ route('login') }}" 
                                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500/20 to-indigo-500/20 border border-blue-500/30 text-blue-400 hover:from-blue-500/30 hover:to-indigo-500/30 hover:border-blue-400/50 transition-all duration-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                        </svg>
                                        <span>Login to Follow</span>
                                    </a>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Products Grid -->
        <div class="relative z-10 max-w-6xl mx-auto">
            @if($showcases->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($showcases as $item)
                    <div class="bg-[#181d23]/50 border border-white/10 rounded-2xl overflow-hidden hover:border-white/20 transition-colors group">
                        <!-- Product Image -->
                        <div class="aspect-square bg-gray-800/50 flex items-center justify-center overflow-hidden">
                            @if($item->product->image_url)
                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-300">
                            @else
                                <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            @endif
                        </div>
                        
                        <!-- Product Info -->
                        <div class="p-4">
                            <h4 class="font-medium text-white text-sm mb-2 line-clamp-2 leading-tight">{{ $item->product->name }}</h4>
                            
                            <!-- Price Section -->
                            <div class="space-y-2">
                                <!-- Price -->
                                <div class="mb-3">
                                    <span class="text-lg font-bold text-[#25F4EE]">
                                        Rp {{ number_format($item->product->harga_jual, 0, ',', '.') }}
                                    </span>
                                </div>
                                
                                <!-- Stock and Category Info -->
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-400">Stok: {{ $item->product->stock }}</span>
                                    
                                    @if($item->product->category)
                                    <span class="text-gray-500">{{ $item->product->category->name }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Featured Badge -->
                            @if($item->is_featured_active)
                            <div class="mt-3 mb-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-yellow-500/20 border border-yellow-500/30">
                                    <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="text-xs font-medium text-yellow-400">Featured</span>
                                </span>
                            </div>
                            @endif
                            
                            <!-- Buy Button -->
                            @if($item->product->stock > 0)
                            <div class="mt-3 space-y-2">
                                @auth
                                <!-- Authenticated user - show buy button -->
                                <form action="{{ route('etalase.buy-product', $item->product->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="seller_id" value="{{ $seller->id }}">
                                    <input type="hidden" name="from_etalase" value="true">
                                    <button type="submit" class="w-full py-2 px-4 bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] text-white text-sm font-medium rounded-lg hover:opacity-90 transition focus:outline-none focus:ring-2 focus:ring-[#FE2C55]/50">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5-5M7 13l-2.5 5M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6" />
                                        </svg>
                                        Beli Sekarang
                                    </button>
                                </form>
                                @else
                                <!-- Guest user - show login button -->
                                <a href="{{ route('login') }}" class="block w-full py-2 px-4 bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] text-white text-sm font-medium rounded-lg hover:opacity-90 transition focus:outline-none focus:ring-2 focus:ring-[#FE2C55]/50 text-center">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                    Login untuk Beli
                                </a>
                                @endauth
                            </div>
                            @else
                            <div class="mt-3">
                                <button disabled class="w-full py-2 px-4 bg-gray-600/50 text-gray-400 text-sm font-medium rounded-lg cursor-not-allowed">
                                    Stok Habis
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="w-20 h-20 mx-auto rounded-full bg-white/5 flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">Etalase Kosong</h3>
                    <p class="text-gray-400 text-sm">Seller belum menambahkan produk ke etalase.</p>
                </div>
            @endif
        </div>

        <!-- Footer Info -->
        <div class="relative z-10 text-center pt-8 pb-4">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10">
                <svg class="w-4 h-4 text-[#FE2C55]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span class="text-xs text-gray-400">Powered by Bangobos</span>
            </div>
        </div>
    </div>
    
    <!-- JavaScript for Buy Button & Follow System -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buyForms = document.querySelectorAll('form[action*="buy"]');
            
            buyForms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    console.log('Form submission:', {
                        action: this.action,
                        seller_id: this.querySelector('input[name="seller_id"]')?.value,
                        from_etalase: this.querySelector('input[name="from_etalase"]')?.value
                    });
                });
            });
        });

        // Follow/Unfollow System
        function toggleFollow(sellerId, action) {
            const button = document.getElementById(action === 'follow' ? 'follow-btn' : 'unfollow-btn');
            const container = document.getElementById('follow-button-container');
            const followersCountElement = document.getElementById('followers-count');
            
            // Disable button during request
            button.disabled = true;
            button.style.opacity = '0.5';
            
            const url = action === 'follow' ? '{{ route("seller.follow") }}' : '{{ route("seller.unfollow") }}';
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    seller_id: sellerId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showNotification(data.message, 'success');
                    
                    // Update followers count
                    followersCountElement.textContent = `${formatNumber(data.followers_count)} Followers`;
                    
                    // Switch button
                    if (action === 'follow') {
                        container.innerHTML = `
                            <button id="unfollow-btn" 
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-red-500/20 to-pink-500/20 border border-red-500/30 text-red-400 hover:from-red-500/30 hover:to-pink-500/30 hover:border-red-400/50 transition-all duration-300"
                                    onclick="toggleFollow(${sellerId}, 'unfollow')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span>Unfollow</span>
                            </button>
                        `;
                    } else {
                        container.innerHTML = `
                            <button id="follow-btn" 
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500/20 to-indigo-500/20 border border-blue-500/30 text-blue-400 hover:from-blue-500/30 hover:to-indigo-500/30 hover:border-blue-400/50 transition-all duration-300"
                                    onclick="toggleFollow(${sellerId}, 'follow')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Follow</span>
                            </button>
                        `;
                    }
                } else {
                    showNotification(data.message, 'error');
                    
                    // Re-enable button
                    button.disabled = false;
                    button.style.opacity = '1';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
                
                // Re-enable button
                button.disabled = false;
                button.style.opacity = '1';
            });
        }

        // Notification system
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg border transition-all duration-300 transform translate-x-full ${
                type === 'success' 
                    ? 'bg-green-500/20 border-green-500/30 text-green-400' 
                    : 'bg-red-500/20 border-red-500/30 text-red-400'
            }`;
            notification.innerHTML = `
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        ${type === 'success' 
                            ? '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />'
                            : '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />'
                        }
                    </svg>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Animate out and remove
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        // Number formatting
        function formatNumber(num) {
            if (num >= 1000000) {
                return (num / 1000000).toFixed(1) + 'M';
            } else if (num >= 1000) {
                return (num / 1000).toFixed(1) + 'K';
            }
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }
    </script>
</x-app-layout>
