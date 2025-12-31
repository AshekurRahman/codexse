@props(['service'])

@php
    $startingPrice = $service->packages->first()?->price ?? 0;
    $deliveryDays = $service->packages->first()?->delivery_days ?? 0;
@endphp

<div class="group rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden hover:shadow-xl hover:shadow-primary-500/5 dark:hover:shadow-primary-500/10 hover:border-primary-200 dark:hover:border-primary-800 transition-all duration-300">
    <!-- Thumbnail -->
    <a href="{{ route('services.show', $service) }}" class="block relative aspect-[16/10] overflow-hidden">
        @if($service->thumbnail)
            <img src="{{ asset('storage/' . $service->thumbnail) }}" alt="{{ $service->name }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                loading="lazy">
        @else
            <div class="w-full h-full bg-gradient-to-br from-primary-500 to-indigo-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
        @endif

        @if($service->is_featured)
            <div class="absolute top-3 left-3">
                <x-badge type="featured" icon="star">Featured</x-badge>
            </div>
        @endif
    </a>

    <div class="p-5">
        <!-- Seller Info -->
        <div class="flex items-center gap-3 mb-3">
            <a href="{{ route('sellers.show', $service->seller) }}" class="shrink-0">
                <img src="{{ $service->seller->logo_url }}" alt="{{ $service->seller->store_name }}"
                    class="w-9 h-9 rounded-xl object-cover ring-2 ring-surface-100 dark:ring-surface-700"
                    loading="lazy">
            </a>
            <div class="min-w-0 flex-1">
                <a href="{{ route('sellers.show', $service->seller) }}" class="text-sm font-medium text-surface-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 truncate block">
                    {{ $service->seller->store_name }}
                </a>
                @if($service->seller->level)
                    <span class="text-xs text-surface-500 dark:text-surface-400">{{ ucfirst($service->seller->level) }} Seller</span>
                @endif
            </div>
            @if($service->seller->is_verified)
                <div class="w-5 h-5 bg-primary-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
            @endif
        </div>

        <!-- Title -->
        <h3 class="mb-3 min-h-[3rem]">
            <a href="{{ route('services.show', $service) }}" class="text-surface-900 dark:text-white font-semibold hover:text-primary-600 dark:hover:text-primary-400 line-clamp-2 transition-colors">
                {{ $service->name }}
            </a>
        </h3>

        <!-- Rating -->
        @if($service->rating_count > 0)
            <div class="flex items-center gap-3 mb-4">
                <div class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-warning-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="text-sm font-semibold text-surface-900 dark:text-white">{{ number_format($service->rating_average, 1) }}</span>
                </div>
                <span class="w-1 h-1 rounded-full bg-surface-300 dark:bg-surface-600"></span>
                <span class="text-sm text-surface-600 dark:text-surface-400">({{ $service->rating_count }} reviews)</span>
            </div>
        @else
            <div class="flex items-center gap-2 mb-4">
                <span class="text-sm text-surface-500 dark:text-surface-400">New service</span>
            </div>
        @endif

        <!-- Footer -->
        <div class="pt-4 border-t border-surface-100 dark:border-surface-700 flex items-center justify-between">
            <div class="flex items-center gap-1.5 text-sm text-surface-500 dark:text-surface-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $deliveryDays }} day{{ $deliveryDays != 1 ? 's' : '' }} delivery</span>
            </div>
            <div class="text-right">
                <span class="text-xs text-surface-500 dark:text-surface-400 uppercase tracking-wide">From</span>
                <p class="text-lg font-bold text-surface-900 dark:text-white">${{ number_format($startingPrice, 2) }}</p>
            </div>
        </div>
    </div>
</div>
