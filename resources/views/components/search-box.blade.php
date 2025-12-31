@props(['class' => ''])

<div
    x-data="{
        query: '',
        results: { products: [], categories: [] },
        isOpen: false,
        loading: false,
        selectedIndex: -1,
        debounceTimer: null,

        async search() {
            if (this.query.length < 2) {
                this.results = { products: [], categories: [] };
                this.isOpen = false;
                return;
            }

            this.loading = true;

            try {
                const response = await fetch(`{{ route('products.suggestions') }}?q=${encodeURIComponent(this.query)}`);
                this.results = await response.json();
                this.isOpen = true;
                this.selectedIndex = -1;
            } catch (error) {
                console.error('Search error:', error);
            } finally {
                this.loading = false;
            }
        },

        debounceSearch() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => this.search(), 300);
        },

        get totalResults() {
            return this.results.products.length + this.results.categories.length;
        },

        navigate(direction) {
            if (direction === 'down') {
                this.selectedIndex = Math.min(this.selectedIndex + 1, this.totalResults - 1);
            } else {
                this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
            }
        },

        selectCurrent() {
            const allItems = [...this.results.products, ...this.results.categories];
            if (this.selectedIndex >= 0 && allItems[this.selectedIndex]) {
                window.location.href = allItems[this.selectedIndex].url;
            } else if (this.query) {
                this.$refs.form.submit();
            }
        },

        close() {
            setTimeout(() => this.isOpen = false, 200);
        }
    }"
    class="relative {{ $class }}"
    @keydown.escape="isOpen = false"
>
    <form
        x-ref="form"
        action="{{ route('products.index') }}"
        method="GET"
        class="relative"
    >
        <div class="relative">
            <input
                type="text"
                name="search"
                x-model="query"
                @input="debounceSearch()"
                @focus="query.length >= 2 && (isOpen = true)"
                @blur="close()"
                @keydown.down.prevent="navigate('down')"
                @keydown.up.prevent="navigate('up')"
                @keydown.enter.prevent="selectCurrent()"
                placeholder="Search products..."
                autocomplete="off"
                class="w-full pl-10 pr-4 py-2.5 bg-surface-100 dark:bg-surface-800 border-0 rounded-lg text-surface-900 dark:text-white placeholder-surface-500 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 transition-shadow"
            >
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg x-show="!loading" class="w-5 h-5 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <svg x-show="loading" class="w-5 h-5 text-surface-400 animate-spin" fill="none" viewBox="0 0 24 24" x-cloak>
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
    </form>

    <!-- Dropdown Results -->
    <div
        x-show="isOpen && totalResults > 0"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute z-50 w-full mt-2 bg-white dark:bg-surface-800 rounded-xl shadow-xl border border-surface-200 dark:border-surface-700 overflow-hidden"
        x-cloak
    >
        <!-- Categories -->
        <template x-if="results.categories.length > 0">
            <div>
                <div class="px-4 py-2 text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider bg-surface-50 dark:bg-surface-900/50">
                    Categories
                </div>
                <template x-for="(category, index) in results.categories" :key="'cat-' + category.id">
                    <a
                        :href="category.url"
                        class="flex items-center gap-3 px-4 py-3 hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors"
                        :class="{ 'bg-surface-50 dark:bg-surface-700/50': selectedIndex === results.products.length + index }"
                    >
                        <div class="w-8 h-8 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-surface-900 dark:text-white" x-text="category.name"></span>
                    </a>
                </template>
            </div>
        </template>

        <!-- Products -->
        <template x-if="results.products.length > 0">
            <div>
                <div class="px-4 py-2 text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider bg-surface-50 dark:bg-surface-900/50">
                    Products
                </div>
                <template x-for="(product, index) in results.products" :key="'prod-' + product.id">
                    <a
                        :href="product.url"
                        class="flex items-center gap-3 px-4 py-3 hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors"
                        :class="{ 'bg-surface-50 dark:bg-surface-700/50': selectedIndex === index }"
                    >
                        <div class="w-12 h-12 rounded-lg bg-surface-100 dark:bg-surface-700 overflow-hidden shrink-0">
                            <img :src="product.thumbnail" :alt="product.name" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-surface-900 dark:text-white truncate" x-text="product.name"></p>
                            <p class="text-xs text-surface-500 dark:text-surface-400" x-text="product.category"></p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-surface-900 dark:text-white" x-text="'$' + parseFloat(product.price).toFixed(2)"></p>
                            <template x-if="product.original_price">
                                <p class="text-xs text-surface-400 line-through" x-text="'$' + parseFloat(product.original_price).toFixed(2)"></p>
                            </template>
                        </div>
                    </a>
                </template>
            </div>
        </template>

        <!-- View All -->
        <a
            :href="'{{ route('products.index') }}?search=' + encodeURIComponent(query)"
            class="flex items-center justify-center gap-2 px-4 py-3 bg-surface-50 dark:bg-surface-900/50 text-sm font-medium text-primary-600 dark:text-primary-400 hover:bg-surface-100 dark:hover:bg-surface-900 transition-colors"
        >
            View all results
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        </a>
    </div>

    <!-- No Results -->
    <div
        x-show="isOpen && query.length >= 2 && totalResults === 0 && !loading"
        class="absolute z-50 w-full mt-2 bg-white dark:bg-surface-800 rounded-xl shadow-xl border border-surface-200 dark:border-surface-700 p-6 text-center"
        x-cloak
    >
        <svg class="w-12 h-12 text-surface-300 dark:text-surface-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-surface-600 dark:text-surface-400">No results found for "<span x-text="query" class="font-medium"></span>"</p>
    </div>
</div>
