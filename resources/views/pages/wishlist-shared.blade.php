<x-layouts.app :title="$owner->name . '\'s Wishlist - ' . config('app.name')">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-pink-50 via-white to-primary-50 dark:from-surface-900 dark:via-surface-900 dark:to-surface-800">
        <div class="absolute top-0 left-0 w-96 h-96 bg-pink-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 dark:opacity-10 -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-primary-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 dark:opacity-10 translate-x-1/2 -translate-y-1/2"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center gap-5">
                    <!-- Owner Avatar -->
                    <div class="flex-shrink-0">
                        <img src="{{ $owner->avatar_url }}" alt="{{ $owner->name }}" class="w-20 h-20 rounded-2xl object-cover shadow-lg ring-4 ring-white dark:ring-surface-700">
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-pink-100 dark:bg-pink-900/50 text-pink-700 dark:text-pink-300 rounded-full text-sm font-medium">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                                Public Wishlist
                            </span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-bold text-surface-900 dark:text-white">
                            {{ $owner->name }}'s Wishlist
                        </h1>
                        <p class="mt-2 text-surface-600 dark:text-surface-400">
                            {{ $wishlists->total() }} {{ Str::plural('item', $wishlists->total()) }} saved
                        </p>
                    </div>
                </div>

                <!-- Share Actions -->
                <div class="flex items-center gap-3">
                    <button onclick="copyShareLink()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-surface-700 dark:text-surface-300 font-medium rounded-xl hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Copy Link
                    </button>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition-colors shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Browse Products
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Wishlist Items -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if($wishlists->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($wishlists as $item)
                    <div class="group bg-white dark:bg-surface-800 rounded-2xl overflow-hidden shadow-lg shadow-surface-200/50 dark:shadow-none border border-surface-100 dark:border-surface-700 hover:shadow-xl transition-all duration-300">
                        <a href="{{ route('products.show', $item->product) }}" class="block">
                            <!-- Product Image -->
                            <div class="aspect-[4/3] overflow-hidden bg-surface-100 dark:bg-surface-700 relative">
                                @if($item->product->thumbnail)
                                    <img src="{{ asset('storage/' . $item->product->thumbnail) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Category Badge -->
                                @if($item->product->category)
                                    <div class="absolute top-3 left-3">
                                        <span class="px-2.5 py-1 bg-white/90 dark:bg-surface-800/90 backdrop-blur-sm text-xs font-medium text-surface-700 dark:text-surface-300 rounded-full">
                                            {{ $item->product->category->name }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </a>

                        <!-- Product Info -->
                        <div class="p-5">
                            <a href="{{ route('products.show', $item->product) }}" class="block">
                                <h3 class="font-semibold text-surface-900 dark:text-white line-clamp-2 mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                    {{ $item->product->name }}
                                </h3>
                            </a>

                            <!-- Seller -->
                            @if($item->product->seller)
                                <a href="{{ route('sellers.show', $item->product->seller) }}" class="flex items-center gap-2 mb-3 text-sm text-surface-500 dark:text-surface-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                    <img src="{{ $item->product->seller->logo_url }}" alt="{{ $item->product->seller->store_name }}" class="w-5 h-5 rounded-full object-cover">
                                    <span>{{ $item->product->seller->store_name }}</span>
                                </a>
                            @endif

                            <!-- Price -->
                            <div class="flex items-center justify-between">
                                <div class="font-bold text-lg text-surface-900 dark:text-white">
                                    {{ format_price($item->product->price) }}
                                </div>
                                @if($item->product->reviews_count > 0)
                                    <div class="flex items-center gap-1 text-sm">
                                        <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span class="text-surface-600 dark:text-surface-400">{{ number_format($item->product->reviews_avg_rating, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $wishlists->links() }}
            </div>
        @else
            <div class="text-center py-20 bg-surface-50 dark:bg-surface-800/50 rounded-3xl">
                <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-surface-100 dark:bg-surface-700 flex items-center justify-center">
                    <svg class="w-10 h-10 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-surface-900 dark:text-white mb-2">This wishlist is empty</h3>
                <p class="text-surface-500 dark:text-surface-400 mb-6">No items have been added yet.</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Browse Products
                </a>
            </div>
        @endif
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-4 right-4 transform translate-y-full opacity-0 transition-all duration-300 z-50">
        <div class="flex items-center gap-3 px-4 py-3 bg-surface-900 dark:bg-surface-700 text-white rounded-xl shadow-lg">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span id="toast-message">Link copied to clipboard!</span>
        </div>
    </div>

    @push('scripts')
    <script>
        function copyShareLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                showToast('Link copied to clipboard!');
            }).catch(() => {
                // Fallback for older browsers
                const input = document.createElement('input');
                input.value = url;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                showToast('Link copied to clipboard!');
            });
        }

        function showToast(message) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            toastMessage.textContent = message;
            toast.classList.remove('translate-y-full', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('translate-y-full', 'opacity-0');
            }, 3000);
        }
    </script>
    @endpush
</x-layouts.app>
