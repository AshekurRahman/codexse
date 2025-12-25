@props(['product' => null])

<article
    x-data="{
        inWishlist: {{ $product && auth()->check() && auth()->user()->wishlists()->where('product_id', $product->id)->exists() ? 'true' : 'false' }},
        inCart: false,
        loading: false,
        wishlistLoading: false,
        message: '',
        showMessage: false,

        async addToCart() {
            if (this.loading) return;
            this.loading = true;

            try {
                const response = await fetch('{{ route('cart.add', $product ?? 0) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                this.inCart = true;
                this.showNotification(data.message || 'Added to cart!');

                // Update cart count in navbar
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
            } catch (error) {
                this.showNotification('Error adding to cart', true);
            } finally {
                this.loading = false;
            }
        },

        async toggleWishlist() {
            @guest
                window.location.href = '{{ route('login') }}';
                return;
            @endguest

            if (this.wishlistLoading) return;
            this.wishlistLoading = true;

            try {
                const response = await fetch('{{ route('wishlist.toggle', $product ?? 0) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                this.inWishlist = data.in_wishlist;
                this.showNotification(data.message);
            } catch (error) {
                this.showNotification('Error updating wishlist', true);
            } finally {
                this.wishlistLoading = false;
            }
        },

        showNotification(msg, isError = false) {
            this.message = msg;
            this.showMessage = true;
            setTimeout(() => this.showMessage = false, 2000);
        }
    }"
    class="group relative rounded-xl bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 overflow-hidden hover:shadow-lg transition-shadow duration-300"
>
    <!-- Notification Toast -->
    <div
        x-show="showMessage"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="absolute top-2 left-2 right-2 z-20 bg-surface-900 dark:bg-white text-white dark:text-surface-900 text-xs font-medium px-3 py-2 rounded-lg text-center shadow-lg"
        x-text="message"
        x-cloak
    ></div>

    <!-- Image -->
    <a href="{{ $product ? route('products.show', $product) : '#' }}" class="relative block aspect-[4/3] bg-surface-100 dark:bg-surface-700 overflow-hidden">
        @if($product && $product->thumbnail)
            <img
                src="{{ $product->thumbnail_url }}"
                alt="{{ $product->name }}"
                class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
            >
        @else
            <div class="h-full flex items-center justify-center">
                <svg class="w-12 h-12 text-surface-300 dark:text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        @endif

        <!-- Sale Badge -->
        @if($product && $product->sale_price)
            <div class="absolute top-4 left-4">
                <span class="bg-danger-500 text-white text-xs font-semibold px-2 py-1 rounded-md">
                    {{ round((1 - $product->sale_price / $product->price) * 100) }}% OFF
                </span>
            </div>
        @endif

        <!-- Wishlist Button -->
        <div class="absolute top-4 right-[1rem] opacity-0 group-hover:opacity-100 transition-opacity">
            <button
                @click.prevent="toggleWishlist()"
                :disabled="wishlistLoading"
                class="w-8 h-8 bg-white dark:bg-surface-800 rounded-full flex items-center justify-center shadow hover:bg-danger-50 dark:hover:bg-danger-900/30 transition-colors"
                :class="inWishlist ? 'text-danger-500' : 'text-surface-600 dark:text-surface-400 hover:text-danger-500'"
            >
                <svg x-show="!wishlistLoading" class="w-4 h-4" :fill="inWishlist ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <svg x-show="wishlistLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" x-cloak>
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </div>
    </a>

    <!-- Content -->
    <div class="p-4">
        <!-- Category -->
        @if($product && $product->category)
            <p class="text-xs font-medium text-primary-600 dark:text-primary-400 mb-1">{{ $product->category->name }}</p>
        @endif

        <!-- Title -->
        <h3 class="font-semibold text-surface-900 dark:text-white line-clamp-1 mb-2">
            <a href="{{ $product ? route('products.show', $product) : '#' }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                {{ $product?->name ?? 'Product Title' }}
            </a>
        </h3>

        <!-- Rating -->
        <div class="flex items-center gap-1 mb-3">
            <svg class="w-4 h-4 text-warning-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
            <span class="text-sm text-surface-600 dark:text-surface-400">{{ $product ? number_format($product->average_rating, 1) : '4.8' }}</span>
            <span class="text-surface-300 dark:text-surface-600">·</span>
            <span class="text-sm text-surface-500 dark:text-surface-400">{{ $product ? number_format($product->downloads_count) : '1.2k' }} sales</span>
        </div>

        <!-- Price & Add to Cart -->
        <div class="flex items-center justify-between">
            <div>
                @if($product && $product->sale_price)
                    <span class="text-lg font-bold text-surface-900 dark:text-white">${{ number_format($product->sale_price, 2) }}</span>
                    <span class="text-sm text-surface-400 line-through ml-1">${{ number_format($product->price, 2) }}</span>
                @else
                    <span class="text-lg font-bold text-surface-900 dark:text-white">${{ $product ? number_format($product->price, 2) : '49.00' }}</span>
                @endif
            </div>

            <button
                @click="addToCart()"
                :disabled="loading || inCart"
                class="text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors flex items-center gap-2"
                :class="inCart ? 'bg-success-500' : 'bg-primary-600 hover:bg-primary-700'"
            >
                <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" x-cloak>
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg x-show="inCart && !loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span x-text="inCart ? 'Added' : 'Add to Cart'"></span>
            </button>
        </div>
    </div>
</article>
