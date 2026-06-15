<div class="space-y-8" x-data="{
    initLottie() {
        if (!document.getElementById('lottie-script')) {
            const script = document.createElement('script');
            script.id = 'lottie-script';
            script.src = 'https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js';
            document.head.appendChild(script);
        }
    }
}" x-init="initLottie()">

    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-on-surface">Group Debt Podium</h2>
        <a href="{{ route('user.group-detail', ['id' => $groupId]) }}" wire:navigate class="text-primary hover:underline">
            Back to Group
        </a>
    </div>

    <!-- Podium Section (total_debt > 0) -->
    @php
        $debtors = array_filter($debts, fn($d) => $d['total_debt'] > 0);
        // The array is already sorted descending, so index 0 is rank 1, index 1 is rank 2, etc.
    @endphp

    @if(count($debtors) > 0)
        <div class="bg-surface-container rounded-xl p-8 shadow-sm">
            <h3 class="text-lg font-semibold text-on-surface mb-6 text-center">Wall of Shame (Debtors)</h3>
            
            <div class="flex flex-col md:flex-row justify-center items-end gap-4 md:gap-8 min-h-[300px]">
                @foreach(array_slice($debtors, 0, 3) as $index => $debtor)
                    @php
                        // Calculate podium height based on rank
                        $rank = $index + 1;
                        $heightClass = $rank === 1 ? 'h-48' : ($rank === 2 ? 'h-40' : 'h-32');
                        $orderClass = $rank === 1 ? 'order-2' : ($rank === 2 ? 'order-1' : 'order-3');
                    @endphp
                    
                    <div class="flex flex-col items-center {{ $orderClass }}">
                        <div class="text-center mb-2">
                            <p class="font-bold text-on-surface">{{ $debtor['name'] }}</p>
                            <p class="text-error font-semibold">Owes ${{ number_format($debtor['total_debt'], 2) }}</p>
                        </div>
                        
                        <!-- Animal Animation for Debtors -->
                        <div class="w-24 h-24 mb-2 relative">
                            <lottie-player 
                                src="https://lottie.host/8e1ea05f-3957-4ce9-8086-42bb33fdf081/i8a9r5oH2p.json" 
                                background="transparent" 
                                speed="1" 
                                style="width: 100%; height: 100%;" 
                                loop 
                                autoplay>
                            </lottie-player>
                        </div>
                        
                        <!-- Podium Box -->
                        <div class="w-32 {{ $heightClass }} bg-primary text-on-primary flex items-start justify-center pt-4 rounded-t-lg shadow-lg relative overflow-hidden">
                            <div class="absolute inset-0 bg-white/10"></div>
                            <span class="text-3xl font-black relative z-10">{{ $rank }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Other Debtors (Rank 4+) -->
            @if(count($debtors) > 3)
                <div class="mt-8 pt-8 border-t border-outline-variant">
                    <h4 class="text-sm font-semibold text-on-surface-variant mb-4 uppercase tracking-wider">Other Debtors</h4>
                    <ul class="space-y-3">
                        @foreach(array_slice($debtors, 3) as $debtor)
                            <li class="flex items-center justify-between bg-background p-4 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-error-container flex items-center justify-center text-on-error-container">
                                        <span class="material-symbols-outlined text-sm">pets</span>
                                    </div>
                                    <span class="font-medium text-on-surface">{{ $debtor['name'] }}</span>
                                </div>
                                <span class="text-error font-bold">${{ number_format($debtor['total_debt'], 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @else
        <div class="bg-surface-container rounded-xl p-8 shadow-sm text-center">
            <p class="text-on-surface-variant">No one is currently in debt. Everyone is all settled up!</p>
        </div>
    @endif

    <!-- Balanced/Receivables Section (total_debt <= 0) -->
    @php
        $balanced = array_filter($debts, fn($d) => $d['total_debt'] <= 0);
        // Sort by receivables (highest negative debt first = highest credit)
        usort($balanced, fn($a, $b) => $a['total_debt'] <=> $b['total_debt']);
    @endphp

    @if(count($balanced) > 0)
        <div class="bg-surface-container rounded-xl p-8 shadow-sm">
            <h3 class="text-lg font-semibold text-on-surface mb-6">Creditors & Settled (Humans)</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($balanced as $person)
                    @php
                        $isCreditor = $person['total_debt'] < 0;
                        $amount = abs($person['total_debt']);
                    @endphp
                    <div class="bg-background border border-outline-variant rounded-xl p-6 flex flex-col items-center text-center hover:shadow-md transition-shadow">
                        <!-- Human Animation -->
                        <div class="w-32 h-32 mb-4">
                            <lottie-player 
                                src="https://lottie.host/bbba72b6-455b-432d-9653-ff8fcbcbc4b9/R9l6Yx9N5m.json" 
                                background="transparent" 
                                speed="1" 
                                style="width: 100%; height: 100%;" 
                                loop 
                                autoplay>
                            </lottie-player>
                        </div>
                        
                        <h4 class="font-bold text-on-surface text-lg">{{ $person['name'] }}</h4>
                        
                        @if($isCreditor)
                            <div class="mt-2 inline-flex items-center gap-1 bg-primary-container text-on-primary-container px-3 py-1 rounded-full text-sm font-medium">
                                <span class="material-symbols-outlined text-[16px]">trending_up</span>
                                Owed ${{ number_format($amount, 2) }}
                            </div>
                        @else
                            <div class="mt-2 inline-flex items-center gap-1 bg-surface-variant text-on-surface-variant px-3 py-1 rounded-full text-sm font-medium">
                                <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                Balanced
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
