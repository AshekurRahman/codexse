<div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
        <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($subscribers) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Active Subscribers</div>
        </div>
        <div class="text-center p-3 bg-green-50 dark:bg-green-900/30 rounded-lg">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($dailyLimit) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Day 1 Limit</div>
        </div>
        <div class="text-center p-3 bg-purple-50 dark:bg-purple-900/30 rounded-lg">
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($totalCapacity) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Total Capacity</div>
        </div>
        <div class="text-center p-3 bg-orange-50 dark:bg-orange-900/30 rounded-lg">
            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $days }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Days Configured</div>
        </div>
    </div>

    @if($increment > 0)
        <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Daily Schedule Preview:</div>
            <div class="flex flex-wrap gap-2">
                @for($i = 0; $i < min($days, 7); $i++)
                    <div class="px-3 py-1 bg-white dark:bg-gray-700 rounded-full text-sm border border-gray-200 dark:border-gray-600">
                        <span class="text-gray-500 dark:text-gray-400">Day {{ $i + 1 }}:</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ number_format($dailyLimit + ($increment * $i)) }}</span>
                    </div>
                @endfor
                @if($days > 7)
                    <div class="px-3 py-1 bg-gray-100 dark:bg-gray-600 rounded-full text-sm text-gray-500 dark:text-gray-400">
                        +{{ $days - 7 }} more days...
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="flex items-center gap-3 p-3 rounded-lg {{ $willComplete ? 'bg-green-50 dark:bg-green-900/20' : 'bg-yellow-50 dark:bg-yellow-900/20' }}">
        @if($willComplete)
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <div>
                <span class="font-medium text-green-700 dark:text-green-400">Campaign will complete</span>
                <span class="text-gray-600 dark:text-gray-400">within {{ $days }} days.</span>
                <span class="text-gray-500 dark:text-gray-500 text-sm">({{ number_format($totalCapacity - $subscribers) }} capacity remaining)</span>
            </div>
        @else
            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <span class="font-medium text-yellow-700 dark:text-yellow-400">Needs more time!</span>
                <span class="text-gray-600 dark:text-gray-400">Estimated {{ $estimatedDays }} days to reach all subscribers.</span>
                <span class="text-gray-500 dark:text-gray-500 text-sm">({{ number_format($subscribers - $totalCapacity) }} subscribers won't receive)</span>
            </div>
        @endif
    </div>
</div>
