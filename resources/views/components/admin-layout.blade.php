<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin Panel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50">
    <div class="min-h-screen flex">
        <!-- Include Admin Sidebar - Fixed Position -->
        <div class="fixed inset-y-0 left-0 z-50 h-screen">
            <x-admin.sidebar />
        </div>

        <!-- Main Content Area - Offset for sidebar -->
        <div class="flex-1 flex flex-col overflow-hidden ml-64">
            <!-- Top Header - Fixed Position -->
            <header class="bg-white shadow-sm border-b border-gray-200 fixed top-0 right-0 z-40" style="left: calc(16rem - 1px); border-left: 1px solid #e5e7eb;">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <h1 class="text-2xl font-bold text-gray-800">{{ $title ?? 'Admin Dashboard' }}</h1>
                            <div class="hidden sm:block">
                                <nav class="flex space-x-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                        Live
                                    </span>
                                </nav>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <!-- Search -->
                            <div class="hidden md:block">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white text-gray-900 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Cari...">
                                </div>
                            </div>

                            <!-- Notifications -->
                            <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg relative transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <span class="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full"></span>
                            </button>

                            <!-- Profile Dropdown -->
                            <div class="relative">
                                <button class="flex items-center space-x-3 p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                        <span class="text-xs font-bold text-white">{{ strtoupper(substr(auth()->user()->full_name, 0, 2)) }}</span>
                                    </div>
                                    <div class="hidden sm:block text-left">
                                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->full_name }}</p>
                                        <p class="text-xs text-gray-500">Administrator</p>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Current Time -->
                            <div class="hidden lg:block text-sm text-gray-600 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                                <span id="current-time"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content - With top padding for fixed header -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-8 pt-28">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Update current time -->
    <script>
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { 
                hour: '2-digit', 
                minute: '2-digit',
                second: '2-digit'
            });
            const dateString = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            document.getElementById('current-time').textContent = `${dateString}, ${timeString}`;
        }
        
        updateTime();
        setInterval(updateTime, 1000);
    </script>
</body>
</html>
