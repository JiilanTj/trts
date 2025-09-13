<x-admin-layout>
    <x-slot name="title">Etalase {{ $user->full_name }}</x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-store-alt me-2"></i>{{ $user->full_name }}'s Store Showcases
                            </h4>
                            <p class="text-muted small mb-0">
                                @if($user->sellerInfo)
                                    Store: {{ $user->sellerInfo->store_name }} | 
                                @endif
                                Total Showcases: {{ $showcases->count() }} | 
                                Active: {{ $showcases->where('is_active', true)->count() }} |
                                Featured: {{ $showcases->where('is_featured', true)->count() }}
                            </p>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.showcases.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                                <i class="fas fa-arrow-left me-1"></i>Back to All Showcases
                            </a>
                            <a href="{{ route('admin.showcases.user-showcase', $user->id) }}" 
                               class="btn btn-outline-primary btn-sm" target="_blank">
                                <i class="fas fa-external-link-alt me-1"></i>Public View
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- User Info -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            @if($user->sellerInfo && $user->sellerInfo->store_logo)
                                                <img src="{{ Storage::url($user->sellerInfo->store_logo) }}" 
                                                     alt="{{ $user->full_name }}" 
                                                     class="rounded-circle" 
                                                     style="width: 80px; height: 80px; object-fit: cover;">
                                            @else
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 80px; height: 80px;">
                                                    <i class="fas fa-user fa-2x text-white"></i>
                                                </div>
                                            @endif
                                        </div>                            <div class="col">
                                <h5 class="mb-1">{{ $user->full_name }}</h5>
                                @if($user->sellerInfo)
                                                <p class="mb-1"><strong>Store Name:</strong> {{ $user->sellerInfo->store_name }}</p>
                                                <p class="mb-1"><strong>Description:</strong> {{ $user->sellerInfo->description ?? 'No description' }}</p>
                                                <p class="mb-0"><strong>Credit Score:</strong> 
                                                    <span class="badge bg-info">{{ $user->sellerInfo->credit_score ?? 0 }}</span>
                                                </p>
                                            @else
                                                <p class="text-muted mb-0">No seller information available</p>
                                            @endif
                                        </div>
                                        <div class="col-auto">
                                            <div class="text-end">
                                                <p class="mb-1"><strong>User ID:</strong> {{ $user->id }}</p>
                                                <p class="mb-1"><strong>Email:</strong> {{ $user->email }}</p>
                                                <p class="mb-0"><strong>Joined:</strong> {{ $user->created_at->format('d M Y') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Showcases -->
                    @if($showcases->count() > 0)
                        <div class="row">
                            @foreach($showcases as $showcase)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 {{ !$showcase->is_active ? 'opacity-75' : '' }}">
                                        <!-- Product Image -->
                                        <div class="position-relative">
                                            @if($showcase->product && $showcase->product->images && count($showcase->product->images) > 0)
                                                <img src="{{ Storage::url($showcase->product->images[0]) }}" 
                                                     alt="{{ $showcase->product->name }}" 
                                                     class="card-img-top"
                                                     style="height: 200px; object-fit: cover;">
                                            @else
                                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                                     style="height: 200px;">
                                                    <i class="fas fa-image fa-3x text-muted"></i>
                                                </div>
                                            @endif
                                            
                                            <!-- Status Badges -->
                                            <div class="position-absolute top-0 start-0 p-2">
                                                @if($showcase->is_featured)
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-star me-1"></i>Featured
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <div class="position-absolute top-0 end-0 p-2">
                                                <span class="badge {{ $showcase->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $showcase->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            <h6 class="card-title">
                                                {{ $showcase->product->name ?? 'Product Not Found' }}
                                            </h6>
                                            
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    Category: {{ $showcase->product->category->name ?? 'No Category' }}
                                                </small>
                                            </p>

                                            <!-- Pricing -->
                                            <div class="mb-3">
                                                <h6 class="text-success mb-1">
                                                    Rp {{ number_format($showcase->price, 0, ',', '.') }}
                                                </h6>
                                                @if($showcase->original_price > $showcase->price)
                                                    <small class="text-muted text-decoration-line-through">
                                                        Rp {{ number_format($showcase->original_price, 0, ',', '.') }}
                                                    </small>
                                                    <span class="badge bg-danger ms-2">
                                                        {{ round((($showcase->original_price - $showcase->price) / $showcase->original_price) * 100) }}% OFF
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Description -->
                                            @if($showcase->description)
                                                <p class="card-text">
                                                    <small>{{ Str::limit($showcase->description, 100) }}</small>
                                                </p>
                                            @endif

                                            <!-- Meta Info -->
                                            <div class="mb-3">
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-sort me-1"></i>Sort Order: {{ $showcase->sort_order }}
                                                </small>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-calendar me-1"></i>Created: {{ $showcase->created_at->format('d M Y H:i') }}
                                                </small>
                                                @if($showcase->is_featured && $showcase->featured_until)
                                                    <small class="text-warning d-block">
                                                        <i class="fas fa-star me-1"></i>Featured until: {{ $showcase->featured_until->format('d M Y') }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="card-footer bg-transparent">
                                            <div class="row g-2">
                                                <div class="col">
                                                    <button type="button" 
                                                            class="btn btn-sm {{ $showcase->is_active ? 'btn-success' : 'btn-outline-success' }} w-100"
                                                            onclick="toggleActive({{ $showcase->id }})">
                                                        <i class="fas fa-{{ $showcase->is_active ? 'check-circle' : 'times-circle' }} me-1"></i>
                                                        {{ $showcase->is_active ? 'Active' : 'Activate' }}
                                                    </button>
                                                </div>
                                                <div class="col">
                                                    <button type="button" 
                                                            class="btn btn-sm {{ $showcase->is_featured ? 'btn-warning' : 'btn-outline-warning' }} w-100"
                                                            onclick="toggleFeatured({{ $showcase->id }})">
                                                        <i class="fas fa-star me-1"></i>
                                                        {{ $showcase->is_featured ? 'Featured' : 'Feature' }}
                                                    </button>
                                                </div>
                                                <div class="col">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger w-100"
                                                            onclick="deleteShowcase({{ $showcase->id }})"
                                                            title="Delete Showcase">
                                                        <i class="fas fa-trash me-1"></i>Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No Showcases</h5>
                            <p class="text-muted mb-0">{{ $user->full_name }} hasn't created any showcases yet.</p>
                        </div>
                    @endif
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
