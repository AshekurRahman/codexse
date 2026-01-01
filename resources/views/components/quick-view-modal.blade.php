<!-- Quick View Modal -->
<div
    x-data="quickViewModal()"
    x-show="isOpen"
    x-on:open-quick-view.window="open($event.detail)"
    x-on:keydown.escape.window="close()"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="quick-view-title"
    role="dialog"
    aria-modal="true"
>
    <!-- Backdrop -->
    <div
        x-show="isOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="close()"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm"
    ></div>

    <!-- Modal Panel -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div
            x-show="isOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.stop
            class="relative w-full max-w-4xl bg-white dark:bg-surface-800 rounded-2xl shadow-2xl overflow-hidden"
        >
            <!-- Close Button -->
            <button
                @click="close()"
                class="absolute top-4 right-4 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-surface-100 dark:bg-surface-700 text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 hover:bg-surface-200 dark:hover:bg-surface-600 transition-colors"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Loading State -->
            <div x-show="loading" class="flex items-center justify-center h-96">
                <svg class="w-12 h-12 text-primary-500 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <!-- Content -->
            <div x-show="!loading && product" class="grid md:grid-cols-2 gap-0">
                <!-- Image Gallery -->
                <div class="relative bg-surface-100 dark:bg-surface-900">
                    <!-- Main Image -->
                    <div class="aspect-square">
                        <img
                            :src="currentImage || product?.thumbnail || '{{ asset('images/placeholder-product.svg') }}'"
                            :alt="product?.name"
                            class="w-full h-full object-cover"
                            onerror="this.src='{{ asset('images/placeholder-product.svg') }}'"
                        >
                    </div>

                    <!-- Sale Badge -->
                    <div x-show="product?.sale_price" class="absolute top-4 left-4">
                        <span class="bg-danger-500 text-white text-sm font-semibold px-3 py-1 rounded-lg">
                            <span x-text="Math.round((1 - product?.sale_price / product?.price) * 100)"></span>% OFF
                        </span>
                    </div>

                    <!-- Thumbnail Gallery -->
                    <div x-show="product?.gallery?.length > 0" class="absolute bottom-4 left-4 right-4">
                        <div class="flex gap-2 overflow-x-auto pb-2">
                            <button
                                @click="currentImage = product?.thumbnail"
                                :class="currentImage === product?.thumbnail || !currentImage ? 'ring-2 ring-primary-500' : 'ring-1 ring-surface-300 dark:ring-surface-600'"
                                class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden bg-white"
                            >
                                <img :src="product?.thumbnail || '{{ asset('images/placeholder-product.svg') }}'" class="w-full h-full object-cover" onerror="this.src='{{ asset('images/placeholder-product.svg') }}'">
                            </button>
                            <template x-for="(image, index) in product?.gallery" :key="index">
                                <button
                                    @click="currentImage = image"
                                    :class="currentImage === image ? 'ring-2 ring-primary-500' : 'ring-1 ring-surface-300 dark:ring-surface-600'"
                                    class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden bg-white"
                                >
                                    <img :src="image || '{{ asset('images/placeholder-product.svg') }}'" class="w-full h-full object-cover" onerror="this.src='{{ asset('images/placeholder-product.svg') }}'">
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="p-6 md:p-8 flex flex-col">
                    <!-- Category -->
                    <p x-show="product?.category" class="text-sm font-medium text-primary-600 dark:text-primary-400 mb-2" x-text="product?.category"></p>

                    <!-- Title -->
                    <h2 id="quick-view-title" class="text-2xl font-bold text-surface-900 dark:text-white mb-3" x-text="product?.name"></h2>

                    <!-- Rating & Sales -->
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex items-center gap-1">
                            <template x-for="i in 5" :key="i">
                                <svg
                                    class="w-5 h-5"
                                    :class="i <= Math.round(product?.rating || 0) ? 'text-warning-400' : 'text-surface-300 dark:text-surface-600'"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </template>
                            <span class="text-sm text-surface-600 dark:text-surface-400 ml-1" x-text="product?.rating?.toFixed(1) || '0.0'"></span>
                        </div>
                        <span class="text-surface-300 dark:text-surface-600">|</span>
                        <span class="text-sm text-surface-600 dark:text-surface-400">
                            <span x-text="product?.sales?.toLocaleString() || '0'"></span> sales
                        </span>
                        <span class="text-surface-300 dark:text-surface-600">|</span>
                        <span class="text-sm text-surface-600 dark:text-surface-400">
                            <span x-text="product?.reviews_count || '0'"></span> reviews
                        </span>
                    </div>

                    <!-- Price -->
                    <div class="flex items-baseline gap-3 mb-6">
                        <span class="text-3xl font-bold text-surface-900 dark:text-white" x-text="formatPrice(product?.sale_price || product?.price)"></span>
                        <span x-show="product?.sale_price" class="text-lg text-surface-400 line-through" x-text="formatPrice(product?.price)"></span>
                    </div>

                    <!-- Description -->
                    <div class="flex-1 mb-6">
                        <p class="text-surface-600 dark:text-surface-400 line-clamp-4" x-text="product?.short_description || product?.description"></p>
                    </div>

                    <!-- Features/Tags -->
                    <div x-show="product?.tags?.length > 0" class="flex flex-wrap gap-2 mb-6">
                        <template x-for="tag in product?.tags?.slice(0, 5)" :key="tag">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-400" x-text="tag"></span>
                        </template>
                    </div>

                    <!-- Seller Info -->
                    <div x-show="product?.seller" class="flex items-center gap-3 mb-6 p-3 bg-surface-50 dark:bg-surface-700/50 rounded-xl">
                        <img
                            :src="product?.seller?.avatar || '{{ asset('images/default-avatar.svg') }}'"
                            :alt="product?.seller?.name"
                            class="w-10 h-10 rounded-full object-cover"
                            onerror="this.src='{{ asset('images/default-avatar.svg') }}'"
                        >
                        <div>
                            <p class="text-sm font-medium text-surface-900 dark:text-white" x-text="product?.seller?.name"></p>
                            <p class="text-xs text-surface-500">Seller</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3">
                        <button
                            @click="addToCart()"
                            :disabled="cartLoading || inCart"
                            class="flex-1 flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-white font-semibold transition-colors"
                            :class="inCart ? 'bg-success-500' : 'bg-primary-600 hover:bg-primary-700'"
                        >
                            <svg x-show="cartLoading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg x-show="inCart && !cartLoading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span x-text="inCart ? 'Added to Cart' : 'Add to Cart'"></span>
                        </button>

                        <button
                            @click="toggleWishlist()"
                            :disabled="wishlistLoading"
                            class="w-12 h-12 flex items-center justify-center rounded-xl border-2 transition-colors"
                            :class="inWishlist ? 'border-danger-500 bg-danger-50 dark:bg-danger-900/20 text-danger-500' : 'border-surface-300 dark:border-surface-600 text-surface-600 dark:text-surface-400 hover:border-danger-500 hover:text-danger-500'"
                        >
                            <svg x-show="!wishlistLoading" class="w-5 h-5" :fill="inWishlist ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <svg x-show="wishlistLoading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- View Details Link -->
                    <a
                        :href="product?.url"
                        class="mt-4 text-center text-sm font-medium text-primary-600 dark:text-primary-400 hover:underline"
                    >
                        View Full Details &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function quickViewModal() {
    const currencySymbol = '{{ current_currency_symbol() }}';
    const currencyPosition = '{{ current_currency()->symbol_position }}';
    const exchangeRate = {{ current_currency()->exchange_rate }};

    function formatPrice(amount) {
        if (!amount) return currencySymbol + '0.00';
        const converted = (parseFloat(amount) * exchangeRate).toFixed(2);
        return currencyPosition === 'after' ? converted + ' ' + currencySymbol : currencySymbol + converted;
    }

    return {
        isOpen: false,
        loading: false,
        product: null,
        currentImage: null,
        inCart: false,
        inWishlist: false,
        cartLoading: false,
        wishlistLoading: false,
        formatPrice: formatPrice,

        open(data) {
            this.isOpen = true;
            this.loading = true;
            this.product = null;
            this.currentImage = null;
            this.inCart = false;
            this.inWishlist = data.inWishlist || false;
            document.body.style.overflow = 'hidden';

            // Fetch full product data
            fetch(`{{ url('/api/products') }}/${data.id}`)
                .then(response => response.json())
                .then(productData => {
                    this.product = productData;
                    this.currentImage = productData.thumbnail;
                    this.inWishlist = productData.in_wishlist || false;
                    this.loading = false;
                })
                .catch(error => {
                    console.error('Failed to load product:', error);
                    // Fallback to passed data
                    this.product = data;
                    this.currentImage = data.thumbnail;
                    this.loading = false;
                });
        },

        close() {
            this.isOpen = false;
            document.body.style.overflow = '';
        },

        async addToCart() {
            if (this.cartLoading || this.inCart || !this.product) return;
            this.cartLoading = true;

            try {
                const response = await fetch(`{{ url('/cart/add') }}/${this.product.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                this.inCart = true;
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
            } catch (error) {
                console.error('Error adding to cart:', error);
            } finally {
                this.cartLoading = false;
            }
        },

        async toggleWishlist() {
            if (this.wishlistLoading || !this.product) return;
            this.wishlistLoading = true;

            try {
                const response = await fetch(`{{ url('/wishlist') }}/${this.product.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                this.inWishlist = data.in_wishlist;
            } catch (error) {
                console.error('Error updating wishlist:', error);
            } finally {
                this.wishlistLoading = false;
            }
        }
    }
}
</script>
@endpush
