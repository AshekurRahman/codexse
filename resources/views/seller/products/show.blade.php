<x-layouts.app title="{{ $product->name }}">
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('seller.products.index') }}" class="inline-flex items-center gap-2 text-surface-600 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Products
                </a>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">{{ $product->name }}</h1>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('seller.products.edit', $product) }}" class="inline-flex items-center gap-2 px-4 py-2 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                        <a href="{{ route('products.show', $product) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            View Live
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Thumbnail -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                        <div class="aspect-[16/9] bg-surface-100 dark:bg-surface-700">
                            @if($product->thumbnail)
                                <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-surface-300 dark:text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Description</h2>
                        <div class="prose prose-surface dark:prose-invert max-w-none">
                            {!! $product->description !!}
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Status -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="text-sm font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider mb-4">Status</h3>
                        @if($product->status === 'published')
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400 rounded-lg">
                                <span class="w-2 h-2 bg-success-500 rounded-full"></span>
                                Published
                            </span>
                        @elseif($product->status === 'pending')
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400 rounded-lg">
                                <span class="w-2 h-2 bg-warning-500 rounded-full"></span>
                                Pending Review
                            </span>
                        @else
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-400 rounded-lg">
                                <span class="w-2 h-2 bg-surface-400 rounded-full"></span>
                                {{ ucfirst($product->status) }}
                            </span>
                        @endif
                    </div>

                    <!-- Pricing -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="text-sm font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider mb-4">Pricing</h3>
                        @if($product->sale_price)
                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl font-bold text-surface-900 dark:text-white">{{ format_price($product->sale_price) }}</span>
                                <span class="text-lg text-surface-400 line-through">{{ format_price($product->price) }}</span>
                            </div>
                            <p class="text-sm text-success-600 dark:text-success-400 mt-1">{{ round((1 - $product->sale_price / $product->price) * 100) }}% off</p>
                        @else
                            <span class="text-2xl font-bold text-surface-900 dark:text-white">{{ format_price($product->price) }}</span>
                        @endif
                    </div>

                    <!-- Stats -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="text-sm font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider mb-4">Statistics</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Sales</span>
                                <span class="font-semibold text-surface-900 dark:text-white">{{ number_format($product->downloads_count ?? 0) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Rating</span>
                                <span class="font-semibold text-surface-900 dark:text-white">{{ number_format($product->average_rating ?? 0, 1) }} / 5.0</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Category</span>
                                <span class="font-semibold text-surface-900 dark:text-white">{{ $product->category->name ?? 'Uncategorized' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Created</span>
                                <span class="font-semibold text-surface-900 dark:text-white">{{ $product->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
