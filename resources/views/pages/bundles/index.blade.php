<x-layouts.app title="Product Bundles - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Product Bundles</h1>
                <p class="mt-1 text-surface-600 dark:text-surface-400">Save money with our curated product bundles</p>
            </div>

            @if($bundles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($bundles as $bundle)
                        <a href="{{ route('bundles.show', $bundle) }}" class="group rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden hover:shadow-lg transition-shadow">
                            @if($bundle->thumbnail)
                                <div class="aspect-video overflow-hidden">
                                    <img src="{{ asset('storage/' . $bundle->thumbnail) }}" alt="{{ $bundle->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>
                            @else
                                <div class="aspect-video bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                            @endif
                            <div class="p-6">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="inline-flex items-center rounded-full bg-success-100 dark:bg-success-900/30 px-2 py-0.5 text-xs font-medium text-success-700 dark:text-success-400">
                                        Save {{ $bundle->savings_percent }}%
                                    </span>
                                    <span class="text-sm text-surface-500 dark:text-surface-400">{{ $bundle->products->count() }} products</span>
                                </div>
                                <h3 class="font-semibold text-surface-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ $bundle->name }}</h3>
                                <p class="mt-2 text-sm text-surface-600 dark:text-surface-400 line-clamp-2">{!! strip_tags($bundle->description) !!}</p>
                                <div class="mt-4 flex items-center gap-2">
                                    <span class="text-lg font-bold text-surface-900 dark:text-white">${{ number_format($bundle->price, 2) }}</span>
                                    <span class="text-sm text-surface-500 dark:text-surface-400 line-through">${{ number_format($bundle->original_price, 2) }}</span>
                                </div>
                                <div class="mt-4 flex items-center gap-2">
                                    <img src="{{ $bundle->seller->logo_url }}" alt="{{ $bundle->seller->store_name }}" class="h-6 w-6 rounded-full object-cover">
                                    <span class="text-sm text-surface-600 dark:text-surface-400">{{ $bundle->seller->store_name }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="mt-8">
                    {{ $bundles->links() }}
                </div>
            @else
                <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <p class="text-surface-600 dark:text-surface-400 mb-4">No bundles available at the moment</p>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                        Browse Products
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
