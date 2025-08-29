<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        
        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/heroicons/2.0.18/24/outline/heroicons.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
            
            /* Custom animations */
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
            }
            
            .float-animation {
                animation: float 6s ease-in-out infinite;
            }
            
            .float-animation-delayed {
                animation: float 6s ease-in-out infinite;
                animation-delay: 2s;
            }
            
            /* Gradient text */
            .gradient-text {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gradient-to-br from-blue-50 via-white to-purple-50">
        <div class="min-h-screen flex flex-col lg:flex-row">
            <!-- Left Side - Branding/Image -->
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-600 via-purple-600 to-indigo-700 relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-10 left-10 w-20 h-20 bg-white rounded-full float-animation"></div>
                    <div class="absolute top-40 right-20 w-16 h-16 bg-white rounded-full float-animation-delayed"></div>
                    <div class="absolute bottom-20 left-20 w-12 h-12 bg-white rounded-full float-animation"></div>
                    <div class="absolute bottom-40 right-10 w-24 h-24 bg-white rounded-full float-animation-delayed"></div>
                </div>
                
                <!-- Background Grid -->
                <div class="absolute inset-0 opacity-5">
                    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 20px 20px;"></div>
                </div>
                
                <!-- Content -->
                <div class="relative z-10 flex flex-col justify-center items-center text-white p-12">
                    <div class="text-center">
                        <!-- Logo/Icon -->
                        <div class="mb-8">
                            <div class="w-20 h-20 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                </svg>
                            </div>
                            <h1 class="text-5xl font-bold mb-6">{{ config('app.name') }}</h1>
                        </div>
                        
                        <p class="text-xl text-blue-100 mb-8 leading-relaxed">
                            Bergabunglah dengan ribuan pengguna yang telah mempercayai platform kami untuk kebutuhan digital mereka.
                        </p>
                        
                        <!-- Features -->
                        <div class="mb-8 space-y-4">
                            <div class="flex items-center justify-start text-blue-100">
                                <svg class="w-5 h-5 mr-3 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>Secure & Encrypted Platform</span>
                            </div>
                            <div class="flex items-center justify-start text-blue-100">
                                <svg class="w-5 h-5 mr-3 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>24/7 Customer Support</span>
                            </div>
                            <div class="flex items-center justify-start text-blue-100">
                                <svg class="w-5 h-5 mr-3 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>Fast & Reliable Service</span>
                            </div>
                        </div>
                        
                        <!-- Stats -->
                        <div class="flex items-center justify-center space-x-8 text-blue-100">
                            <div class="text-center">
                                <div class="text-2xl font-bold">10K+</div>
                                <div class="text-sm">Active Users</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">99.9%</div>
                                <div class="text-sm">Uptime</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">24/7</div>
                                <div class="text-sm">Support</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Auth Form -->
            <div class="w-full lg:w-1/2 flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-gray-50 min-h-screen lg:min-h-auto">
                <div class="w-full max-w-md mx-auto">
                    <!-- Mobile Logo -->
                    <div class="lg:hidden text-center mb-6 sm:mb-8 pt-4">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl sm:rounded-2xl flex items-center justify-center mx-auto mb-3 sm:mb-4">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                            </svg>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-bold gradient-text">
                            {{ config('app.name') }}
                        </h1>
                        <p class="text-sm text-gray-600 mt-2">Platform Digital Terpercaya</p>
                    </div>

                    <!-- Auth Card -->
                    <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-100 p-6 sm:p-8 relative overflow-hidden">
                        <!-- Card Background Pattern -->
                        <div class="absolute top-0 right-0 w-24 h-24 sm:w-32 sm:h-32 opacity-5">
                            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgb(59 130 246) 1px, transparent 0); background-size: 15px 15px;"></div>
                        </div>
                        
                        <div class="relative z-10">
                            {{ $slot }}
                        </div>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="mt-4 sm:mt-6 text-center">
                        <div class="flex items-center justify-center text-xs text-gray-500 mb-3 sm:mb-4">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>SSL Secured & Protected</span>
                            </div>
                        </div>
                        
                        <!-- Footer -->
                        <div class="text-xs sm:text-sm text-gray-500 pb-4">
                            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
