@php
$user = auth()->user();
$levelRequirements = \App\Models\User::getLevelRequirements();
$currentLevel = $user->level;
$currentLevelData = $user->getCurrentLevelData();
$currentProgress = $user->getNextLevelProgress();
$amountNeeded = $user->getAmountNeededForNextLevel();
$currentTransactionAmount = $user->total_transaction_amount;

$levelColors = [
    1 => 'from-gray-400 to-gray-600', // Bronze
    2 => 'from-slate-400 to-slate-600', // Silver
    3 => 'from-yellow-400 to-yellow-600', // Gold
    4 => 'from-emerald-400 to-emerald-600', // Platinum
    5 => 'from-cyan-400 to-blue-600', // Diamond
    6 => 'from-purple-500 via-pink-500 to-yellow-500', // Master - Super Special Rainbow
    10 => 'from-red-400 to-red-600', // Admin
];

$levelIcons = [
    1 => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />',
    2 => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />',
    3 => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z" />',
    4 => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />',
    5 => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />',
    6 => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />',
    10 => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />',
];
@endphp

<div class="rounded-xl mb-6 border border-[#2c3136] bg-[#23272b] shadow-sm relative overflow-hidden">
    <div class="h-1 w-full bg-gradient-to-r from-[#fe2c55] via-[#fe2c55]/40 to-[#25f4ee]"></div>
    
    <!-- Current Level Status -->
    <div class="p-5 pb-4">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                @if($currentLevel == 6)
                    <!-- Super Special Level 6 Display -->
                    <div class="relative">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-r {{ $levelColors[$currentLevel] }} p-1 animate-pulse">
                            <div class="w-full h-full rounded-xl bg-[#23272b] flex items-center justify-center relative overflow-hidden">
                                <!-- Animated background sparkles -->
                                <div class="absolute inset-0 opacity-30">
                                    <div class="absolute top-2 left-2 w-1 h-1 bg-yellow-300 rounded-full animate-ping"></div>
                                    <div class="absolute top-4 right-3 w-1 h-1 bg-pink-300 rounded-full animate-ping" style="animation-delay: 0.5s;"></div>
                                    <div class="absolute bottom-3 left-3 w-1 h-1 bg-purple-300 rounded-full animate-ping" style="animation-delay: 1s;"></div>
                                    <div class="absolute bottom-2 right-2 w-1 h-1 bg-cyan-300 rounded-full animate-ping" style="animation-delay: 1.5s;"></div>
                                </div>
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    {!! $levelIcons[$currentLevel] !!}
                                </svg>
                            </div>
                        </div>
                        <!-- Crown effect -->
                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center">
                            <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732L14.146 12.8l-1.179 4.456a1 1 0 01-1.934 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732L9.854 7.2l1.179-4.456A1 1 0 0112 2z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                @else
                    <!-- Regular Level Display -->
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-r {{ $levelColors[$currentLevel] }} p-0.5">
                        <div class="w-full h-full rounded-xl bg-[#23272b] flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $levelIcons[$currentLevel] !!}
                            </svg>
                        </div>
                    </div>
                @endif
                <div>
                    @if($currentLevel == 6)
                        <!-- Super Special Level 6 Title -->
                        <h3 class="text-xl font-bold bg-gradient-to-r from-purple-400 via-pink-400 to-yellow-400 bg-clip-text text-transparent animate-pulse">
                            {{ $levelRequirements[$currentLevel]['badge'] }}
                        </h3>
                        <p class="text-xs text-transparent bg-gradient-to-r from-yellow-400 to-pink-400 bg-clip-text font-medium">
                            🏆 Komisi Margin: {{ $levelRequirements[$currentLevel]['margin_percent'] }}% - Status Tertinggi! 🏆
                        </p>
                    @else
                        <!-- Regular Level Title -->
                        <h3 class="text-lg font-bold text-white">
                            {{ $levelRequirements[$currentLevel]['badge'] }}
                        </h3>
                        <p class="text-xs text-neutral-400">
                            @if($levelRequirements[$currentLevel]['margin_percent'])
                                Komisi Margin: {{ $levelRequirements[$currentLevel]['margin_percent'] }}%
                            @endif
                        </p>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs text-neutral-500 mb-1">Total Transaksi</p>
                <p class="text-lg font-bold text-white">Rp{{ number_format($currentTransactionAmount, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Progress Bar to Next Level -->
        @if($amountNeeded !== null)
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-neutral-400">
                        @if($currentLevel + 1 == 6)
                            Progress ke {{ $levelRequirements[6]['badge'] }}
                        @else
                            Progress ke {{ $levelRequirements[$currentLevel + 1]['badge'] }}
                        @endif
                    </span>
                    <span class="text-xs text-neutral-400">{{ round($currentProgress, 1) }}%</span>
                </div>
                <div class="w-full bg-[#2c3136] rounded-full h-2.5">
                    <div class="bg-gradient-to-r from-[#fe2c55] to-[#25f4ee] h-2.5 rounded-full transition-all duration-700" 
                         style="width: {{ $currentProgress }}%"></div>
                </div>
            </div>
        @else
            <div class="text-center py-2">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-gradient-to-r from-purple-500/20 to-purple-600/20 border border-purple-500/30">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    <span class="text-purple-400 text-sm font-medium">Level Maksimum Tercapai!</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Horizontal Scrollable Level Requirements -->
    <div class="border-t border-[#2c3136] bg-[#1e2226]">
        <div class="p-4">
            <h4 class="text-sm font-semibold text-white mb-3 flex items-center">
                <svg class="w-4 h-4 mr-2 text-[#fe2c55]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m-4-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Syarat & Benefit Level
            </h4>
            
            <!-- Scrollable Cards -->
            <div class="flex space-x-4 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-[#fe2c55]/30 scrollbar-track-transparent">
                @foreach($levelRequirements as $level => $requirement)
                    @php
                        $isCurrentLevel = $level === $currentLevel;
                        $isAchieved = $currentTransactionAmount >= $requirement['transaction_amount'];
                        $isNextLevel = $level === $currentLevel + 1;
                        
                        // Special styling patterns
                        if ($level == 6) {
                            $cardClass = $isCurrentLevel 
                                ? 'border-purple-500 bg-gradient-to-br from-purple-500/10 via-pink-500/10 to-yellow-500/10 shadow-lg shadow-purple-500/20' 
                                : ($isAchieved 
                                    ? 'border-purple-400/50 bg-gradient-to-br from-purple-400/5 via-pink-400/5 to-yellow-400/5' 
                                    : 'border-purple-300/30 bg-gradient-to-br from-purple-300/5 via-pink-300/5 to-yellow-300/5');
                        } elseif (in_array($level, [4, 5])) {
                            // Premium levels 4-5
                            $cardClass = $isCurrentLevel 
                                ? 'border-[#fe2c55] bg-[#fe2c55]/10 shadow-md shadow-[#fe2c55]/20' 
                                : ($isAchieved 
                                    ? 'border-emerald-500/60 bg-emerald-500/10' 
                                    : ($isNextLevel 
                                        ? 'border-[#25f4ee]/60 bg-[#25f4ee]/10' 
                                        : 'border-slate-500/40 bg-slate-500/5'));
                        } else {
                            // Basic levels 1-3
                            $cardClass = $isCurrentLevel 
                                ? 'border-[#fe2c55] bg-[#fe2c55]/5' 
                                : ($isAchieved 
                                    ? 'border-emerald-500/50 bg-emerald-500/5' 
                                    : ($isNextLevel 
                                        ? 'border-[#25f4ee]/50 bg-[#25f4ee]/5' 
                                        : 'border-[#2c3136] bg-[#23272b]'));
                        }
                    @endphp
                    
                    <div class="flex-shrink-0 w-48 p-3 rounded-lg border transition-all {{ $cardClass }}">
                        <!-- Level Header -->
                        <div class="flex items-center space-x-2 mb-2">
                            @if($level == 6)
                                <!-- Super Special Level 6 Icon -->
                                <div class="relative">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-r {{ $levelColors[$level] }} p-0.5 animate-pulse">
                                        <div class="w-full h-full rounded-xl bg-[#1e2226] flex items-center justify-center relative overflow-hidden">
                                            <!-- Mini sparkles -->
                                            <div class="absolute inset-0 opacity-40">
                                                <div class="absolute top-1 left-1 w-0.5 h-0.5 bg-yellow-300 rounded-full animate-ping"></div>
                                                <div class="absolute top-2 right-1.5 w-0.5 h-0.5 bg-pink-300 rounded-full animate-ping" style="animation-delay: 0.3s;"></div>
                                                <div class="absolute bottom-1.5 left-1.5 w-0.5 h-0.5 bg-purple-300 rounded-full animate-ping" style="animation-delay: 0.6s;"></div>
                                            </div>
                                            <svg class="w-5 h-5 text-white relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                {!! $levelIcons[$level] !!}
                                            </svg>
                                        </div>
                                    </div>
                                    <!-- Mini crown -->
                                    <div class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center">
                                        <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                    </div>
                                </div>
                            @else
                                <!-- Regular Level Icon -->
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-r {{ $levelColors[$level] }} p-0.5">
                                    <div class="w-full h-full rounded-lg bg-[#1e2226] flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            {!! $levelIcons[$level] !!}
                                        </svg>
                                    </div>
                                </div>
                            @endif
                            <div>
                                @if($level == 6)
                                    <!-- Super Special Level 6 Title -->
                                    <h5 class="text-sm font-bold bg-gradient-to-r from-purple-400 via-pink-400 to-yellow-400 bg-clip-text text-transparent">
                                        {{ $requirement['badge'] }}
                                    </h5>
                                    <p class="text-xs text-transparent bg-gradient-to-r from-yellow-400 to-pink-400 bg-clip-text font-medium">
                                        Status Tertinggi
                                    </p>
                                @else
                                    <!-- Regular Level Title -->
                                    <h5 class="text-sm font-bold text-white">{{ $requirement['badge'] }}</h5>
                                    <p class="text-xs text-{{ $isCurrentLevel ? '[#fe2c55]' : ($isAchieved ? 'emerald-400' : 'neutral-400') }}">
                                        Level {{ $level }}
                                    </p>
                                @endif
                            </div>
                            @if($isCurrentLevel)
                                <div class="ml-auto">
                                    <div class="w-2 h-2 bg-[#fe2c55] rounded-full animate-pulse"></div>
                                </div>
                            @elseif($isAchieved)
                                <div class="ml-auto">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Requirements -->
                        <div class="space-y-2 mb-3">
                            <div class="text-xs">
                                <span class="text-neutral-500">Syarat:</span>
                                <div class="text-white font-medium">
                                    @if($requirement['transaction_amount'] > 0)
                                        Rp{{ number_format($requirement['transaction_amount'], 0, ',', '.') }}
                                    @else
                                        Default
                                    @endif
                                </div>
                                <span class="text-neutral-600 text-[10px]">total transaksi</span>
                            </div>
                        </div>

                        <!-- Benefits -->
                        <div class="space-y-1">
                            <span class="text-xs text-neutral-500">Benefit:</span>
                            <div class="text-xs space-y-1">
                                @if($level == 6)
                                    <!-- Super Special Level 6 Benefits -->
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-3 h-3 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                        <span class="text-transparent bg-gradient-to-r from-yellow-400 to-pink-400 bg-clip-text font-medium">{{ $requirement['margin_percent'] }}% - Margin Tertinggi</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-3 h-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2H5z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                                        </svg>
                                        <span class="text-transparent bg-gradient-to-r from-purple-400 to-cyan-400 bg-clip-text font-medium">Priority Support</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-3 h-3 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 713.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 713.138-3.138z" />
                                        </svg>
                                        <span class="text-transparent bg-gradient-to-r from-pink-400 to-purple-400 bg-clip-text font-bold">{{ $requirement['badge'] }}</span>
                                    </div>
                                @else
                                    <!-- Regular Benefits -->
                                    @if($requirement['margin_percent'])
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                            </svg>
                                            <span class="text-emerald-400 font-medium">{{ $requirement['margin_percent'] }}% Margin</span>
                                        </div>
                                    @else
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-3 h-3 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span class="text-neutral-400">Admin Setting</span>
                                        </div>
                                    @endif
                                    
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 714.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 713.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 713.138-3.138z" />
                                        </svg>
                                        <span class="text-blue-400">Badge {{ $requirement['badge'] }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
