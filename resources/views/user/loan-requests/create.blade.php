<x-app-layout>
    @php($user = auth()->user())
    @php($initials = collect(explode(' ', trim($user->full_name ?: $user->username)))->filter()->take(2)->map(fn($p)=> strtoupper(mb_substr($p,0,1)))->implode(''))
    <div class="min-h-screen bg-[#1a1d21] text-neutral-100">
        <!-- Header Section with User Info -->
        <div class="sticky top-0 z-40 backdrop-blur bg-[#1f2226]/95 border-b border-neutral-800/70">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
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
                                <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center bg-[#FE2C55] text-white text-xs font-bold shadow-[0_0_0_2px_#000]">💰</div>
                            </div>
                            <div>
                                @if(auth()->user()->isSeller() && auth()->user()->sellerInfo)
                                    <h1 class="text-xl font-semibold">{{ auth()->user()->full_name }}</h1>
                                    <p class="text-sm font-medium bg-clip-text text-transparent bg-gradient-to-r from-[#FE2C55] to-[#25F4EE]">{{ auth()->user()->sellerInfo->store_name }}</p>
                                @else
                                    <h1 class="text-xl font-semibold">{{ auth()->user()->full_name }}</h1>
                                @endif
                                <!-- Page Title -->
                                <div class="flex items-center space-x-6 mt-2">
                                    <div class="text-left">
                                        <span class="text-sm font-semibold">Pengajuan Pinjaman Baru</span>
                                        <p class="text-[10px] uppercase tracking-wide text-neutral-400 mt-0.5">Pembiayaan Usaha</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <!-- Back Button -->
                            <a href="{{ route('user.loan-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-neutral-700 text-neutral-300 text-sm font-medium rounded-lg hover:bg-neutral-600 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Kembali ke Daftar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <form method="POST" action="{{ route('user.loan-requests.store') }}" enctype="multipart/form-data" class="max-w-4xl mx-auto">
                @csrf
                
                <!-- Loan Information Section -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-6 mb-6">
                    <div class="h-1 w-full bg-gradient-to-r from-[#fe2c55] via-[#fe2c55]/40 to-[#25f4ee] absolute top-0 left-0 -mt-6 rounded-t-xl"></div>
                    
                    <h2 class="text-xl font-semibold mb-6 text-neutral-100">Informasi Pinjaman</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Loan Amount -->
                        <div>
                            <label for="amount_requested" class="block text-sm font-medium text-neutral-300 mb-2">
                                Jumlah Pinjaman (Rp) <span class="text-red-400">*</span>
                            </label>
                            <input type="number" 
                                   id="amount_requested" 
                                   name="amount_requested" 
                                   min="1000000" 
                                   max="1000000000" 
                                   step="100000"
                                   value="{{ old('amount_requested') }}"
                                   class="w-full px-4 py-3 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-100 placeholder-neutral-400 focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent @error('amount_requested') border-red-500 @enderror"
                                   placeholder="e.g., 10000000">
                            @error('amount_requested')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-neutral-400">Minimum: Rp 1.000.000 - Maksimum: Rp 1.000.000.000</p>
                        </div>

                        <!-- Duration -->
                        <div>
                            <label for="duration_months" class="block text-sm font-medium text-neutral-300 mb-2">
                                Jangka Waktu (Bulan) <span class="text-red-400">*</span>
                            </label>
                            <select id="duration_months" 
                                    name="duration_months" 
                                    class="w-full px-4 py-3 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-100 focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent @error('duration_months') border-red-500 @enderror">
                                <option value="">Pilih jangka waktu</option>
                                <option value="3" {{ old('duration_months') == '3' ? 'selected' : '' }}>3 bulan</option>
                                <option value="6" {{ old('duration_months') == '6' ? 'selected' : '' }}>6 bulan</option>
                                <option value="12" {{ old('duration_months') == '12' ? 'selected' : '' }}>1 tahun</option>
                                <option value="24" {{ old('duration_months') == '24' ? 'selected' : '' }}>2 tahun</option>
                                <option value="36" {{ old('duration_months') == '36' ? 'selected' : '' }}>3 tahun</option>
                                <option value="48" {{ old('duration_months') == '48' ? 'selected' : '' }}>4 tahun</option>
                                <option value="60" {{ old('duration_months') == '60' ? 'selected' : '' }}>5 tahun</option>
                            </select>
                            @error('duration_months')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Purpose -->
                        <div>
                            <label for="purpose" class="block text-sm font-medium text-neutral-300 mb-2">
                                Tujuan Pinjaman <span class="text-red-400">*</span>
                            </label>
                            <select id="purpose" 
                                    name="purpose" 
                                    class="w-full px-4 py-3 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-100 focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent @error('purpose') border-red-500 @enderror">
                                <option value="">Pilih tujuan</option>
                                @foreach($purposes as $key => $label)
                                    <option value="{{ $key }}" {{ old('purpose') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('purpose')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Monthly Payment Preview -->
                        <div>
                            <label class="block text-sm font-medium text-neutral-300 mb-2">
                                Estimasi Cicilan Bulanan
                            </label>
                            <div id="monthly-payment-preview" class="w-full px-4 py-3 bg-neutral-700 border border-neutral-600 rounded-lg text-neutral-300">
                                <span class="text-[#25F4EE] font-semibold">Rp 0</span>
                                <span class="text-xs text-neutral-400 ml-2">(akan dihitung otomatis)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Purpose Description -->
                    <div class="mt-6">
                        <label for="purpose_description" class="block text-sm font-medium text-neutral-300 mb-2">
                            Deskripsi Detail <span class="text-red-400">*</span>
                        </label>
                        <textarea id="purpose_description" 
                                  name="purpose_description" 
                                  rows="4" 
                                  maxlength="1000"
                                  class="w-full px-4 py-3 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-100 placeholder-neutral-400 focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent @error('purpose_description') border-red-500 @enderror"
                                  placeholder="Mohon berikan informasi detail tentang bagaimana Anda berencana menggunakan pinjaman ini...">{{ old('purpose_description') }}</textarea>
                        @error('purpose_description')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-neutral-400">Maksimal 1.000 karakter</p>
                    </div>
                </div>

                <!-- Document Upload Section -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-6 text-neutral-100">Dokumen Pendukung</h2>
                    
                    <div>
                        <label for="documents" class="block text-sm font-medium text-neutral-300 mb-2">
                            Unggah Dokumen (Opsional)
                        </label>
                        <input type="file" 
                               id="documents" 
                               name="documents[]" 
                               multiple 
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full px-4 py-3 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-100 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#FE2C55] file:text-white hover:file:bg-[#FE2C55]/80 @error('documents.*') border-red-500 @enderror">
                        @error('documents.*')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-neutral-400">
                            Format yang diterima: PDF, JPG, JPEG, PNG. Maksimal 2MB per file.
                            <br>Direkomendasikan: Izin usaha, laporan keuangan, copy KTP, bukti penghasilan.
                        </p>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="rounded-xl border border-[#2c3136] bg-[#23272b] p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4 text-neutral-100">Syarat & Ketentuan</h2>
                    
                    <div class="space-y-3 text-sm text-neutral-300">
                        <div class="flex items-start space-x-2">
                            <svg class="w-5 h-5 text-[#25F4EE] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p>Suku bunga dihitung berdasarkan profil kredit dan jumlah pinjaman Anda.</p>
                        </div>
                        <div class="flex items-start space-x-2">
                            <svg class="w-5 h-5 text-[#25F4EE] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p>Persetujuan pinjaman tergantung pada penilaian kredit dan verifikasi.</p>
                        </div>
                        <div class="flex items-start space-x-2">
                            <svg class="w-5 h-5 text-[#25F4EE] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p>Anda hanya dapat memiliki satu pengajuan pinjaman yang pending pada satu waktu.</p>
                        </div>
                        <div class="flex items-start space-x-2">
                            <svg class="w-5 h-5 text-[#25F4EE] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p>Opsi pelunasan dipercepat tersedia tanpa penalti.</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between">
                    <a href="{{ route('user.loan-requests.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-neutral-700 text-neutral-300 font-medium rounded-lg hover:bg-neutral-600 transition">
                        Batal
                    </a>
                    
                    <button type="submit" 
                            class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-[#FE2C55] to-[#25F4EE] text-white font-medium rounded-lg hover:shadow-lg transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Ajukan Pinjaman
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript for Monthly Payment Calculation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('amount_requested');
            const durationSelect = document.getElementById('duration_months');
            const monthlyPaymentDiv = document.getElementById('monthly-payment-preview');

            function calculateMonthlyPayment() {
                const amount = parseFloat(amountInput.value) || 0;
                const duration = parseInt(durationSelect.value) || 0;
                
                if (amount > 0 && duration > 0) {
                    // Estimate interest rate (this would be calculated server-side)
                    const annualInterestRate = 12; // 12% base rate
                    const monthlyInterestRate = annualInterestRate / 100 / 12;
                    
                    const monthlyPayment = amount * 
                        (monthlyInterestRate * Math.pow(1 + monthlyInterestRate, duration)) /
                        (Math.pow(1 + monthlyInterestRate, duration) - 1);
                    
                    monthlyPaymentDiv.innerHTML = `
                        <span class="text-[#25F4EE] font-semibold">Rp ${monthlyPayment.toLocaleString('id-ID', {maximumFractionDigits: 0})}</span>
                        <span class="text-xs text-neutral-400 ml-2">(estimasi dengan bunga ${annualInterestRate}% per tahun)</span>
                    `;
                } else {
                    monthlyPaymentDiv.innerHTML = `
                        <span class="text-[#25F4EE] font-semibold">Rp 0</span>
                        <span class="text-xs text-neutral-400 ml-2">(akan dihitung otomatis)</span>
                    `;
                }
            }

            amountInput.addEventListener('input', calculateMonthlyPayment);
            durationSelect.addEventListener('change', calculateMonthlyPayment);
        });
    </script>
</x-app-layout>
