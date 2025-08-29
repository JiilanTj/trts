<x-admin-layout>
    <x-slot name="title">{{ $product->name }}</x-slot>

    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ $product->name }}</h1>
                    <p class="text-gray-600 mt-1">SKU: {{ $product->sku }}</p>
                </div>
                <div class="flex space-x-2">
                        <a href="{{ route('admin.products.edit', $product) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                            Edit Product
                        </a>
                        <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                            Back to Products
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Product Image -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-6">
                            @if($product->image)
                                <div class="aspect-square rounded-lg overflow-hidden border-2 border-gray-200">
                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" 
                                         class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="aspect-square rounded-lg bg-gray-200 flex items-center justify-center border-2 border-gray-300">
                                    <div class="text-center text-gray-500">
                                        <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <p>No Image</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Product Details -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Status Badge -->
                        <div class="flex items-center space-x-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                {{ $product->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($product->status) }}
                            </span>
                            @if($product->stock <= 5)
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    Low Stock
                                </span>
                            @endif
                            @if($product->expiry_date && $product->expiry_date->isPast())
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    Expired
                                </span>
                            @elseif($product->expiry_date && $product->expiry_date->diffInDays() <= 30)
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                    Expires Soon
                                </span>
                            @endif
                        </div>

                        <!-- Basic Information -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Product Name</label>
                                    <p class="mt-1 text-gray-900">{{ $product->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">SKU</label>
                                    <p class="mt-1 text-gray-900 font-mono">{{ $product->sku }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Category</label>
                                    <p class="mt-1">
                                        <a href="{{ route('admin.categories.show', $product->category) }}" 
                                           class="text-blue-600 hover:text-blue-800">
                                            {{ $product->category->name }}
                                        </a>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Status</label>
                                    <p class="mt-1 text-gray-900">{{ ucfirst($product->status) }}</p>
                                </div>
                            </div>
                            @if($product->description)
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-600">Description</label>
                                    <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $product->description }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Pricing Information -->
                        <div class="bg-blue-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pricing Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Purchase Price</label>
                                    <p class="mt-1 text-xl font-semibold text-gray-900">${{ number_format($product->purchase_price, 2) }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Sell Price</label>
                                    <p class="mt-1 text-xl font-semibold text-green-600">${{ number_format($product->sell_price, 2) }}</p>
                                </div>
                                @if($product->promo_price)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Promo Price</label>
                                        <p class="mt-1 text-xl font-semibold text-orange-600">${{ number_format($product->promo_price, 2) }}</p>
                                    </div>
                                @endif
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Profit</label>
                                    <p class="mt-1 text-xl font-semibold {{ $product->profit > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        ${{ number_format($product->profit, 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Inventory & Physical Details -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Inventory & Physical Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Stock Quantity</label>
                                    <p class="mt-1 text-xl font-semibold {{ $product->stock <= 5 ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $product->stock }} units
                                    </p>
                                </div>
                                @if($product->weight)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Weight</label>
                                        <p class="mt-1 text-gray-900">{{ number_format($product->weight, 2) }} grams</p>
                                    </div>
                                @endif
                                @if($product->expiry_date)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Expiry Date</label>
                                        <p class="mt-1 text-gray-900">
                                            {{ $product->expiry_date->format('M d, Y') }}
                                            <span class="text-sm text-gray-500">
                                                ({{ $product->expiry_date->diffForHumans() }})
                                            </span>
                                        </p>
                                    </div>
                                @endif
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Created</label>
                                    <p class="mt-1 text-gray-900">{{ $product->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-between items-center pt-6 border-t">
                            <div class="flex space-x-4">
                                <a href="{{ route('admin.products.edit', $product) }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                                    Edit Product
                                </a>
                                
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Are you sure you want to delete this product?')"
                                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition duration-200">
                                        Delete Product
                                    </button>
                                </form>
                            </div>

                            <div class="text-sm text-gray-500">
                                Last updated: {{ $product->updated_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
