<x-layouts.app title="Categories - Codexse">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Categories</h1>
                <p class="mt-1 text-surface-600 dark:text-surface-400">Browse premium digital assets by category</p>
            </div>

            <!-- Categories Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($categories as $category)
                    <a href="{{ route('categories.show', $category) }}" class="group block rounded-2xl bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 overflow-hidden hover:border-primary-500 hover:shadow-xl transition-all">
                        <!-- Category Image/Gradient Header -->
                        <div class="h-32 bg-gradient-to-br from-primary-500 to-accent-500 relative">
                            @if($category->image)
                                <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                            @endif
                            <!-- Icon Overlay -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="h-16 w-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white">
                                    <x-category-icon :icon="$category->icon" class="h-8 w-8" />
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <h3 class="text-lg font-semibold text-surface-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ $category->name }}</h3>
                            <p class="text-sm text-surface-500 dark:text-surface-400 mt-1 line-clamp-2">{{ $category->description ?? 'Premium ' . strtolower($category->name) . ' for your projects' }}</p>

                            <!-- Footer -->
                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-surface-100 dark:border-surface-700">
                                <span class="text-sm font-medium text-surface-600 dark:text-surface-300">{{ $category->products_count }} products</span>
                                <span class="text-primary-600 dark:text-primary-400 group-hover:translate-x-1 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-16">
                        <div class="w-16 h-16 rounded-full bg-surface-100 dark:bg-surface-800 flex items-center justify-center mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-1">No categories yet</h3>
                        <p class="text-surface-500 dark:text-surface-400">Categories will appear here once products are added.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
