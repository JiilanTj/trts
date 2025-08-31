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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Inter', sans-serif;
            }

            @keyframes glitch {
                0% { transform: translate(0, 0); }
                10% { transform: translate(-1px, 1px); }
                20% { transform: translate(1px, -1px); }
                30% { transform: translate(-1px, -1px); }
                40% { transform: translate(1px, 1px); }
                50% { transform: translate(-1px, 0); }
                60% { transform: translate(1px, 0); }
                70% { transform: translate(0, -1px); }
                80% { transform: translate(0, 1px); }
                90% { transform: translate(-1px, 1px); }
                100% { transform: translate(0, 0); }
            }

            .glitch-button:hover .glitch-layer-1 {
                animation: glitch 0.3s ease-in-out;
            }

            .glitch-button:hover .glitch-layer-2 {
                animation: glitch 0.3s ease-in-out 0.1s;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-blue-50 via-white to-purple-50">
        <div class="min-h-screen pb-20">
            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Bottom Navigation - TikTok Style -->
            <nav class="fixed bottom-0 left-0 right-0 bg-black z-50">
                <div class="flex justify-around items-center h-16 px-4">
                    <!-- Home -->
                    <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center space-y-1 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-400' }}">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        <span class="text-xs">Home</span>
                    </a>

                    <!-- Shop -->
                    <a href="{{ route('browse.categories.index') }}" class="flex flex-col items-center justify-center space-y-1 {{ request()->routeIs('browse.categories.*') ? 'text-white' : 'text-gray-400' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span class="text-xs">Shop</span>
                    </a>

                <!-- Plus Button dengan TikTok glitch effect -->
                <button class="glitch-button flex flex-col items-center justify-center space-y-1 relative">
                    <div class="relative w-12 h-8">
                        <!-- Pink layer (terlihat di kanan) -->
                        <div class="glitch-layer-1 absolute w-12 h-8 bg-pink-500 rounded-xl transform translate-x-1"></div>
                        
                        <!-- Cyan layer (terlihat di kiri) -->
                        <div class="glitch-layer-2 absolute w-12 h-8 bg-cyan-400 rounded-xl transform -translate-x-1"></div>
                        
                        <!-- Main white layer (di tengah) -->
                        <div class="relative w-12 h-8 bg-white rounded-xl flex items-center justify-center z-10">
                            <svg class="w-4 h-4 text-black font-bold" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </button>

                    <!-- Inbox with notification -->
                    <button class="flex flex-col items-center justify-center space-y-1 text-gray-400 relative">
                        <div class="relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4m16 0l-2-2M4 13l2-2"></path>
                            </svg>
                            <!-- Red notification badge -->
                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center">
                                <span class="text-xs text-white font-bold">1</span>
                            </div>
                        </div>
                        <span class="text-xs">Inbox</span>
                    </button>

                    <!-- Profile with notification dot -->
                    <button class="flex flex-col items-center justify-center space-y-1 text-gray-400 relative">
                        <div class="relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <!-- Red notification dot -->
                            <div class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-red-500 rounded-full"></div>
                        </div>
                        <span class="text-xs">Profile</span>
                    </button>
                </div>
            </nav>
        </div>
    </body>
</html>
