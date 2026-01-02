@props(['seller'])

<a href="{{ route('sellers.show', $seller) }}" class="seller-card-compact group">
    <!-- Avatar -->
    <div class="relative mx-auto mb-3">
        <img src="{{ $seller->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($seller->store_name) . '&background=6366f1&color=fff' }}"
             alt="{{ $seller->store_name }}"
             class="seller-avatar object-cover">
        @if($seller->is_verified)
            <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-primary-500 rounded-full flex items-center justify-center ring-2 ring-white dark:ring-surface-800">
                <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </div>
        @endif
    </div>

    <!-- Store Name -->
    <h3 class="font-semibold text-surface-900 dark:text-white mb-1 truncate group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
        {{ $seller->store_name }}
    </h3>

    <!-- Rating -->
    <div class="flex items-center justify-center gap-1 mb-2">
        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
        </svg>
        <span class="text-sm font-medium text-surface-700 dark:text-surface-300">
            {{ number_format($seller->average_rating ?? 5.0, 1) }}
        </span>
    </div>

    <!-- Stats -->
    <div class="flex items-center justify-center gap-4 text-xs text-surface-500 dark:text-surface-400">
        <span class="flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            {{ $seller->products_count ?? 0 }} items
        </span>
        <span class="flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            {{ number_format($seller->total_sales ?? 0) }} sold
        </span>
    </div>

    <!-- Level Badge -->
    @if($seller->level)
        <div class="mt-3">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                @if($seller->level === 'pro') bg-gradient-to-r from-primary-500 to-accent-500 text-white
                @elseif($seller->level === 'expert') bg-gradient-to-r from-amber-500 to-orange-500 text-white
                @else bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-300
                @endif">
                {{ ucfirst($seller->level) }} Seller
            </span>
        </div>
    @endif
</a>
