<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
        <!-- Header Section with User Info -->
        <div class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <!-- Profile Photo with Badge -->
                            <div class="relative">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <!-- Badge on Profile -->
                                <div class="absolute -top-1 -right-1 w-5 h-5 bg-orange-500 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-bold text-white">+</span>
                                </div>
                            </div>
                            
                            <div>
                                @if(auth()->user()->isSeller() && auth()->user()->sellerInfo)
                                    <h1 class="text-xl font-medium text-gray-900">{{ auth()->user()->full_name }}</h1>
                                    <p class="text-sm text-blue-600 font-medium">{{ auth()->user()->sellerInfo->store_name }}</p>
                                @else
                                    <h1 class="text-xl font-medium text-gray-900">{{ auth()->user()->full_name }}</h1>
                                @endif
                                <!-- User Stats -->
                                <div class="flex items-center space-x-4 mt-1">
                                    @if(auth()->user()->isSeller() && auth()->user()->sellerInfo)
                                        <!-- Seller Stats -->
                                        <div class="text-center">
                                            <span class="text-sm font-semibold text-gray-900">{{ auth()->user()->sellerInfo->followers }}</span>
                                            <p class="text-xs text-gray-500">Mengikuti</p>
                                        </div>
                                        <div class="text-center">
                                            <span class="text-sm font-semibold text-gray-900">{{ number_format(auth()->user()->sellerInfo->visitors) }}</span>
                                            <p class="text-xs text-gray-500">Kunjungan</p>
                                        </div>
                                        <div class="text-center">
                                            <span class="text-sm font-semibold text-gray-900">{{ auth()->user()->sellerInfo->credit_score }}</span>
                                            <p class="text-xs text-gray-500">Skor kredit</p>
                                        </div>
                                    @else
                                        <!-- Regular User Stats -->
                                        <div class="text-center">
                                            <span class="text-sm font-semibold text-gray-900">1250</span>
                                            <p class="text-xs text-gray-500">Mengikuti</p>
                                        </div>
                                        <div class="text-center">
                                            <span class="text-sm font-semibold text-gray-900">564423</span>
                                            <p class="text-xs text-gray-500">Kunjungan</p>
                                        </div>
                                        <div class="text-center">
                                            <span class="text-sm font-semibold text-gray-900">100</span>
                                            <p class="text-xs text-gray-500">Skor kredit</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col items-end space-y-1">
                            <!-- Status Badge -->
                            <div class="flex items-center space-x-1 bg-green-50 px-2 py-1 rounded-lg">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-xs font-medium text-green-700">Active</span>
                            </div>
                            
                            @if(auth()->user()->isSeller())
                                <!-- Seller Badge -->
                                <div class="flex items-center space-x-1 bg-orange-50 px-2 py-1 rounded-lg">
                                    <svg class="w-3 h-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    <span class="text-xs font-medium text-orange-700">Seller</span>
                                </div>
                            @endif
                            
                            <!-- Level Badge -->
                            <div class="flex items-center space-x-1 bg-blue-50 px-2 py-1 rounded-lg">
                                <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <span class="text-xs font-medium text-blue-700">Lv {{ auth()->user()->level }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="rounded-2xl shadow-lg p-6 text-white mb-6" style="background-color: #a70054;">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            <h3 class="text-lg font-semibold">Dompet Balance</h3>
                        </div>
                        <p class="text-pink-100 text-sm mb-1">Saldo Promosi: Rp0</p>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-pink-100 text-sm font-medium mb-1">Saldo</p>
                                <p class="text-3xl font-bold">Rp{{ number_format(auth()->user()->balance, 0, ',', '.') }}</p>
                            </div>
                            <button class="bg-white bg-opacity-20 hover:bg-opacity-30 px-4 py-2 rounded-full text-sm font-medium transition-all">
                                MEMOP...
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Pintasan Cepat Component -->
            @include('components.quick-shortcuts')
        </div>
    </div>
</x-app-layout>
