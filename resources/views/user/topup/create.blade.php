<x-app-layout>
    <div class="min-h-screen bg-[#0f1115] text-gray-200 relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_25%_15%,rgba(6,182,212,0.07),transparent_60%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_85%,rgba(59,130,246,0.08),transparent_65%)]"></div>
        
        <!-- Header -->
        <div class="sticky top-0 z-40 backdrop-blur-md bg-[#0f1115]/70 border-b border-white/10">
            <div class="px-4 py-3 flex items-center gap-3">
                <a href="{{ route('user.topup.index') }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:bg-white/5 transition focus:outline-none focus:ring-2 focus:ring-cyan-500/60" aria-label="Kembali">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-semibold text-white leading-tight">Topup Saldo Baru</h1>
                    <p class="text-[11px] text-gray-500 mt-0.5">Isi form untuk membuat permintaan topup saldo.</p>
                </div>
                <div class="flex items-center gap-1 px-2 py-1 rounded-lg bg-gradient-to-r from-cyan-500/10 to-blue-500/10 border border-cyan-500/20">
                    <svg class="w-3 h-3 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-[10px] text-cyan-300 font-medium">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="px-3 sm:px-4 py-4 sm:py-6 max-w-3xl mx-auto">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-emerald-600/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-xl text-sm mb-6">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-600/10 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl text-sm mb-6">{{ session('error') }}</div>
            @endif

            <!-- Main Form -->
            <form method="POST" action="{{ route('user.topup.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- Amount Section -->
                <div class="bg-[#181d23] border border-white/10 rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-cyan-500/20 to-blue-500/20 border border-cyan-500/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Jumlah Topup</h3>
                            <p class="text-xs text-gray-400">Minimum Rp 1.000.000 - Maximum Rp 10.000.000</p>
                        </div>
                    </div>
                    
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-300 mb-2">Nominal Topup</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">Rp</span>
                            </div>
                            <input type="number" 
                                   id="amount" 
                                   name="amount" 
                                   value="{{ old('amount') }}"
                                   min="1000000" 
                                   max="10000000" 
                                   step="1000"
                                   class="w-full pl-8 pr-4 py-3 bg-[#1a1f25] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 transition" 
                                   placeholder="1000000">
                        </div>
                        @error('amount')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Bank Info Section -->
                <div class="bg-[#181d23] border border-white/10 rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500/20 to-indigo-500/20 border border-blue-500/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Informasi Bank</h3>
                            <p class="text-xs text-gray-400">Pilih bank tujuan untuk transfer</p>
                        </div>
                    </div>
                    
                    <!-- Bank Selection -->
                    @if($setting && $setting->account_name && $setting->account_number)
                        <div class="space-y-3">
                            <label class="relative block cursor-pointer">
                                <input type="radio" 
                                       name="bank_selection" 
                                       value="bank|{{ $setting->payment_provider ?? 'Bank Transfer' }} - {{ $setting->account_number }} ({{ $setting->account_name }})"
                                       class="sr-only peer"
                                       checked>
                                <div class="p-4 bg-[#1a1f25] border border-cyan-500/50 bg-cyan-500/5 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-medium text-white">{{ $setting->payment_provider ?? 'Bank Transfer' }}</div>
                                            <div class="text-sm text-gray-400">{{ $setting->account_number }} - {{ $setting->account_name }}</div>
                                        </div>
                                        <div class="w-4 h-4 border-2 border-cyan-500 bg-cyan-500 rounded-full flex items-center justify-center">
                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        
                        <!-- Hidden inputs for selected bank -->
                        <input type="hidden" id="bank_name" name="bank_name" value="{{ $setting->payment_provider ?? 'Bank Transfer' }}">
                        <input type="hidden" id="bank_account" name="bank_account" value="{{ $setting->account_number }} - {{ $setting->account_name }}">
                    @else
                        <div class="text-center py-8 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <p class="text-sm">Bank tujuan belum tersedia. Hubungi admin untuk informasi bank.</p>
                        </div>
                    @endif
                    
                    @error('bank_name')
                        <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Transfer Details Section -->
                <div class="bg-[#181d23] border border-white/10 rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border border-indigo-500/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0l-1 1v10a2 2 0 002 2h4a2 2 0 002-2V8l-1-1M9 12h6m-6 4h6" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Detail Transfer</h3>
                            <p class="text-xs text-gray-400">Upload bukti transfer dan informasi tambahan</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <!-- Payment Proof -->
                        <div>
                            <label for="payment_proof" class="block text-sm font-medium text-gray-300 mb-2">Bukti Transfer</label>
                            <div class="relative">
                                <input type="file" 
                                       id="payment_proof" 
                                       name="payment_proof" 
                                       accept=".jpg,.jpeg,.png,.pdf"
                                       class="hidden"
                                       onchange="updateFilePreview(this)">
                                <label for="payment_proof" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-white/20 rounded-lg cursor-pointer bg-[#1a1f25] hover:bg-white/5 transition">
                                    <div id="file-preview" class="text-center">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="text-sm text-gray-400">Click untuk upload bukti transfer</p>
                                        <p class="text-xs text-gray-500 mt-1">JPG, PNG, PDF (Max 2MB)</p>
                                    </div>
                                </label>
                            </div>
                            @error('payment_proof')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-300 mb-2">Catatan (Opsional)</label>
                            <textarea id="notes" 
                                      name="notes" 
                                      rows="3" 
                                      class="w-full px-4 py-3 bg-[#1a1f25] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 transition" 
                                      placeholder="Tambahkan catatan jika diperlukan...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="bg-[#181d23] border border-white/10 rounded-xl p-6">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('user.topup.index') }}" class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-[#1a1f25] border border-white/10 rounded-lg text-gray-300 hover:bg-white/5 hover:text-white transition">
                            Batal
                        </a>
                        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-cyan-500 via-blue-500 to-indigo-500 text-white rounded-lg hover:from-cyan-500/90 hover:via-blue-500/90 hover:to-indigo-500/90 shadow-sm shadow-cyan-500/30 transition focus:outline-none focus:ring-2 focus:ring-cyan-500/60 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Kirim Permintaan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Handle bank selection
        document.addEventListener('DOMContentLoaded', function() {
            const bankRadios = document.querySelectorAll('input[name="bank_selection"]');
            const bankNameInput = document.getElementById('bank_name');
            const bankAccountInput = document.getElementById('bank_account');
            
            bankRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        const [bankKey, bankValue] = this.value.split('|');
                        const bankInfo = bankValue.split(' - ');
                        bankNameInput.value = bankInfo[0] || bankValue;
                        bankAccountInput.value = bankInfo[1] || '';
                    }
                });
            });
        });

        // File preview function
        function updateFilePreview(input) {
            const preview = document.getElementById('file-preview');
            const file = input.files[0];
            
            if (file) {
                const fileName = file.name;
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                
                preview.innerHTML = `
                    <svg class="w-8 h-8 mx-auto mb-2 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-green-400 font-medium">${fileName}</p>
                    <p class="text-xs text-gray-500 mt-1">${fileSize} MB</p>
                `;
            }
        }
    </script>
</x-app-layout>
