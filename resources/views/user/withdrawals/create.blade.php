<x-app-layout>
    <style>
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>
    <div class="min-h-screen bg-[#0f1115] text-gray-200 relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_25%_15%,rgba(255,187,0,0.07),transparent_60%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_85%,rgba(251,146,60,0.08),transparent_65%)]"></div>
        
        <!-- Header -->
        <div class="sticky top-0 z-40 backdrop-blur-md bg-[#0f1115]/70 border-b border-white/10">
            <div class="px-4 py-3 flex items-center gap-3">
                <a href="{{ route('user.withdrawals.index') }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:bg-white/5 transition focus:outline-none focus:ring-2 focus:ring-amber-500/60" aria-label="Kembali">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-semibold text-white leading-tight">Ajukan Penarikan Saldo</h1>
                    <p class="text-[11px] text-gray-500 mt-0.5">Isi form di bawah untuk mengajukan penarikan saldo</p>
                </div>
            </div>
        </div>

        <div class="px-4 py-4 space-y-4">
            <!-- Balance Card -->
            <div class="bg-gradient-to-r from-green-500/10 to-emerald-500/10 border border-green-500/20 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-white mb-1">Saldo Tersedia</h2>
                        <p class="text-2xl font-bold text-green-400">Rp {{ number_format(auth()->user()->balance ?? 0, 0, ',', '.') }}</p>
                        <p class="text-green-300/70 text-[10px] mt-1">Siap untuk ditarik</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-green-500/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                </div>
            </div>

            @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-red-300 font-medium text-sm mb-2">Terdapat kesalahan dalam form:</h4>
                        <ul class="list-disc list-inside text-red-300 text-sm space-y-1">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <!-- Form -->
            <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl overflow-hidden">
                <div class="px-4 py-3 border-b border-white/10">
                    <h2 class="text-sm font-semibold text-white">Informasi Penarikan</h2>
                </div>
                
                <form action="{{ route('user.withdrawals.store') }}" method="POST" class="p-4 space-y-4">
                    @csrf
                    
                    <!-- Account Information -->
                    <div class="space-y-3">
                        <h3 class="text-xs font-medium text-white uppercase tracking-wide">Informasi Rekening</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label for="account_name" class="block text-[11px] font-medium text-gray-300 mb-2">
                                    Nama Pemilik Rekening <span class="text-red-400">*</span>
                                </label>
                                <input type="text" 
                                       name="account_name" 
                                       id="account_name" 
                                       value="{{ old('account_name') }}"
                                       class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition text-sm"
                                       placeholder="Contoh: John Doe"
                                       required>
                                @error('account_name')
                                <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="account_number" class="block text-[11px] font-medium text-gray-300 mb-2">
                                    Nomor Rekening <span class="text-red-400">*</span>
                                </label>
                                <input type="text" 
                                       name="account_number" 
                                       id="account_number" 
                                       value="{{ old('account_number') }}"
                                       class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition text-sm font-mono"
                                       placeholder="Contoh: 1234567890"
                                       required>
                                @error('account_number')
                                <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div>
                            <label for="bank_name" class="block text-[11px] font-medium text-gray-300 mb-2">
                                Nama Bank <span class="text-red-400">*</span>
                            </label>
                            <input type="text" 
                                   name="bank_name" 
                                   id="bank_name" 
                                   value="{{ old('bank_name') }}"
                                   class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition text-sm"
                                   placeholder="Contoh: BCA, BRI, BNI, Mandiri, dll"
                                   required>
                            <p class="text-gray-400 text-[10px] mt-1">Tulis nama bank sesuai yang tertera di rekening Anda</p>
                            @error('bank_name')
                            <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Withdrawal Amount -->
                    <div class="space-y-3">
                        <h3 class="text-xs font-medium text-white uppercase tracking-wide">Jumlah Penarikan</h3>
                        
                        <div>
                            <label for="amount" class="block text-[11px] font-medium text-gray-300 mb-2">
                                Nominal Penarikan <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                                <input type="number" 
                                       name="amount" 
                                       id="amount" 
                                       value="{{ old('amount') }}"
                                       class="w-full pl-10 pr-3 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition text-sm"
                                       placeholder="0"
                                       min="10000"
                                       step="1000"
                                       required>
                            </div>
                            <p class="text-gray-400 text-[10px] mt-1">Minimal penarikan Rp 10.000</p>
                            @error('amount')
                            <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Fee Preview -->
                        <div id="fee-preview" class="hidden bg-white/5 border border-white/10 rounded-lg p-3">
                            <div class="space-y-2 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-gray-300">Jumlah Penarikan:</span>
                                    <span class="text-white font-medium" id="preview-amount">Rp 0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-300">Biaya Admin:</span>
                                    <span class="text-yellow-400 font-medium" id="preview-fee">Rp 0</span>
                                </div>
                                <hr class="border-white/10">
                                <div class="flex justify-between font-semibold">
                                    <span class="text-white">Total Dipotong:</span>
                                    <span class="text-red-400" id="preview-total">Rp 0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-300">Saldo Setelah Penarikan:</span>
                                    <span class="text-green-400 font-medium" id="preview-remaining">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-[11px] font-medium text-gray-300 mb-2">
                            Catatan (Opsional)
                        </label>
                        <textarea name="notes" 
                                  id="notes" 
                                  rows="3"
                                  class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition text-sm resize-none"
                                  placeholder="Tuliskan catatan tambahan jika diperlukan...">{{ old('notes') }}</textarea>
                        @error('notes')
                        <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Terms -->
                    <div class="bg-amber-500/10 border border-amber-500/20 rounded-lg p-3">
                        <h4 class="text-amber-300 font-medium text-xs mb-2">Perhatian:</h4>
                        <ul class="text-amber-200/80 text-[10px] space-y-1">
                            <li>• Penarikan akan diproses dalam 1-3 hari kerja</li>
                            <li>• Pastikan data rekening sudah benar sebelum submit</li>
                            <li>• Biaya admin akan dipotong otomatis dari saldo</li>
                            <li>• Penarikan yang sudah diproses tidak dapat dibatalkan</li>
                        </ul>
                    </div>
                    
                    <!-- Submit Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <button type="button" 
                                onclick="previewWithdrawal()"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-lg font-medium text-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Preview
                        </button>
                        <button type="submit" 
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white rounded-lg font-medium text-sm transition shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Ajukan Penarikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewWithdrawal() {
            const amount = document.getElementById('amount').value;
            const currentBalance = {{ auth()->user()->balance ?? 0 }};
            
            if (!amount || amount < 10000) {
                alert('Masukkan jumlah penarikan minimal Rp 10.000');
                return;
            }
            
            if (amount > currentBalance) {
                alert('Jumlah penarikan melebihi saldo tersedia');
                return;
            }
            
            // Calculate admin fee (example: 2% with min Rp 5000, max Rp 25000)
            const adminFeeRate = 0.02;
            const minFee = 5000;
            const maxFee = 25000;
            let adminFee = Math.max(minFee, Math.min(maxFee, amount * adminFeeRate));
            
            const totalDeducted = parseInt(amount) + adminFee;
            const remainingBalance = currentBalance - totalDeducted;
            
            // Update preview
            document.getElementById('preview-amount').textContent = 'Rp ' + parseInt(amount).toLocaleString('id-ID');
            document.getElementById('preview-fee').textContent = 'Rp ' + adminFee.toLocaleString('id-ID');
            document.getElementById('preview-total').textContent = 'Rp ' + totalDeducted.toLocaleString('id-ID');
            document.getElementById('preview-remaining').textContent = 'Rp ' + remainingBalance.toLocaleString('id-ID');
            
            // Show preview
            document.getElementById('fee-preview').classList.remove('hidden');
            
            // Scroll to preview
            document.getElementById('fee-preview').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // Auto preview on amount change
        document.getElementById('amount').addEventListener('input', function() {
            if (this.value >= 10000) {
                setTimeout(previewWithdrawal, 500);
            } else {
                document.getElementById('fee-preview').classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('user.withdrawals.index') }}" class="inline-flex items-center gap-2 text-neutral-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>
        <h1 class="text-2xl font-bold text-white mb-2">Ajukan Penarikan Saldo</h1>
        <p class="text-neutral-400">Isi form di bawah untuk mengajukan penarikan saldo</p>
    </div>

    <!-- Balance Card -->
    <div class="bg-gradient-to-r from-green-600/20 to-emerald-600/20 border border-green-500/30 rounded-xl p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-white mb-1">Saldo Tersedia</h2>
                <p class="text-3xl font-bold text-green-400">Rp {{ number_format(auth()->user()->balance ?? 0, 0, ',', '.') }}</p>
                <p class="text-green-300/70 text-sm mt-1">Siap untuk ditarik</p>
            </div>
            <div class="w-16 h-16 rounded-full bg-green-500/20 flex items-center justify-center">
                <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-900/50 border border-red-500/50 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            <div>
                <h4 class="text-red-300 font-medium mb-1">Terdapat kesalahan dalam form:</h4>
                <ul class="list-disc list-inside text-red-300 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Form -->
    <div class="bg-neutral-800/30 border border-neutral-700 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-neutral-700">
            <h2 class="text-lg font-semibold text-white">Informasi Penarikan</h2>
        </div>
        
        <form action="{{ route('user.withdrawals.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <!-- Account Information -->
            <div class="space-y-4">
                <h3 class="text-sm font-medium text-white">Informasi Rekening</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="account_name" class="block text-sm font-medium text-neutral-300 mb-2">
                            Nama Pemilik Rekening <span class="text-red-400">*</span>
                        </label>
                        <input type="text" 
                               name="account_name" 
                               id="account_name" 
                               value="{{ old('account_name') }}"
                               class="w-full px-4 py-3 bg-neutral-700 border border-neutral-600 rounded-lg text-white placeholder-neutral-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
                               placeholder="Contoh: John Doe"
                               required>
                        @error('account_name')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="account_number" class="block text-sm font-medium text-neutral-300 mb-2">
                            Nomor Rekening <span class="text-red-400">*</span>
                        </label>
                        <input type="text" 
                               name="account_number" 
                               id="account_number" 
                               value="{{ old('account_number') }}"
                               class="w-full px-4 py-3 bg-neutral-700 border border-neutral-600 rounded-lg text-white placeholder-neutral-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
                               placeholder="Contoh: 1234567890"
                               required>
                        @error('account_number')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <label for="bank_name" class="block text-sm font-medium text-neutral-300 mb-2">
                        Nama Bank <span class="text-red-400">*</span>
                    </label>
                    <select name="bank_name" 
                            id="bank_name" 
                            class="w-full px-4 py-3 bg-neutral-700 border border-neutral-600 rounded-lg text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
                            required>
                        <option value="">Pilih Bank</option>
                        <option value="BCA" {{ old('bank_name') === 'BCA' ? 'selected' : '' }}>Bank Central Asia (BCA)</option>
                        <option value="BRI" {{ old('bank_name') === 'BRI' ? 'selected' : '' }}>Bank Rakyat Indonesia (BRI)</option>
                        <option value="BNI" {{ old('bank_name') === 'BNI' ? 'selected' : '' }}>Bank Negara Indonesia (BNI)</option>
                        <option value="Mandiri" {{ old('bank_name') === 'Mandiri' ? 'selected' : '' }}>Bank Mandiri</option>
                        <option value="CIMB Niaga" {{ old('bank_name') === 'CIMB Niaga' ? 'selected' : '' }}>CIMB Niaga</option>
                        <option value="Danamon" {{ old('bank_name') === 'Danamon' ? 'selected' : '' }}>Bank Danamon</option>
                        <option value="Permata" {{ old('bank_name') === 'Permata' ? 'selected' : '' }}>Bank Permata</option>
                        <option value="BTN" {{ old('bank_name') === 'BTN' ? 'selected' : '' }}>Bank Tabungan Negara (BTN)</option>
                        <option value="BSI" {{ old('bank_name') === 'BSI' ? 'selected' : '' }}>Bank Syariah Indonesia (BSI)</option>
                        <option value="Muamalat" {{ old('bank_name') === 'Muamalat' ? 'selected' : '' }}>Bank Muamalat</option>
                        <option value="OCBC NISP" {{ old('bank_name') === 'OCBC NISP' ? 'selected' : '' }}>Bank OCBC NISP</option>
                        <option value="Maybank" {{ old('bank_name') === 'Maybank' ? 'selected' : '' }}>Maybank Indonesia</option>
                        <option value="Lainnya" {{ old('bank_name') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('bank_name')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <hr class="border-neutral-700">
            
            <!-- Withdrawal Amount -->
            <div class="space-y-4">
                <h3 class="text-sm font-medium text-white">Jumlah Penarikan</h3>
                
                <div>
                    <label for="amount" class="block text-sm font-medium text-neutral-300 mb-2">
                        Nominal Penarikan <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400">Rp</span>
                        <input type="number" 
                               name="amount" 
                               id="amount" 
                               value="{{ old('amount') }}"
                               class="w-full pl-12 pr-4 py-3 bg-neutral-700 border border-neutral-600 rounded-lg text-white placeholder-neutral-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
                               placeholder="0"
                               min="10000"
                               step="1000"
                               required>
                    </div>
                    <p class="text-neutral-400 text-xs mt-1">Minimal penarikan Rp 10.000</p>
                    @error('amount')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Fee Preview -->
                <div id="fee-preview" class="hidden bg-neutral-700/50 border border-neutral-600 rounded-lg p-4">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-neutral-300">Jumlah Penarikan:</span>
                            <span class="text-white font-medium" id="preview-amount">Rp 0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-neutral-300">Biaya Admin:</span>
                            <span class="text-yellow-400 font-medium" id="preview-fee">Rp 0</span>
                        </div>
                        <hr class="border-neutral-600">
                        <div class="flex justify-between font-semibold">
                            <span class="text-white">Total Dipotong:</span>
                            <span class="text-red-400" id="preview-total">Rp 0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-neutral-300">Saldo Setelah Penarikan:</span>
                            <span class="text-green-400 font-medium" id="preview-remaining">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="border-neutral-700">
            
            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-neutral-300 mb-2">
                    Catatan (Opsional)
                </label>
                <textarea name="notes" 
                          id="notes" 
                          rows="3"
                          class="w-full px-4 py-3 bg-neutral-700 border border-neutral-600 rounded-lg text-white placeholder-neutral-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
                          placeholder="Tuliskan catatan tambahan jika diperlukan...">{{ old('notes') }}</textarea>
                @error('notes')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Terms -->
            <div class="bg-amber-900/20 border border-amber-500/30 rounded-lg p-4">
                <h4 class="text-amber-300 font-medium mb-2">Perhatian:</h4>
                <ul class="text-amber-200/80 text-sm space-y-1">
                    <li>• Penarikan akan diproses dalam 1-3 hari kerja</li>
                    <li>• Pastikan data rekening sudah benar sebelum submit</li>
                    <li>• Biaya admin akan dipotong otomatis dari saldo</li>
                    <li>• Penarikan yang sudah diproses tidak dapat dibatalkan</li>
                </ul>
            </div>
            
            <!-- Submit Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6">
                <button type="button" 
                        onclick="previewWithdrawal()"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-neutral-700 hover:bg-neutral-600 text-white rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Preview
                </button>
                <button type="submit" 
                        class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-amber-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajukan Penarikan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewWithdrawal() {
    const amount = document.getElementById('amount').value;
    const currentBalance = {{ auth()->user()->balance ?? 0 }};
    
    if (!amount || amount < 10000) {
        alert('Masukkan jumlah penarikan minimal Rp 10.000');
        return;
    }
    
    if (amount > currentBalance) {
        alert('Jumlah penarikan melebihi saldo tersedia');
        return;
    }
    
    // Calculate admin fee (example: 2% with min Rp 5000, max Rp 25000)
    const adminFeeRate = 0.02;
    const minFee = 5000;
    const maxFee = 25000;
    let adminFee = Math.max(minFee, Math.min(maxFee, amount * adminFeeRate));
    
    const totalDeducted = parseInt(amount) + adminFee;
    const remainingBalance = currentBalance - totalDeducted;
    
    // Update preview
    document.getElementById('preview-amount').textContent = 'Rp ' + parseInt(amount).toLocaleString('id-ID');
    document.getElementById('preview-fee').textContent = 'Rp ' + adminFee.toLocaleString('id-ID');
    document.getElementById('preview-total').textContent = 'Rp ' + totalDeducted.toLocaleString('id-ID');
    document.getElementById('preview-remaining').textContent = 'Rp ' + remainingBalance.toLocaleString('id-ID');
    
    // Show preview
    document.getElementById('fee-preview').classList.remove('hidden');
    
    // Scroll to preview
    document.getElementById('fee-preview').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// Auto preview on amount change
document.getElementById('amount').addEventListener('input', function() {
    if (this.value >= 10000) {
        setTimeout(previewWithdrawal, 500);
    } else {
        document.getElementById('fee-preview').classList.add('hidden');
    }
});
</script>

