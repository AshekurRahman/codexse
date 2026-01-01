@props(['products' => collect(), 'title' => 'Recently Viewed', 'limit' => 8])

@if($products->count() > 0)
<section class="py-12 bg-surface-50 dark:bg-surface-900/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shadow-amber-500/25">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-surface-900 dark:text-white">{{ $title }}</h2>
                    <p class="text-surface-500 dark:text-surface-400">Products you've browsed recently</p>
                </div>
            </div>
            <a href="{{ route('products.index') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 text-primary-600 dark:text-primary-400 font-medium hover:bg-primary-50 dark:hover:bg-primary-900/30 rounded-xl transition-colors">
                View All Products
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <!-- Products Carousel -->
        <div class="relative" x-data="{ scrollContainer: null }" x-init="scrollContainer = $refs.container">
            <!-- Scroll Buttons -->
            <button
                @click="scrollContainer.scrollBy({ left: -300, behavior: 'smooth' })"
                class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 z-10 w-12 h-12 bg-white dark:bg-surface-800 rounded-full shadow-lg flex items-center justify-center text-surface-600 dark:text-surface-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors hidden md:flex"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            <button
                @click="scrollContainer.scrollBy({ left: 300, behavior: 'smooth' })"
                class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 z-10 w-12 h-12 bg-white dark:bg-surface-800 rounded-full shadow-lg flex items-center justify-center text-surface-600 dark:text-surface-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors hidden md:flex"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

            <!-- Products Container -->
            <div
                x-ref="container"
                class="flex gap-6 overflow-x-auto pb-4 scrollbar-hide scroll-smooth snap-x snap-mandatory"
                style="scrollbar-width: none; -ms-overflow-style: none;"
            >
                @foreach($products->take($limit) as $product)
                <div class="flex-shrink-0 w-64 snap-start">
                    <a href="{{ route('products.show', $product) }}" class="group block">
                        <div class="bg-white dark:bg-surface-800 rounded-2xl overflow-hidden border border-surface-200 dark:border-surface-700 hover:shadow-xl hover:border-primary-200 dark:hover:border-primary-800 transition-all duration-300">
                            <!-- Image -->
                            <div class="aspect-[4/3] overflow-hidden bg-surface-100 dark:bg-surface-700 relative">
                                @if($product->thumbnail)
                                    <img
                                        src="{{ $product->thumbnail_url }}"
                                        alt="{{ $product->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-10 h-10 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif

                                @if($product->sale_price)
                                    <div class="absolute top-2 left-2 px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-lg">
                                        {{ round((1 - $product->sale_price / $product->price) * 100) }}% OFF
                                    </div>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="p-4">
                                @if($product->category)
                                    <p class="text-xs font-semibold text-primary-600 dark:text-primary-400 uppercase tracking-wide mb-1">{{ $product->category->name }}</p>
                                @endif

                                <h3 class="font-semibold text-surface-900 dark:text-white line-clamp-2 mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                    {{ $product->name }}
                                </h3>

                                <div class="flex items-center justify-between">
                                    <div>
                                        @if($product->sale_price)
                                            <span class="text-lg font-bold text-surface-900 dark:text-white">{{ format_price($product->sale_price) }}</span>
                                            <span class="text-sm text-surface-400 line-through ml-1">{{ format_price($product->price) }}</span>
                                        @else
                                            <span class="text-lg font-bold text-surface-900 dark:text-white">{{ format_price($product->price) }}</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-1 text-sm">
                                        <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span class="text-surface-600 dark:text-surface-400">{{ number_format($product->average_rating, 1) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Mobile View All Link -->
        <div class="mt-6 text-center sm:hidden">
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition-colors">
                View All Products
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</section>
@endif
