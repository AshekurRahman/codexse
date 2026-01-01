@props(['product' => null])

<article
    x-data="{
        inWishlist: {{ $product && auth()->check() && auth()->user()->wishlists()->where('product_id', $product->id)->exists() ? 'true' : 'false' }},
        inCompare: false,
        inCart: false,
        loading: false,
        wishlistLoading: false,
        compareLoading: false,
        message: '',
        showMessage: false,
        baseUrl: '{{ url('/') }}',

        init() {
            // Check if product is in compare list
            this.checkCompareStatus();
            window.addEventListener('compare-updated', () => this.checkCompareStatus());
        },

        async checkCompareStatus() {
            try {
                const response = await fetch(this.baseUrl + '/compare/list');
                const data = await response.json();
                this.inCompare = data.products.some(p => p.id === {{ $product?->id ?? 0 }});
            } catch (error) {
                console.error('Error checking compare status');
            }
        },

        async toggleCompare() {
            if (this.compareLoading) return;
            this.compareLoading = true;

            try {
                const url = this.inCompare
                    ? this.baseUrl + '/compare/remove/{{ $product?->id ?? 0 }}'
                    : this.baseUrl + '/compare/add/{{ $product?->id ?? 0 }}';

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    this.inCompare = !this.inCompare;
                }
                this.showNotification(data.message);
                window.dispatchEvent(new CustomEvent('compare-updated', { detail: data }));
            } catch (error) {
                this.showNotification('Error updating compare list', true);
            } finally {
                this.compareLoading = false;
            }
        },

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
    class="group relative rounded-2xl bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 overflow-hidden hover:shadow-xl hover:shadow-primary-500/5 dark:hover:shadow-primary-500/10 hover:border-primary-200 dark:hover:border-primary-800 transition-all duration-300"
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
        class="absolute top-3 left-3 right-3 z-20 bg-surface-900 dark:bg-white text-white dark:text-surface-900 text-xs font-medium px-3 py-2 rounded-lg text-center shadow-lg"
        x-text="message"
        x-cloak
    ></div>

    <!-- Image -->
    <a href="{{ $product ? route('products.show', $product) : '#' }}" class="relative block aspect-[4/3] bg-surface-100 dark:bg-surface-700 overflow-hidden">
        @if($product && $product->thumbnail)
            <img
                src="{{ $product->thumbnail_url }}"
                alt="{{ $product->name }}"
                class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500"
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
            <div class="absolute top-3 left-3">
                <x-badge type="sale" icon="bolt">
                    {{ round((1 - $product->sale_price / $product->price) * 100) }}% OFF
                </x-badge>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="absolute top-3 right-3 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
            <!-- Wishlist Button -->
            <button
                @click.prevent="toggleWishlist()"
                :disabled="wishlistLoading"
                class="w-9 h-9 bg-white dark:bg-surface-800 rounded-xl flex items-center justify-center shadow-lg hover:bg-danger-50 dark:hover:bg-danger-900/30 transition-colors"
                :class="inWishlist ? 'text-danger-500' : 'text-surface-600 dark:text-surface-400 hover:text-danger-500'"
                title="Add to Wishlist"
            >
                <svg x-show="!wishlistLoading" class="w-4 h-4" :fill="inWishlist ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <svg x-show="wishlistLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" x-cloak>
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>

            <!-- Quick View Button -->
            <button
                @click.prevent="$dispatch('open-quick-view', {
                    id: {{ $product?->id ?? 0 }},
                    name: '{{ addslashes($product?->name ?? '') }}',
                    thumbnail: '{{ $product?->thumbnail_url ?? '' }}',
                    price: {{ $product?->price ?? 0 }},
                    sale_price: {{ $product?->sale_price ?? 'null' }},
                    category: '{{ addslashes($product?->category?->name ?? '') }}',
                    rating: {{ $product?->average_rating ?? 0 }},
                    sales: {{ $product?->downloads_count ?? 0 }},
                    url: '{{ $product ? route('products.show', $product) : '#' }}',
                    inWishlist: inWishlist
                })"
                class="w-9 h-9 bg-white dark:bg-surface-800 rounded-xl flex items-center justify-center shadow-lg text-surface-600 dark:text-surface-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
                title="Quick View"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>

            <!-- Compare Button -->
            <button
                @click.prevent="toggleCompare()"
                :disabled="compareLoading"
                class="w-9 h-9 bg-white dark:bg-surface-800 rounded-xl flex items-center justify-center shadow-lg transition-colors"
                :class="inCompare ? 'text-accent-600 dark:text-accent-400 bg-accent-50 dark:bg-accent-900/30' : 'text-surface-600 dark:text-surface-400 hover:bg-accent-50 dark:hover:bg-accent-900/30 hover:text-accent-600 dark:hover:text-accent-400'"
                title="Add to Compare"
            >
                <svg x-show="!compareLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <svg x-show="compareLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" x-cloak>
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </div>
    </a>

    <!-- Content -->
    <div class="p-5">
        <!-- Category -->
        @if($product && $product->category)
            <p class="text-xs font-semibold text-primary-600 dark:text-primary-400 uppercase tracking-wide mb-2">{{ $product->category->name }}</p>
        @endif

        <!-- Title -->
        <h3 class="font-semibold text-surface-900 dark:text-white line-clamp-2 mb-3 min-h-[2.5rem]">
            <a href="{{ $product ? route('products.show', $product) : '#' }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                {{ $product?->name ?? 'Product Title' }}
            </a>
        </h3>

        <!-- Rating & Sales -->
        <div class="flex items-center gap-3 mb-4">
            <div class="flex items-center gap-1">
                <svg class="w-4 h-4 text-warning-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <span class="text-sm font-semibold text-surface-900 dark:text-white">{{ $product ? number_format($product->average_rating, 1) : '4.8' }}</span>
            </div>
            <span class="w-1 h-1 rounded-full bg-surface-300 dark:bg-surface-600"></span>
            <span class="text-sm text-surface-600 dark:text-surface-400">{{ $product ? number_format($product->downloads_count) : '1.2k' }} sales</span>
        </div>

        <!-- Price & Add to Cart -->
        <div class="flex items-center justify-between pt-4 border-t border-surface-100 dark:border-surface-700">
            <div>
                @if($product && $product->sale_price)
                    <span class="text-lg font-bold text-surface-900 dark:text-white">{{ format_price($product->sale_price) }}</span>
                    <span class="text-sm text-surface-400 line-through ml-1">{{ format_price($product->price) }}</span>
                @else
                    <span class="text-lg font-bold text-surface-900 dark:text-white">{{ $product ? format_price($product->price) : format_price(49.00) }}</span>
                @endif
            </div>

            <button
                @click="addToCart()"
                :disabled="loading || inCart"
                class="w-10 h-10 rounded-xl transition-all flex items-center justify-center"
                :class="inCart ? 'bg-success-500 text-white' : 'bg-primary-600 hover:bg-primary-700 hover:shadow-lg hover:shadow-primary-500/25 text-white'"
                :title="inCart ? 'Added to Cart' : 'Add to Cart'"
            >
                <svg x-show="loading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" x-cloak>
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg x-show="inCart && !loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <svg x-show="!inCart && !loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </button>
        </div>
    </div>
</article>
