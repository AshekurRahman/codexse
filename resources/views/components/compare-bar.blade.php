<!-- Floating Compare Bar -->
<div
    x-data="{
        products: [],
        show: false,
        baseUrl: '{{ url('/') }}',

        init() {
            this.loadCompareList();
            window.addEventListener('compare-updated', (e) => {
                if (e.detail && e.detail.products) {
                    this.products = e.detail.products;
                    this.show = this.products.length > 0;
                } else {
                    this.loadCompareList();
                }
            });
        },

        async loadCompareList() {
            try {
                const response = await fetch(this.baseUrl + '/compare/list');
                const data = await response.json();
                this.products = data.products || [];
                this.show = this.products.length > 0;
            } catch (error) {
                console.error('Error loading compare list');
            }
        },

        async removeProduct(productId) {
            try {
                const response = await fetch(this.baseUrl + `/compare/remove/${productId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    }
                });
                const data = await response.json();
                this.products = data.products || [];
                this.show = this.products.length > 0;
                window.dispatchEvent(new CustomEvent('compare-updated', { detail: data }));
            } catch (error) {
                console.error('Error removing product');
            }
        },

        async clearAll() {
            try {
                const response = await fetch(this.baseUrl + '/compare/clear', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    }
                });
                const data = await response.json();
                this.products = [];
                this.show = false;
                window.dispatchEvent(new CustomEvent('compare-updated', { detail: data }));
            } catch (error) {
                console.error('Error clearing compare list');
            }
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="translate-y-full opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="translate-y-0 opacity-100"
    x-transition:leave-end="translate-y-full opacity-0"
    class="fixed bottom-0 left-0 right-0 z-40 bg-white dark:bg-surface-800 border-t border-surface-200 dark:border-surface-700 shadow-2xl"
    x-cloak
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between gap-4">
            <!-- Products Preview -->
            <div class="flex items-center gap-4 overflow-x-auto flex-1 pb-2 sm:pb-0">
                <div class="flex items-center gap-2 flex-shrink-0">
                    <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-sm font-semibold text-surface-900 dark:text-white">Compare</p>
                        <p class="text-xs text-surface-500" x-text="products.length + ' of 4 products'"></p>
                    </div>
                </div>

                <!-- Product Thumbnails -->
                <div class="flex items-center gap-3">
                    <template x-for="product in products" :key="product.id">
                        <div class="relative group flex-shrink-0">
                            <div class="w-16 h-16 rounded-xl overflow-hidden border-2 border-surface-200 dark:border-surface-600 bg-surface-100 dark:bg-surface-700 flex items-center justify-center">
                                <template x-if="product.thumbnail">
                                    <img
                                        :src="product.thumbnail"
                                        :alt="product.name"
                                        class="w-full h-full object-cover"
                                        onerror="this.style.display='none'"
                                    >
                                </template>
                                <template x-if="!product.thumbnail">
                                    <div class="w-full h-full flex items-center justify-center bg-surface-100 dark:bg-surface-700">
                                        <svg class="w-6 h-6 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </template>
                            </div>
                            <button
                                @click="removeProduct(product.id)"
                                class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </template>

                    <!-- Empty Slots -->
                    <template x-for="i in (4 - products.length)" :key="'empty-' + i">
                        <div class="w-16 h-16 rounded-xl border-2 border-dashed border-surface-300 dark:border-surface-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 flex-shrink-0">
                <button
                    @click="clearAll()"
                    class="px-4 py-2.5 text-sm font-medium text-surface-600 dark:text-surface-400 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                >
                    Clear All
                </button>
                <a
                    href="{{ route('compare.index') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700 transition-colors shadow-lg shadow-primary-500/25"
                    :class="{ 'opacity-50 pointer-events-none': products.length < 2 }"
                >
                    <span>Compare Now</span>
                    <span class="bg-white/20 px-2 py-0.5 rounded-lg text-xs" x-text="products.length"></span>
                </a>
            </div>
        </div>
    </div>
</div>
