<x-layouts.app title="{{ $bundle->name }} - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <a href="{{ route('bundles.index') }}" class="inline-flex items-center text-sm text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Bundles
            </a>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    @if($bundle->thumbnail)
                        <div class="rounded-xl overflow-hidden">
                            <img src="{{ asset('storage/' . $bundle->thumbnail) }}" alt="{{ $bundle->name }}" class="w-full aspect-video object-cover">
                        </div>
                    @endif

                    <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">{{ $bundle->name }}</h1>
                        <div class="mt-4 prose dark:prose-invert max-w-none">
                            {!! $bundle->description !!}
                        </div>
                    </div>

                    <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                        <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700">
                            <h2 class="font-semibold text-surface-900 dark:text-white">Included Products ({{ $bundle->products->count() }})</h2>
                        </div>
                        <div class="divide-y divide-surface-200 dark:divide-surface-700">
                            @foreach($bundle->products as $product)
                                <a href="{{ route('products.show', $product) }}" class="flex items-center gap-4 px-6 py-4 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                    <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="h-16 w-16 rounded-lg object-cover">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-surface-900 dark:text-white">{{ $product->name }}</h3>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">{{ $product->category->name }}</p>
                                    </div>
                                    <span class="text-sm font-medium text-surface-600 dark:text-surface-400">${{ number_format($product->price, 2) }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="sticky top-24 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6 space-y-6">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-full bg-success-100 dark:bg-success-900/30 px-3 py-1 text-sm font-medium text-success-700 dark:text-success-400">
                                Save ${{ number_format($bundle->savings, 2) }} ({{ $bundle->savings_percent }}%)
                            </span>
                        </div>

                        <div>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-bold text-surface-900 dark:text-white">${{ number_format($bundle->price, 2) }}</span>
                                <span class="text-lg text-surface-500 dark:text-surface-400 line-through">${{ number_format($bundle->original_price, 2) }}</span>
                            </div>
                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">{{ $bundle->products->count() }} products included</p>
                        </div>

                        <button type="button" class="w-full rounded-lg bg-primary-600 px-6 py-3 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                            Add Bundle to Cart
                        </button>

                        <div class="flex items-center gap-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                            <img src="{{ $bundle->seller->user->avatar_url }}" alt="{{ $bundle->seller->store_name }}" class="h-12 w-12 rounded-full object-cover">
                            <div>
                                <a href="{{ route('sellers.show', $bundle->seller) }}" class="font-medium text-surface-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">{{ $bundle->seller->store_name }}</a>
                                <p class="text-sm text-surface-500 dark:text-surface-400">Seller</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
