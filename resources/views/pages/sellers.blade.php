<x-layouts.app title="Sellers - Codexse">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Sellers</h1>
                <p class="mt-1 text-surface-600 dark:text-surface-400">Discover talented creators and their products</p>
            </div>

            <!-- Sellers Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($sellers as $seller)
                    <a href="{{ route('sellers.show', $seller) }}" class="group block rounded-2xl bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 p-6 hover:border-primary-500 hover:shadow-xl transition-all">
                        <!-- Avatar -->
                        <div class="flex justify-center mb-4">
                            <div class="relative">
                                <div class="h-20 w-20 rounded-full bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white shadow-lg">
                                    @if($seller->logo)
                                        <img src="{{ $seller->logo_url }}" alt="{{ $seller->store_name }}" class="h-20 w-20 rounded-full object-cover">
                                    @else
                                        <span class="text-2xl font-bold">{{ substr($seller->store_name, 0, 1) }}</span>
                                    @endif
                                </div>
                                @if($seller->is_verified)
                                    <div class="absolute bottom-0 right-0 h-6 w-6 rounded-full bg-primary-500 border-2 border-white dark:border-surface-800 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Name -->
                        <h3 class="text-center text-lg font-semibold text-surface-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ $seller->store_name }}</h3>

                        <!-- Badges -->
                        @if($seller->is_featured || $seller->is_verified)
                            <div class="flex justify-center gap-2 mt-2">
                                @if($seller->is_featured)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-xs font-medium">Featured</span>
                                @endif
                                @if($seller->is_verified)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 text-xs font-medium">Verified</span>
                                @endif
                            </div>
                        @endif

                        <!-- Description -->
                        <p class="text-center text-sm text-surface-500 dark:text-surface-400 mt-3 line-clamp-2">{{ $seller->description ?? 'Digital products creator' }}</p>

                        <!-- Stats -->
                        <div class="flex justify-center gap-6 mt-4 pt-4 border-t border-surface-100 dark:border-surface-700">
                            <div class="text-center">
                                <div class="text-lg font-bold text-surface-900 dark:text-white">{{ $seller->products_count }}</div>
                                <div class="text-xs text-surface-500 dark:text-surface-400">Products</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-surface-900 dark:text-white">${{ number_format($seller->total_sales, 0) }}</div>
                                <div class="text-xs text-surface-500 dark:text-surface-400">Sales</div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-16">
                        <div class="w-16 h-16 rounded-full bg-surface-100 dark:bg-surface-800 flex items-center justify-center mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-1">No sellers yet</h3>
                        <p class="text-surface-500 dark:text-surface-400">Sellers will appear here once they are approved.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($sellers->hasPages())
                <div class="mt-8">
                    {{ $sellers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
