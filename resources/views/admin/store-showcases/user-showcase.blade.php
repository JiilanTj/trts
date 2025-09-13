<x-admin-layout>
    <x-slot name="title">Toko {{ $user->sellerInfo->store_name ?? $user->full_name }}</x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-storefront me-2"></i>{{ $user->sellerInfo->store_name ?? $user->full_name }}'s Store
                            </h4>
                            <p class="text-muted small mb-0">Public showcase view - Active showcases only</p>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.showcases.show', $user->id) }}" class="btn btn-outline-secondary btn-sm me-2">
                                <i class="fas fa-arrow-left me-1"></i>Back to Management
                            </a>
                            <a href="{{ route('admin.showcases.index') }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-list me-1"></i>All Showcases
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Store Header -->
                    <div class="row mb-5">
                        <div class="col-12">
                            <div class="text-center py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px;">
                                <div class="mb-3">
                                    @if($user->sellerInfo && $user->sellerInfo->store_logo)
                                        <img src="{{ Storage::url($user->sellerInfo->store_logo) }}" 
                                             alt="{{ $user->sellerInfo->store_name ?? $user->full_name }}" 
                                             class="rounded-circle border border-white border-3" 
                                             style="width: 120px; height: 120px; object-fit: cover;">
                                    @else
                                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center border border-white border-3 mx-auto" 
                                             style="width: 120px; height: 120px;">
                                            <i class="fas fa-store fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <h1 class="text-white mb-2">{{ $user->sellerInfo->store_name ?? $user->full_name }}</h1>
                                @if($user->sellerInfo && $user->sellerInfo->description)
                                    <p class="text-white-50 mb-0 fs-5">{{ $user->sellerInfo->description }}</p>
                                @endif
                                <div class="mt-3">
                                    <span class="badge bg-white text-dark fs-6 px-3 py-2">
                                        <i class="fas fa-box me-2"></i>{{ $showcases->count() }} Products Available
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Showcase -->
                    @if($showcases->count() > 0)
                        <div class="row">
                            @foreach($showcases as $showcase)
                                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                    <div class="card h-100 shadow-sm border-0" style="transition: transform 0.3s, box-shadow 0.3s;">
                                        <!-- Product Image -->
                                        <div class="position-relative overflow-hidden" style="border-radius: 15px 15px 0 0;">
                                            @if($showcase->product && $showcase->product->images && count($showcase->product->images) > 0)
                                                <img src="{{ Storage::url($showcase->product->images[0]) }}" 
                                                     alt="{{ $showcase->product->name }}" 
                                                     class="card-img-top"
                                                     style="height: 250px; object-fit: cover; transition: transform 0.3s;">
                                            @else
                                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                                     style="height: 250px;">
                                                    <i class="fas fa-image fa-3x text-muted"></i>
                                                </div>
                                            @endif
                                            
                                            <!-- Featured Badge -->
                                            @if($showcase->is_featured)
                                                <div class="position-absolute top-0 start-0 p-3">
                                                    <span class="badge bg-warning text-dark fs-6">
                                                        <i class="fas fa-star me-1"></i>Featured
                                                    </span>
                                                </div>
                                            @endif

                                            <!-- Discount Badge -->
                                            @if($showcase->original_price > $showcase->price)
                                                <div class="position-absolute top-0 end-0 p-3">
                                                    <span class="badge bg-danger fs-6">
                                                        {{ round((($showcase->original_price - $showcase->price) / $showcase->original_price) * 100) }}% OFF
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="card-body d-flex flex-column">
                                            <!-- Product Name -->
                                            <h6 class="card-title fw-bold mb-2">
                                                {{ $showcase->product->name ?? 'Product Not Found' }}
                                            </h6>
                                            
                                            <!-- Category -->
                                            <p class="text-muted small mb-3">
                                                <i class="fas fa-tags me-1"></i>
                                                {{ $showcase->product->category->name ?? 'No Category' }}
                                            </p>

                                            <!-- Description -->
                                            @if($showcase->description)
                                                <p class="card-text text-muted small mb-3" style="line-height: 1.5;">
                                                    {{ Str::limit($showcase->description, 80) }}
                                                </p>
                                            @endif

                                            <!-- Pricing -->
                                            <div class="mt-auto">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <h5 class="text-success fw-bold mb-0">
                                                            Rp {{ number_format($showcase->price, 0, ',', '.') }}
                                                        </h5>
                                                        @if($showcase->original_price > $showcase->price)
                                                            <small class="text-muted text-decoration-line-through">
                                                                Rp {{ number_format($showcase->original_price, 0, ',', '.') }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <button type="button" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-eye me-1"></i>View
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-inbox fa-5x text-muted"></i>
                            </div>
                            <h3 class="text-muted mb-2">No Products Available</h3>
                            <p class="text-muted fs-5">
                                {{ $user->sellerInfo->store_name ?? $user->full_name }} hasn't added any products to their showcase yet.
                            </p>
                        </div>
                    @endif

                    <!-- Store Information -->
                    @if($user->sellerInfo)
                        <div class="row mt-5">
                            <div class="col-12">
                                <div class="card bg-light border-0">
                                    <div class="card-body text-center py-4">
                                        <h5 class="mb-3">
                                            <i class="fas fa-info-circle me-2"></i>Store Information
                                        </h5>
                                        <div class="row justify-content-center">
                                            <div class="col-md-8">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <strong class="text-muted">Store Owner:</strong>
                                                            <p class="mb-0">{{ $user->full_name }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <strong class="text-muted">Credit Score:</strong>
                                                            <p class="mb-0">
                                                                <span class="badge bg-info fs-6">
                                                                    {{ $user->sellerInfo->credit_score ?? 0 }} Points
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if($user->sellerInfo->description)
                                                    <div class="mt-3">
                                                        <strong class="text-muted">About This Store:</strong>
                                                        <p class="mb-0 mt-2">{{ $user->sellerInfo->description }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
    </div>
</x-admin-layout>
