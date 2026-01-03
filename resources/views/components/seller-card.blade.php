@props(['seller'])

<a href="{{ route('sellers.show', $seller) }}"
   class="group block bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 overflow-hidden hover:shadow-xl hover:shadow-primary-500/5 dark:hover:shadow-primary-500/10 hover:border-primary-200 dark:hover:border-primary-800 transition-all duration-300">

    <!-- Banner Header -->
    <div class="relative h-20 bg-gradient-to-br from-primary-500 via-indigo-500 to-purple-600 overflow-hidden">
        <!-- Pattern Overlay -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 16px 16px;"></div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute -top-4 -right-4 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
        <div class="absolute -bottom-6 -left-6 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>

        <!-- Featured Badge -->
        @if($seller->is_featured)
            <div class="absolute top-3 right-3">
                <x-badge type="featured" icon="star" size="xs">Featured</x-badge>
            </div>
        @endif

        <!-- Level Badge -->
        @if($seller->level)
            <div class="absolute top-3 left-3">
                <x-badge type="pro" size="xs">{{ ucfirst($seller->level) }}</x-badge>
            </div>
        @endif
    </div>

    <!-- Avatar (Overlapping) -->
    <div class="relative px-5 -mt-8">
        <div class="relative inline-block">
            <div class="w-16 h-16 rounded-2xl overflow-hidden bg-white dark:bg-surface-700 ring-4 ring-white dark:ring-surface-800 shadow-lg">
                <img src="{{ $seller->logo_url }}"
                     alt="{{ $seller->store_name }}"
                     class="w-full h-full object-cover"
                     loading="lazy"
                     onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br from-primary-500 to-indigo-600 flex items-center justify-center\'><span class=\'text-xl font-bold text-white\'>{{ strtoupper(substr($seller->store_name, 0, 2)) }}</span></div>';">
            </div>

            <!-- Verified Badge -->
            @if($seller->is_verified)
                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-primary-600 rounded-lg flex items-center justify-center ring-2 ring-white dark:ring-surface-800">
                    <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
            @endif
        </div>
    </div>

    <!-- Content -->
    <div class="px-5 pt-3 pb-5">
        <!-- Name & Description -->
        <div class="mb-4">
            <h3 class="text-lg font-bold text-surface-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors truncate">
                {{ $seller->store_name }}
            </h3>
            <p class="text-sm text-surface-500 dark:text-surface-400 mt-1 line-clamp-2 min-h-[2.5rem]">
                {{ $seller->description ?? 'Creating premium digital products and services' }}
            </p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-4 gap-1 py-3 border-y border-surface-100 dark:border-surface-700">
            <div class="text-center">
                <div class="flex items-center justify-center gap-1">
                    <svg class="w-3.5 h-3.5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span class="text-sm font-bold text-surface-900 dark:text-white">{{ $seller->products_count ?? 0 }}</span>
                </div>
                <div class="text-xs text-surface-500 dark:text-surface-400">Products</div>
            </div>
            <div class="text-center border-l border-surface-100 dark:border-surface-700">
                <div class="flex items-center justify-center gap-1">
                    <svg class="w-3.5 h-3.5 text-success-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm font-bold text-surface-900 dark:text-white">{{ $seller->services_count ?? 0 }}</span>
                </div>
                <div class="text-xs text-surface-500 dark:text-surface-400">Services</div>
            </div>
            <div class="text-center border-l border-surface-100 dark:border-surface-700">
                <div class="text-sm font-bold text-surface-900 dark:text-white">{{ number_format($seller->total_sales ?? 0) }}</div>
                <div class="text-xs text-surface-500 dark:text-surface-400">Sales</div>
            </div>
            <div class="text-center border-l border-surface-100 dark:border-surface-700">
                <div class="flex items-center justify-center gap-1">
                    <svg class="w-3.5 h-3.5 text-warning-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="text-sm font-bold text-surface-900 dark:text-white">4.9</span>
                </div>
                <div class="text-xs text-surface-500 dark:text-surface-400">Rating</div>
            </div>
        </div>

        <!-- Action Row -->
        <div class="flex items-center justify-between mt-4">
            <div class="flex items-center gap-2"></div>

            <span class="inline-flex items-center gap-1 text-sm font-medium text-primary-600 dark:text-primary-400 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                View Profile
                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </span>
        </div>
    </div>
</a>
