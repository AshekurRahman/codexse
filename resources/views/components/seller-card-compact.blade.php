@props(['seller'])

@php
    $isFeatured = $seller->is_featured ?? false;
    $isVerified = $seller->is_verified ?? false;
    $level = $seller->level ?? null;
    $rating = $seller->average_rating ?? 5.0;
@endphp

<a href="{{ route('sellers.show', $seller) }}"
   class="group relative flex flex-col items-center p-5 rounded-2xl bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 hover:border-primary-300 dark:hover:border-primary-600 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">

    {{-- Featured Badge --}}
    @if($isFeatured)
        <div class="absolute top-3 right-3">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-gradient-to-r from-amber-400 to-orange-400 text-white shadow-sm">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                Top
            </span>
        </div>
    @endif

    {{-- Avatar --}}
    <div class="relative mb-3">
        <div class="w-16 h-16 rounded-full overflow-hidden ring-2 ring-surface-100 dark:ring-surface-700 group-hover:ring-primary-200 dark:group-hover:ring-primary-700 transition-all duration-300 {{ $isFeatured ? 'ring-amber-200 dark:ring-amber-700' : '' }}">
            <img src="{{ $seller->logo_url }}"
                 alt="{{ $seller->store_name }}"
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($seller->store_name) }}&background=6366f1&color=fff&size=128';">
        </div>
        @if($isVerified)
            <div class="absolute -bottom-0.5 -right-0.5 w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center ring-2 ring-white dark:ring-surface-800">
                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </div>
        @endif
    </div>

    {{-- Store Name --}}
    <h3 class="font-semibold text-surface-900 dark:text-white text-sm text-center truncate w-full group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
        {{ $seller->store_name }}
    </h3>

    {{-- Level Badge --}}
    @if($level)
        <span class="mt-1 text-[10px] font-medium uppercase tracking-wide text-surface-500 dark:text-surface-400">
            {{ ucfirst($level) }} Seller
        </span>
    @endif

    {{-- Rating --}}
    <div class="flex items-center gap-1 mt-2">
        <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
        </svg>
        <span class="text-xs font-medium text-surface-700 dark:text-surface-300">{{ number_format($rating, 1) }}</span>
    </div>

    {{-- Stats --}}
    <div class="flex items-center justify-center gap-3 mt-3 pt-3 border-t border-surface-100 dark:border-surface-700 w-full">
        <div class="text-center">
            <div class="text-sm font-bold text-surface-900 dark:text-white">{{ $seller->products_count ?? 0 }}</div>
            <div class="text-[10px] text-surface-500 dark:text-surface-400 uppercase tracking-wide">Products</div>
        </div>
        <div class="w-px h-8 bg-surface-200 dark:bg-surface-700"></div>
        <div class="text-center">
            <div class="text-sm font-bold text-surface-900 dark:text-white">{{ number_format($seller->total_sales ?? 0) }}</div>
            <div class="text-[10px] text-surface-500 dark:text-surface-400 uppercase tracking-wide">Sales</div>
        </div>
    </div>
</a>
