<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#0f172a" />
        <link rel="manifest" href="/manifest.webmanifest" crossorigin="anonymous">
        <link rel="preload" href="/manifest.webmanifest" as="fetch" type="application/manifest+json" crossorigin>
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <link rel="apple-touch-icon" href="/icons/icon-192.png">

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

            /* Notification Badge Animation */
            @keyframes pulse-notification {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.1); }
            }

            .notification-pulse {
                animation: pulse-notification 2s infinite;
            }

            @keyframes bounce-in {
                0% { transform: scale(0.3); opacity: 0; }
                50% { transform: scale(1.05); }
                70% { transform: scale(0.9); }
                100% { transform: scale(1); opacity: 1; }
            }

            .notification-bounce-in {
                animation: bounce-in 0.6s ease-out;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-[#0f1115] text-gray-200 selection:bg-fuchsia-500/30 selection:text-white">
        <div class="min-h-screen pb-20">
            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Bottom Navigation - TikTok Style -->
            <nav class="fixed bottom-0 left-0 right-0 bg-[#0c0e12]/95 backdrop-blur-md border-t border-white/10 z-50">
                @php
                    $homeActive = request()->routeIs('dashboard');
                    $profileActive = request()->routeIs('user.profile.index');
                    $additionalMenuActive = request()->routeIs('user.additional-menu.index');
                    $messagesActive = request()->routeIs('user.chat.index');
                    $historyActive = request()->routeIs('user.history.index');
                @endphp
                <div class="flex justify-around items-center h-16 px-4">
                    <!-- Home -->
                    <a href="{{ route('dashboard') }}" aria-label="Home" class="relative flex items-center justify-center {{ $homeActive ? 'glitch-button' : 'text-gray-500 hover:text-gray-300' }}">
                        @if($homeActive)
                            <div class="relative w-12 h-8">
                                <div class="glitch-layer-1 absolute w-12 h-8 bg-fuchsia-500 rounded-xl translate-x-1"></div>
                                <div class="glitch-layer-2 absolute w-12 h-8 bg-cyan-400 rounded-xl -translate-x-1"></div>
                                <div class="relative w-12 h-8 bg-white rounded-xl flex items-center justify-center z-10 shadow shadow-fuchsia-500/30">
                                    <svg class="w-5 h-5 text-black" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                                </div>
                            </div>
                        @else
                            <div class="w-12 h-8 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                            </div>
                        @endif
                    </a>

                    <!-- Additional Menu (gear) -->
                    <a href="{{ route('user.additional-menu.index') }}" aria-label="Additional Menu" class="relative flex items-center justify-center {{ $additionalMenuActive ? 'glitch-button' : 'text-gray-500 hover:text-gray-300' }}">
                        @if($additionalMenuActive)
                            <div class="relative w-12 h-8">
                                <div class="glitch-layer-1 absolute w-12 h-8 bg-fuchsia-500 rounded-xl translate-x-1"></div>
                                <div class="glitch-layer-2 absolute w-12 h-8 bg-cyan-400 rounded-xl -translate-x-1"></div>
                                <div class="relative w-12 h-8 bg-white rounded-xl flex items-center justify-center z-10 shadow shadow-fuchsia-500/30">
                                    <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.757.426 1.757 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.757-2.924 1.757-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.757-.426-1.757-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.607 2.273.07 2.573-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                            </div>
                        @else
                            <div class="w-12 h-8 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.757.426 1.757 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.757-2.924 1.757-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.757-.426-1.757-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.607 2.273.07 2.573-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                        @endif
                    </a>

                    <!-- Messages -->
                    <a href="{{ route('user.chat.index') }}" aria-label="Messages" class="relative flex items-center justify-center {{ $messagesActive ? 'glitch-button' : 'text-gray-500 hover:text-gray-300' }}">
                        @if($messagesActive)
                            <div class="relative w-12 h-8">
                                <div class="glitch-layer-1 absolute w-12 h-8 bg-fuchsia-500 rounded-xl translate-x-1"></div>
                                <div class="glitch-layer-2 absolute w-12 h-8 bg-cyan-400 rounded-xl -translate-x-1"></div>
                                <div class="relative w-12 h-8 bg-white rounded-xl flex items-center justify-center z-10 shadow shadow-fuchsia-500/30">
                                    <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4-.8L3 20l1.22-2.44A7.793 7.793 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                </div>
                            </div>
                        @else
                            <div class="w-12 h-8 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4-.8L3 20l1.22-2.44A7.793 7.793 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                        @endif
                    </a>

                    <!-- History -->
                    <a href="{{ route('user.history.index') }}" aria-label="History" class="relative flex items-center justify-center {{ $historyActive ? 'glitch-button' : 'text-gray-500 hover:text-gray-300' }}">
                        @if($historyActive)
                            <div class="relative w-12 h-8">
                                <div class="glitch-layer-1 absolute w-12 h-8 bg-fuchsia-500 rounded-xl translate-x-1"></div>
                                <div class="glitch-layer-2 absolute w-12 h-8 bg-cyan-400 rounded-xl -translate-x-1"></div>
                                <div class="relative w-12 h-8 bg-white rounded-xl flex items-center justify-center z-10 shadow shadow-fuchsia-500/30">
                                    <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v5l3 3m6-5a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            </div>
                        @else
                            <div class="w-12 h-8 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v5l3 3m6-5a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        @endif
                        <!-- Notification Badge -->
                        <div id="notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full min-w-[18px] h-[18px] items-center justify-center font-medium shadow-lg" style="display: none;">
                            <span id="notification-count">0</span>
                        </div>
                    </a>

                    <!-- Profile -->
                    <a href="{{ route('user.profile.index') }}" aria-label="Profile" class="relative flex items-center justify-center {{ $profileActive ? 'glitch-button' : 'text-gray-500 hover:text-gray-300' }}">
                        @if($profileActive)
                            <div class="relative w-12 h-8">
                                <div class="glitch-layer-1 absolute w-12 h-8 bg-fuchsia-500 rounded-xl translate-x-1"></div>
                                <div class="glitch-layer-2 absolute w-12 h-8 bg-cyan-400 rounded-xl -translate-x-1"></div>
                                <div class="relative w-12 h-8 bg-white rounded-xl flex items-center justify-center z-10 shadow shadow-fuchsia-500/30">
                                    <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                            </div>
                        @else
                            <div class="w-12 h-8 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                        @endif
                    </a>
                </div>
            </nav>
        </div>

        <!-- Real-time Notification Script -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const notificationBadge = document.getElementById('notification-badge');
                const notificationCount = document.getElementById('notification-count');
                let lastCount = 0;
                
                // Function to fetch unread notification count
                async function fetchNotificationCount() {
                    try {
                        const response = await fetch('{{ route('api.notifications.unread-count') }}', {
                            method: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                            },
                            credentials: 'same-origin'
                        });
                        
                        if (response.ok) {
                            const data = await response.json();
                            const count = data.unread_count;
                            
                            // Animate if count increased
                            if (count > lastCount && lastCount !== 0) {
                                notificationBadge.classList.add('notification-bounce-in');
                                setTimeout(() => {
                                    notificationBadge.classList.remove('notification-bounce-in');
                                }, 600);
                            }
                            
                            if (count > 0) {
                                notificationCount.textContent = count > 99 ? '99+' : count;
                                notificationBadge.style.display = 'flex';
                                
                                // Add pulse animation for new notifications
                                if (count > lastCount) {
                                    notificationBadge.classList.add('notification-pulse');
                                    setTimeout(() => {
                                        notificationBadge.classList.remove('notification-pulse');
                                    }, 4000);
                                }
                            } else {
                                notificationBadge.style.display = 'none';
                                notificationBadge.classList.remove('notification-pulse');
                            }
                            
                            lastCount = count;
                        }
                    } catch (error) {
                        console.error('Error fetching notification count:', error);
                        // Optionally show error state or retry
                    }
                }
                
                // Initial load
                fetchNotificationCount();
                
                // Update every 30 seconds
                const notificationInterval = setInterval(fetchNotificationCount, 30000);
                
                // Update when page becomes visible (tab switching)
                document.addEventListener('visibilitychange', function() {
                    if (!document.hidden) {
                        fetchNotificationCount();
                    }
                });
                
                // Listen for custom refresh event from history page
                window.addEventListener('refreshNotificationCount', function() {
                    fetchNotificationCount();
                });
                
                // Clean up interval when page unloads
                window.addEventListener('beforeunload', function() {
                    clearInterval(notificationInterval);
                });
            });
        </script>
    </body>
</html>
