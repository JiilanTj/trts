<x-admin-layout>
    <x-slot name="title">Etalase {{ $user->full_name }}</x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h4a1 1 0 011 1v5m-6 0V9a1 1 0 011-1h4a1 1 0 011 1v11"></path>
                        </svg>
                        Etalase {{ $user->full_name }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        @if($user->sellerInfo)
                            Toko: {{ $user->sellerInfo->store_name }} | 
                        @endif
                        Total Etalase: {{ $showcases->count() }} | 
                        Aktif: {{ $showcases->where('is_active', true)->count() }} |
                        Unggulan: {{ $showcases->where('is_featured', true)->count() }}
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.showcases.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Semua Etalase
                    </a>
                    <a href="{{ route('admin.showcases.user-showcase', $user->id) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors" 
                       target="_blank">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        Tampilan Publik
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- User Info -->
            <div class="mb-6">
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 mr-4">
                            @if($user->sellerInfo && $user->sellerInfo->store_logo)
                                <img src="{{ Storage::url($user->sellerInfo->store_logo) }}" 
                                     alt="{{ $user->full_name }}" 
                                     class="w-20 h-20 rounded-full object-cover">
                            @else
                                <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $user->full_name }}</h3>
                            @if($user->sellerInfo)
                                <div class="space-y-1">
                                    <p class="text-sm text-gray-600"><span class="font-medium">Nama Toko:</span> {{ $user->sellerInfo->store_name }}</p>
                                    <p class="text-sm text-gray-600"><span class="font-medium">Deskripsi:</span> {{ $user->sellerInfo->description ?? 'Tidak ada deskripsi' }}</p>
                                    <p class="text-sm text-gray-600"><span class="font-medium">Skor Kredit:</span> 
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $user->sellerInfo->credit_score ?? 0 }}
                                        </span>
                                    </p>
                                </div>
                            @else
                                <p class="text-sm text-gray-500">Informasi seller tidak tersedia</p>
                            @endif
                        </div>
                        <div class="flex-shrink-0 text-right">
                            <div class="space-y-1">
                                <p class="text-sm text-gray-600"><span class="font-medium">User ID:</span> {{ $user->id }}</p>
                                <p class="text-sm text-gray-600"><span class="font-medium">Email:</span> {{ $user->email ?? 'Tidak ada email' }}</p>
                                <p class="text-sm text-gray-600"><span class="font-medium">Bergabung:</span> {{ $user->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Showcases -->
            @if($showcases->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($showcases as $showcase)
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow {{ !$showcase->is_active ? 'opacity-75' : '' }}">
                            <!-- Product Image -->
                            <div class="relative">
                                @if($showcase->product && $showcase->product->image_url)
                                    <img src="{{ $showcase->product->image_url }}" 
                                         alt="{{ $showcase->product->name }}" 
                                         class="w-full h-48 object-cover rounded-t-lg">
                                @else
                                    <div class="w-full h-48 bg-gray-100 rounded-t-lg flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                                
                                <!-- Status Badges -->
                                <div class="absolute top-2 left-2 flex flex-col space-y-1">
                                    @if($showcase->is_featured)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            Unggulan
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="absolute top-2 right-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $showcase->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $showcase->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                                    {{ $showcase->product->name ?? 'Produk Tidak Ditemukan' }}
                                </h3>
                                
                                <p class="text-sm text-gray-500 mb-3">
                                    Kategori: {{ $showcase->product->category->name ?? 'Tanpa Kategori' }}
                                </p>

                                <!-- Pricing -->
                                <div class="mb-4">
                                    <div class="text-xl font-bold text-green-600 mb-1">
                                        Rp {{ number_format($showcase->price, 0, ',', '.') }}
                                    </div>
                                    @if($showcase->original_price > $showcase->price)
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm text-gray-500 line-through">
                                                Rp {{ number_format($showcase->original_price, 0, ',', '.') }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ round((($showcase->original_price - $showcase->price) / $showcase->original_price) * 100) }}% OFF
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Description -->
                                @if($showcase->description)
                                    <p class="text-sm text-gray-600 mb-3">
                                        {{ Str::limit($showcase->description, 100) }}
                                    </p>
                                @endif

                                <!-- Meta Info -->
                                <div class="mb-4 space-y-1">
                                    <div class="text-xs text-gray-500 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                        </svg>
                                        Urutan: {{ $showcase->sort_order }}
                                    </div>
                                    <div class="text-xs text-gray-500 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Dibuat: {{ $showcase->created_at->format('d M Y H:i') }}
                                    </div>
                                    @if($showcase->is_featured && $showcase->featured_until)
                                        <div class="text-xs text-yellow-600 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            Unggulan sampai: {{ $showcase->featured_until->format('d M Y') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="px-4 pb-4">
                                <div class="grid grid-cols-3 gap-2">
                                    <button type="button" 
                                            class="px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ $showcase->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                            onclick="toggleActive({{ $showcase->id }})">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $showcase->is_active ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' }}"></path>
                                        </svg>
                                        {{ $showcase->is_active ? 'Aktif' : 'Aktifkan' }}
                                    </button>
                                    <button type="button" 
                                            class="px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ $showcase->is_featured ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                            onclick="toggleFeatured({{ $showcase->id }})">
                                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        {{ $showcase->is_featured ? 'Unggulan' : 'Jadikan Unggulan' }}
                                    </button>
                                    <button type="button" 
                                            class="px-3 py-2 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors"
                                            onclick="deleteShowcase({{ $showcase->id }})"
                                            title="Hapus Showcase">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293L16 17.586a1 1 0 01-1.414 0L12 15l-2.586 2.586a1 1 0 01-1.414 0L5.414 15.293A1 1 0 004.828 15H2"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Etalase</h3>
                    <p class="text-gray-500">{{ $user->full_name }} belum membuat etalase apapun.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Toggle Active Status
        function toggleActive(showcaseId) {
            fetch(`/admin/etalase/${showcaseId}/toggle-active`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating status');
            });
        }

        // Toggle Featured Status
        function toggleFeatured(showcaseId) {
            fetch(`/admin/etalase/${showcaseId}/toggle-featured`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating featured status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating featured status');
            });
        }

        // Delete Showcase
        function deleteShowcase(showcaseId) {
            if (confirm('Apakah Anda yakin ingin menghapus etalase ini? Tindakan ini tidak dapat dibatalkan.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/etalase/${showcaseId}`;
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(tokenInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</x-admin-layout>
