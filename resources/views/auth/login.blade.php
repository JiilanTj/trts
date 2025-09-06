<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Login</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .bg-blur {
            backdrop-filter: blur(8px);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .input-glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .input-glass:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(254, 44, 85, 0.5);
            box-shadow: 0 0 0 3px rgba(254, 44, 85, 0.1);
        }
    </style>
</head>
<body>
    @php
        $setting = \App\Models\Setting::first();
    @endphp
    
    <!-- Background -->
    <div id="background-container" class="min-h-screen relative overflow-hidden bg-cover bg-center bg-no-repeat bg-gray-900" 
         data-bg="{{ asset('backgroundlogin.png') }}">
         
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/40"></div>
        
        <!-- Content Container -->
        <div class="relative z-10 min-h-screen flex flex-col px-4 py-8">
            
            <!-- Logo - Positioned higher -->
            <div class="mt-16 mb-20 text-center">
                @if($setting && $setting->logo_url)
                    <img src="{{ $setting->logo_url }}" alt="Logo" class="w-24 h-24 mx-auto">
                @else
                    <!-- Default TikTok-style icon if no logo -->
                    <div class="w-24 h-24 mx-auto relative">
                        <svg viewBox="0 0 118 42" fill="none" class="w-full h-full">
                            <path d="M9.875 16.842V15.108c-.739-.084-1.478-.135-2.233-.135-1.138 0-2.226.169-3.248.481v1.734c.956-.32 2.006-.489 3.09-.489.672 0 1.345.068 2.013.203l.378-.07z" fill="#25F4EE"/>
                            <path d="M9.875 16.842V15.108c-.739-.084-1.478-.135-2.233-.135-1.138 0-2.226.169-3.248.481v1.734c.956-.32 2.006-.489 3.09-.489.672 0 1.345.068 2.013.203l.378-.07z" fill="#FE2C55"/>
                            <path d="M27.436 29.503c2.522 0 4.567-2.045 4.567-4.567s-2.045-4.567-4.567-4.567-4.567 2.045-4.567 4.567 2.045 4.567 4.567 4.567z" fill="#25F4EE"/>
                            <path d="M35.593 16.842V15.108c-.739-.084-1.478-.135-2.233-.135-1.138 0-2.226.169-3.248.481v1.734c.956-.32 2.006-.489 3.09-.489.672 0 1.345.068 2.013.203l.378-.07z" fill="#FE2C55"/>
                        </svg>
                    </div>
                @endif
            </div>
            
            <!-- Login Form Container - Centered and larger -->
            <div class="flex-1 flex items-start justify-center">
                <div class="w-full max-w-xs">
                
                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="glass-effect rounded-lg p-3 text-center text-green-400 text-sm">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    <!-- Username Input -->
                    <div>
                        <label for="username" class="block text-black text-sm font-semibold mb-2">Akun</label>
                        <input 
                            id="username" 
                            name="username" 
                            type="text" 
                            required 
                            autofocus 
                            autocomplete="username"
                            value="{{ old('username') }}"
                            placeholder="Silahkan Masukkan Akun"
                            class="input-glass w-full px-4 py-4 rounded-lg text-white placeholder-gray-400 text-base focus:outline-none transition-all duration-200" />
                        
                        @error('username')
                            <p class="mt-2 text-red-400 text-xs">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-black text-sm font-semibold mb-2">Sandi</label>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required 
                            autocomplete="current-password"
                            placeholder="Silahkan Masukkan Sandi"
                            class="input-glass w-full px-4 py-4 rounded-lg text-white placeholder-gray-400 text-base focus:outline-none transition-all duration-200" />
                        
                        @error('password')
                            <p class="mt-2 text-red-400 text-xs">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Login Button -->
                    <div class="pt-4">
                        <div class="relative">
                            <!-- Layer 1 - Cyan (kiri) -->
                            <div class="absolute w-full h-full bg-cyan-400 rounded-lg -translate-x-[2px]"></div>
                            <!-- Layer 2 - Red (kanan) -->
                            <div class="absolute w-full h-full bg-red-500 rounded-lg translate-x-[2px]"></div>
                            <!-- Main Button -->
                            <button 
                                type="submit"
                                class="relative w-full bg-black hover:bg-gray-900 text-white font-semibold py-4 px-4 rounded-lg transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-white/20 text-base z-10">
                                Login
                            </button>
                        </div>
                    </div>
                    
                </form>
                
                <!-- Bottom Text -->
                <div class="mt-12 text-center">
                    <p class="text-white/80 text-xs">
                        Tukar Bahasa|Bantuan|Hubungi|layanan pelanggan
                    </p>
                </div>
                
                </div>
            </div>
            
        </div>
    </div>
    
    <!-- Lazy Load Background Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bgContainer = document.getElementById('background-container');
            const bgUrl = bgContainer.getAttribute('data-bg');
            
            // Create a new image to preload
            const img = new Image();
            img.onload = function() {
                // Once loaded, apply background
                bgContainer.style.backgroundImage = `url('${bgUrl}')`;
                bgContainer.classList.add('opacity-100');
            };
            
            // Start loading the image
            img.src = bgUrl;
            
            // Add initial opacity for smooth transition
            bgContainer.style.opacity = '0.7';
            bgContainer.style.transition = 'opacity 0.5s ease-in-out';
        });
    </script>
    
</body>
</html>
