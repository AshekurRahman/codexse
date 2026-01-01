@php
    $showSelector = \App\Filament\Admin\Pages\CurrencySettings::shouldShowCurrencySelector();
    $currencies = \App\Models\Currency::getActive();
    $currentCurrency = current_currency();
@endphp

@if($showSelector && $currencies->count() > 1)
<div x-data="{ open: false }" class="relative">
    <button
        @click="open = !open"
        @click.outside="open = false"
        type="button"
        class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-surface-600 dark:text-surface-300 hover:text-surface-900 dark:hover:text-white rounded-lg hover:bg-surface-100 dark:hover:bg-surface-700 transition-colors border border-surface-200 dark:border-surface-700"
    >
        <span class="text-base">{{ $currentCurrency->symbol }}</span>
        <span class="font-semibold">{{ $currentCurrency->code }}</span>
        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 z-50 mt-2 w-36 origin-top-right rounded-xl bg-white dark:bg-surface-800 shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-surface-700 focus:outline-none"
        style="display: none;"
    >
        <div class="py-1 max-h-80 overflow-y-auto">
            @foreach($currencies as $currency)
                <form action="{{ route('currency.switch') }}" method="POST" class="block">
                    @csrf
                    <input type="hidden" name="currency" value="{{ $currency->code }}">
                    <button
                        type="submit"
                        class="w-full flex items-center justify-between px-4 py-2 text-sm hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors {{ $currency->code === $currentCurrency->code ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-surface-700 dark:text-surface-300' }}"
                    >
                        <span class="flex items-center gap-1.5 font-medium">
                            <span class="text-base">{{ $currency->symbol }}</span>
                            <span>{{ $currency->code }}</span>
                        </span>
                        @if($currency->code === $currentCurrency->code)
                            <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </button>
                </form>
            @endforeach
        </div>
    </div>
</div>
@endif
