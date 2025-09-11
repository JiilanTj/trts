<x-app-layout>
    <div class="min-h-screen bg-[#1a1d21] text-neutral-100">
        <!-- Header with back button -->
        <div class="sticky top-0 z-30 backdrop-blur bg-[#1f2226]/95 border-b border-neutral-800/70">
            <div class="px-4 py-4 flex items-center justify-between">
                <a href="{{ route('user.profile.index') }}" class="flex items-center text-neutral-400 hover:text-neutral-200 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span class="text-sm font-medium">Kembali</span>
                </a>
                <h1 class="text-lg font-bold text-white">Ubah Kata Sandi</h1>
                <div class="w-16"></div> <!-- Spacer for center alignment -->
            </div>
        </div>

        <div class="px-4 py-6 max-w-xl mx-auto">
            <!-- Success Message -->
            @if (session('status') === 'password-updated')
                <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-emerald-300">Kata sandi berhasil diperbarui!</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->updatePassword->any())
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-300">Terjadi kesalahan pada form. Periksa input Anda.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form Card -->
            <div class="rounded-xl border border-[#2c3136] bg-[#23272b] overflow-hidden">
                <div class="p-4">
                    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
                        @csrf
                        @method('put')

                        <!-- Current Password -->
                        <div>
                            <label for="update_password_current_password" class="block text-sm font-medium text-neutral-200 mb-2">
                                Kata Sandi Saat Ini
                            </label>
                            <input 
                                id="update_password_current_password" 
                                name="current_password" 
                                type="password" 
                                class="w-full px-3 py-3 bg-[#1a1d21] border {{ $errors->updatePassword->get('current_password') ? 'border-red-500' : 'border-neutral-700' }} rounded-lg text-neutral-100 placeholder-neutral-500 focus:ring-2 focus:ring-[#FE2C55]/50 focus:border-[#FE2C55] transition-all text-sm" 
                                placeholder="Masukkan kata sandi saat ini"
                                autocomplete="current-password"
                                required />
                            @if($errors->updatePassword->get('current_password'))
                                <p class="mt-2 text-xs text-red-400 flex items-center">
                                    <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $errors->updatePassword->get('current_password')[0] }}
                                </p>
                            @endif
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="update_password_password" class="block text-sm font-medium text-neutral-200 mb-2">
                                Kata Sandi Baru
                            </label>
                            <input 
                                id="update_password_password" 
                                name="password" 
                                type="password" 
                                class="w-full px-3 py-3 bg-[#1a1d21] border {{ $errors->updatePassword->get('password') ? 'border-red-500' : 'border-neutral-700' }} rounded-lg text-neutral-100 placeholder-neutral-500 focus:ring-2 focus:ring-[#FE2C55]/50 focus:border-[#FE2C55] transition-all text-sm" 
                                placeholder="Masukkan kata sandi baru"
                                autocomplete="new-password"
                                required />
                            @if($errors->updatePassword->get('password'))
                                <p class="mt-2 text-xs text-red-400 flex items-center">
                                    <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $errors->updatePassword->get('password')[0] }}
                                </p>
                            @endif
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="update_password_password_confirmation" class="block text-sm font-medium text-neutral-200 mb-2">
                                Konfirmasi Kata Sandi Baru
                            </label>
                            <input 
                                id="update_password_password_confirmation" 
                                name="password_confirmation" 
                                type="password" 
                                class="w-full px-3 py-3 bg-[#1a1d21] border {{ $errors->updatePassword->get('password_confirmation') ? 'border-red-500' : 'border-neutral-700' }} rounded-lg text-neutral-100 placeholder-neutral-500 focus:ring-2 focus:ring-[#FE2C55]/50 focus:border-[#FE2C55] transition-all text-sm" 
                                placeholder="Ulangi kata sandi baru"
                                autocomplete="new-password"
                                required />
                            @if($errors->updatePassword->get('password_confirmation'))
                                <p class="mt-2 text-xs text-red-400 flex items-center">
                                    <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $errors->updatePassword->get('password_confirmation')[0] }}
                                </p>
                            @endif
                        </div>

                        <!-- Security Tips -->
                        <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-4 w-4 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-xs font-semibold text-blue-300 mb-2 uppercase tracking-wide">Tips Keamanan</h3>
                                    <ul class="text-[11px] text-blue-200/80 space-y-1 leading-relaxed">
                                        <li>• Gunakan minimal 8 karakter</li>
                                        <li>• Kombinasikan huruf, angka, dan simbol</li>
                                        <li>• Jangan gunakan kata sandi yang mudah ditebak</li>
                                        <li>• Berbeda dari kata sandi akun lain</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex gap-3 pt-2">
                            <a href="{{ route('user.profile.index') }}" class="flex-1 px-4 py-3 text-center text-sm font-medium text-neutral-300 bg-neutral-700/40 hover:bg-neutral-600/40 border border-neutral-600 rounded-lg transition-colors">
                                Batal
                            </a>
                            <button type="submit" class="flex-1 px-4 py-3 text-sm font-medium text-black bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] rounded-lg hover:opacity-90 transition-all active:scale-95">
                                Perbarui Kata Sandi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer Note -->
            <div class="mt-8 text-center px-4">
                <p class="text-xs text-neutral-500 leading-relaxed">
                    Pastikan menggunakan kata sandi yang kuat untuk keamanan akun Anda
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
