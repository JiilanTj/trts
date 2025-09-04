<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-neutral-100 mb-2">Welcome Back!</h2>
        <p class="text-sm text-neutral-400">Sign in to your account to continue</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Username -->
        <div>
            <x-input-label for="username" :value="__('Username')" class="text-neutral-300 font-medium text-sm mb-2 block" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <input 
                    id="username" 
                    class="block w-full pl-10 pr-4 py-3 text-sm bg-[#23272b] border border-neutral-700/70 rounded-lg focus:ring-2 focus:ring-[#FE2C55]/50 focus:border-[#FE2C55] transition-all duration-200 text-neutral-100 placeholder-neutral-500" 
                    type="text" 
                    name="username" 
                    value="{{ old('username') }}" 
                    required 
                    autofocus 
                    autocomplete="username"
                    placeholder="Enter your username" />
            </div>
            <x-input-error :messages="$errors->get('username')" class="mt-2 text-red-400 text-sm" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <x-input-label for="password" :value="__('Password')" class="text-neutral-300 font-medium text-sm" />
                <a href="#" class="text-xs text-[#25F4EE] hover:text-[#FE2C55] font-medium transition-colors">Forgot password?</a>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input 
                    id="password" 
                    class="block w-full pl-10 pr-4 py-3 text-sm bg-[#23272b] border border-neutral-700/70 rounded-lg focus:ring-2 focus:ring-[#FE2C55]/50 focus:border-[#FE2C55] transition-all duration-200 text-neutral-100 placeholder-neutral-500"
                    type="password"
                    name="password"
                    required 
                    autocomplete="current-password"
                    placeholder="Enter your password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400 text-sm" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="flex items-center">
                <input 
                    id="remember_me" 
                    type="checkbox" 
                    class="rounded border-neutral-600 bg-[#23272b] text-[#FE2C55] shadow-sm focus:ring-[#FE2C55]/50 focus:ring-2" 
                    name="remember">
                <span class="ml-2 text-sm text-neutral-400">Keep me signed in</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div>
            <button 
                type="submit"
                class="w-full bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] hover:from-[#FE2C55]/90 hover:to-[#25F4EE]/90 text-black font-semibold py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-[#FE2C55]/50 focus:ring-offset-2 focus:ring-offset-[#1a1d21] shadow-lg hover:shadow-xl relative overflow-hidden">
                <span class="relative flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Sign In to Continue
                </span>
            </button>
        </div>

        <!-- Divider -->
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-neutral-700"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-[#1a1d21] text-neutral-500">New to {{ config('app.name') }}?</span>
            </div>
        </div>

        <!-- Register Link -->
        <div class="text-center">
            <a href="" class="inline-flex items-center px-6 py-3 border border-neutral-700 rounded-lg text-sm font-medium text-neutral-300 bg-[#23272b] hover:bg-[#2a2f35] hover:border-neutral-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#25F4EE]/50 focus:ring-offset-[#1a1d21] transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Contact our admin to make an account
            </a>
        </div>
    </form>
</x-guest-layout>
