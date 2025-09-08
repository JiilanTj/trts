<x-app-layout>
    @php($user = auth()->user())
    @php($initials = collect(explode(' ', trim($user->full_name ?: $user->username)))->filter()->take(2)->map(fn($p)=> strtoupper(mb_substr($p,0,1)))->implode(''))
    <div class="min-h-screen bg-[#1a1d21] text-neutral-100">
        <!-- Header Section -->
        <div class="sticky top-0 z-40 backdrop-blur bg-[#1f2226]/95 border-b border-neutral-800/70">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="py-4">
                    <div class="flex items-center space-x-3">
                        <!-- Back Button -->
                        <a href="{{ route('user.tickets.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-neutral-700 text-neutral-400 hover:text-white hover:bg-neutral-700 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        
                        <!-- Profile Photo with Badge -->
                        <div class="relative">
                            @if($user->photo_url)
                                <div class="w-12 h-12 rounded-full p-0.5 bg-gradient-to-br from-[#FE2C55] to-[#25F4EE]">
                                    <img src="{{ $user->photo_url }}" alt="Avatar" class="w-full h-full rounded-full object-cover ring-1 ring-black/40" />
                                </div>
                            @else
                                <div class="w-12 h-12 rounded-full flex items-center justify-center bg-gradient-to-br from-[#FE2C55] to-[#25F4EE] p-0.5">
                                    <div class="w-full h-full rounded-full bg-black flex items-center justify-center text-sm font-semibold tracking-wide">{{ $initials }}</div>
                                </div>
                            @endif
                            <!-- Badge on Profile -->
                            <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center bg-[#FE2C55] text-white text-xs font-bold shadow-[0_0_0_2px_#000]">🎫</div>
                        </div>
                        
                        <div>
                            <h1 class="text-xl font-semibold">{{ auth()->user()->full_name }}</h1>
                            <!-- Page Title -->
                            <div class="flex items-center space-x-6 mt-1">
                                <div class="text-left">
                                    <span class="text-sm font-semibold">Buat Tiket Baru</span>
                                    <p class="text-[10px] uppercase tracking-wide text-neutral-400 mt-0.5">Pusat Bantuan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="max-w-2xl mx-auto">
                <!-- Form Card -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-[#fe2c55] via-[#fe2c55]/40 to-[#25f4ee]"></div>
                    
                    <div class="p-6">
                        <form action="{{ route('user.tickets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            
                            <!-- Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-neutral-200 mb-2">
                                    Judul Tiket <span class="text-[#FE2C55]">*</span>
                                </label>
                                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                                       class="w-full px-4 py-3 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-100 placeholder-neutral-400 focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent @error('title') border-red-500 @enderror"
                                       placeholder="Deskripsi singkat masalah Anda">
                                @error('title')
                                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Category and Priority Row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Category -->
                                <div>
                                    <label for="category" class="block text-sm font-medium text-neutral-200 mb-2">
                                        Kategori <span class="text-[#FE2C55]">*</span>
                                    </label>
                                    <select id="category" name="category" required
                                            class="w-full px-4 py-3 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-100 focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent @error('category') border-red-500 @enderror">
                                        <option value="">Pilih kategori</option>
                                        @foreach($categories as $value => $label)
                                            <option value="{{ $value }}" {{ old('category') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Priority -->
                                <div>
                                    <label for="priority" class="block text-sm font-medium text-neutral-200 mb-2">
                                        Prioritas <span class="text-[#FE2C55]">*</span>
                                    </label>
                                    <select id="priority" name="priority" required
                                            class="w-full px-4 py-3 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-100 focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent @error('priority') border-red-500 @enderror">
                                        <option value="">Pilih tingkat prioritas</option>
                                        @foreach($priorities as $value => $label)
                                            <option value="{{ $value }}" {{ old('priority') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('priority')
                                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-neutral-200 mb-2">
                                    Deskripsi <span class="text-[#FE2C55]">*</span>
                                </label>
                                <textarea id="description" name="description" rows="6" required
                                          class="w-full px-4 py-3 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-100 placeholder-neutral-400 focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent @error('description') border-red-500 @enderror"
                                          placeholder="Berikan informasi detail tentang masalah Anda, termasuk langkah-langkah untuk mereproduksi jika ada...">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-sm text-neutral-400">Silakan berikan informasi sejelas mungkin agar kami dapat membantu menyelesaikan masalah Anda dengan cepat.</p>
                            </div>

                            <!-- Attachments -->
                            <div>
                                <label for="attachments" class="block text-sm font-medium text-neutral-200 mb-2">
                                    Lampiran (Opsional)
                                </label>
                                <div class="relative">
                                    <input type="file" id="attachments" name="attachments[]" multiple
                                           class="w-full px-4 py-3 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-100 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-[#FE2C55] file:text-white hover:file:bg-[#FE2C55]/80 focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent @error('attachments') border-red-500 @enderror"
                                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                    @error('attachments')
                                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                                    @enderror
                                    @error('attachments.*')
                                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <p class="mt-2 text-sm text-neutral-400">
                                    Format yang didukung: JPG, PNG, PDF, DOC, DOCX. Maksimal 2MB per file.
                                </p>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-between pt-6 border-t border-neutral-700">
                                <a href="{{ route('user.tickets.index') }}" class="inline-flex items-center px-4 py-2 bg-neutral-700 text-neutral-300 text-sm font-medium rounded-lg hover:bg-neutral-600 transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Kembali
                                </a>
                                
                                <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] text-white text-sm font-medium rounded-lg hover:shadow-lg transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Kirim Tiket
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Help Info -->
                <div class="mt-6 rounded-xl border border-[#2c3136] bg-[#23272b] p-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#FE2C55]/20 to-[#25F4EE]/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-[#25F4EE]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-neutral-200 mb-1">Tips untuk tiket yang efektif:</h3>
                            <ul class="text-sm text-neutral-400 space-y-1">
                                <li>• Berikan deskripsi yang jelas dan detail</li>
                                <li>• Sertakan tangkapan layar jika membantu</li>
                                <li>• Sebutkan langkah-langkah yang telah Anda coba</li>
                                <li>• Pilih kategori dan prioritas yang sesuai</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
