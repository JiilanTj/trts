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
            
            /* Custom animations */
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
            
            @keyframes pulse-gradient {
                0%, 100% { opacity: 0.4; }
                50% { opacity: 0.8; }
            }
            
            @keyframes glitch {
                0% { transform: translate(0); }
                20% { transform: translate(-2px, 2px); }
                40% { transform: translate(-2px, -2px); }
                60% { transform: translate(2px, 2px); }
                80% { transform: translate(2px, -2px); }
                100% { transform: translate(0); }
            }
            
            .float-animation {
                animation: float 8s ease-in-out infinite;
            }
            
            .float-animation-delayed {
                animation: float 8s ease-in-out infinite;
                animation-delay: 3s;
            }
            
            .pulse-gradient {
                animation: pulse-gradient 4s ease-in-out infinite;
            }
            
            .glitch-effect:hover {
                animation: glitch 0.3s ease-in-out;
            }
            
            /* Gradient text */
            .gradient-text {
                background: linear-gradient(135deg, #FE2C55 0%, #25F4EE 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            
            /* Custom scrollbar */
            ::-webkit-scrollbar {
                width: 6px;
            }
            
            ::-webkit-scrollbar-track {
                background: #1a1d21;
            }
            
            ::-webkit-scrollbar-thumb {
                background: linear-gradient(135deg, #FE2C55, #25F4EE);
                border-radius: 3px;
            }
        </style>
    </head>
    <body class="font-sans text-neutral-100 antialiased bg-[#0f1115] overflow-hidden">
        <!-- Background Effects -->
        <div class="fixed inset-0 z-0">
            <!-- Radial gradients -->
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_15%,rgba(254,44,85,0.15),transparent_50%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_85%_85%,rgba(37,244,238,0.12),transparent_50%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(147,51,234,0.08),transparent_70%)]"></div>
            
            <!-- Animated particles -->
            <div class="absolute top-10 left-10 w-2 h-2 bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] rounded-full float-animation pulse-gradient"></div>
            <div class="absolute top-1/3 right-20 w-3 h-3 bg-gradient-to-r from-[#25F4EE] to-[#FE2C55] rounded-full float-animation-delayed pulse-gradient"></div>
            <div class="absolute bottom-20 left-1/4 w-1.5 h-1.5 bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] rounded-full float-animation pulse-gradient"></div>
            <div class="absolute top-20 right-1/3 w-2.5 h-2.5 bg-gradient-to-r from-[#25F4EE] to-[#FE2C55] rounded-full float-animation-delayed pulse-gradient"></div>
            <div class="absolute bottom-1/3 right-10 w-2 h-2 bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] rounded-full float-animation pulse-gradient"></div>
        </div>

        <div class="min-h-screen flex items-center justify-center relative z-10 p-4">

            <!-- Auth Container -->
            <div class="w-full max-w-md mx-auto">
                <!-- Logo Section -->
                <div class="text-center mb-8">
                    <div class="relative inline-flex items-center justify-center mb-6">
                        <!-- Glowing background -->
                        <div class="absolute inset-0 bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] rounded-2xl blur-xl opacity-30 scale-110"></div>
                        <!-- Logo container -->
                        <div class="relative w-16 h-16 bg-gradient-to-br from-[#FE2C55] to-[#25F4EE] rounded-2xl flex items-center justify-center glitch-effect">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold gradient-text mb-2 glitch-effect">
                        {{ config('app.name') }}
                    </h1>
                    <p class="text-sm text-neutral-400">Platform Digital Terpercaya</p>
                </div>

                <!-- Auth Card -->
                <div class="bg-[#1a1d21] border border-neutral-800/60 rounded-2xl p-8 backdrop-blur-sm relative overflow-hidden shadow-2xl">
                    <!-- Card glow effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-[#FE2C55]/5 to-[#25F4EE]/5 rounded-2xl"></div>
                    
                    <!-- Subtle grid pattern -->
                    <div class="absolute inset-0 opacity-5">
                        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.15) 1px, transparent 0); background-size: 20px 20px;"></div>
                    </div>
                    
                    <div class="relative z-10">
                        {{ $slot }}
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-6 text-center">
                    <div class="flex items-center justify-center text-xs text-neutral-500 mb-4">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>Secured & Protected</span>
                        </div>
                    </div>
                    
                    <div class="text-xs text-neutral-600">
                        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
