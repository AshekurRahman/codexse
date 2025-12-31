<x-layouts.app :title="$seller->store_name . ' - Codexse'">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <!-- Hero Banner -->
        <div class="bg-gradient-to-br from-primary-600 via-primary-700 to-accent-600 dark:from-primary-800 dark:via-primary-900 dark:to-accent-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-sm mb-8">
                    <a href="{{ route('home') }}" class="text-primary-200 hover:text-white transition-colors">Home</a>
                    <svg class="h-4 w-4 text-primary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <a href="{{ route('sellers.index') }}" class="text-primary-200 hover:text-white transition-colors">Sellers</a>
                    <svg class="h-4 w-4 text-primary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-white font-medium">{{ $seller->store_name }}</span>
                </nav>

                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <!-- Seller Info -->
                    <div class="flex items-center gap-6">
                        <!-- Avatar -->
                        <div class="relative shrink-0">
                            <div class="h-28 w-28 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-2xl ring-4 ring-white/30">
                                @if($seller->logo)
                                    <img src="{{ $seller->logo_url }}" alt="{{ $seller->store_name }}" class="h-28 w-28 rounded-2xl object-cover">
                                @else
                                    <span class="text-4xl font-bold text-white">{{ substr($seller->store_name, 0, 1) }}</span>
                                @endif
                            </div>
                            @if($seller->is_verified)
                                <div class="absolute -bottom-2 -right-2 h-8 w-8 rounded-full bg-white shadow-lg flex items-center justify-center">
                                    <svg class="h-5 w-5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <h1 class="text-3xl font-bold text-white">{{ $seller->store_name }}</h1>
                                @if($seller->is_featured)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-400/20 text-amber-200 text-sm font-medium">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        Featured
                                    </span>
                                @endif
                            </div>
                            <p class="text-primary-100 max-w-xl">{{ $seller->description ?? 'Professional seller offering quality products and services' }}</p>

                            <!-- Member Since -->
                            <p class="text-primary-200 text-sm mt-2">
                                Member since {{ $seller->created_at->format('F Y') }}
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3">
                        @auth
                            <div x-data="{
                                following: {{ auth()->user()->isFollowing($seller) ? 'true' : 'false' }},
                                loading: false,
                                async toggleFollow() {
                                    if (this.loading) return;
                                    this.loading = true;

                                    try {
                                        const response = await fetch('{{ route('sellers.follow', $seller) }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json',
                                            },
                                        });

                                        const data = await response.json();

                                        if (data.success) {
                                            this.following = data.following;
                                            // Update followers count in stats bar
                                            const countEl = document.getElementById('followers-count');
                                            if (countEl) {
                                                countEl.textContent = data.followers_count;
                                            }
                                        }
                                    } catch (error) {
                                        console.error('Follow error:', error);
                                    } finally {
                                        this.loading = false;
                                    }
                                }
                            }">
                                <button
                                    @click="toggleFollow()"
                                    :disabled="loading"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold transition-all disabled:opacity-70"
                                    :class="following ? 'bg-white/20 text-white hover:bg-white/30' : 'bg-white text-primary-600 hover:bg-primary-50'"
                                >
                                    <!-- Loading spinner -->
                                    <svg x-show="loading" class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <!-- Following icon -->
                                    <svg x-show="!loading && following" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <!-- Follow icon -->
                                    <svg x-show="!loading && !following" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span x-text="following ? 'Following' : 'Follow'"></span>
                                </button>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold bg-white text-primary-600 hover:bg-primary-50 transition-all">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Follow
                            </a>
                        @endauth

                        @if($seller->website)
                            <a href="{{ $seller->website }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/10 text-white font-semibold hover:bg-white/20 transition-all">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Website
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Vacation Mode Notice -->
        @if($seller->isOnVacation())
            <div class="bg-warning-50 dark:bg-warning-900/30 border-b border-warning-200 dark:border-warning-800">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-warning-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-warning-800 dark:text-warning-300">
                                This seller is currently on vacation
                                @if($seller->vacation_ends_at)
                                    and will return {{ $seller->vacation_ends_at->diffForHumans() }}
                                @endif
                            </p>
                            @if($seller->vacation_message)
                                <p class="text-sm text-warning-700 dark:text-warning-400 mt-1">{{ $seller->vacation_message }}</p>
                            @else
                                <p class="text-sm text-warning-700 dark:text-warning-400 mt-1">Products are visible but purchases may be delayed.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Stats Bar -->
        <div class="bg-white dark:bg-surface-800 border-b border-surface-200 dark:border-surface-700 shadow-sm">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-center gap-8 py-4">
                    <div class="text-center px-6">
                        <div class="text-2xl font-bold text-surface-900 dark:text-white">{{ $totalProducts }}</div>
                        <div class="text-sm text-surface-500 dark:text-surface-400">Products</div>
                    </div>
                    <div class="h-8 w-px bg-surface-200 dark:bg-surface-700"></div>
                    <div class="text-center px-6">
                        <div class="text-2xl font-bold text-surface-900 dark:text-white">{{ $totalServices }}</div>
                        <div class="text-sm text-surface-500 dark:text-surface-400">Services</div>
                    </div>
                    <div class="h-8 w-px bg-surface-200 dark:bg-surface-700"></div>
                    <div class="text-center px-6">
                        <div class="text-2xl font-bold text-surface-900 dark:text-white">${{ number_format($seller->total_sales, 0) }}</div>
                        <div class="text-sm text-surface-500 dark:text-surface-400">Total Sales</div>
                    </div>
                    <div class="h-8 w-px bg-surface-200 dark:bg-surface-700"></div>
                    <div class="text-center px-6">
                        <div id="followers-count" class="text-2xl font-bold text-surface-900 dark:text-white">{{ $seller->followers_count ?? 0 }}</div>
                        <div class="text-sm text-surface-500 dark:text-surface-400">Followers</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
            <!-- Services Section -->
            @if($services->count() > 0)
                <section class="mb-12">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-surface-900 dark:text-white">Services</h2>
                            <p class="text-surface-500 dark:text-surface-400 mt-1">Professional services offered by {{ $seller->store_name }}</p>
                        </div>
                        @if($totalServices > 8)
                            <a href="{{ route('services.index', ['seller' => $seller->id]) }}" class="inline-flex items-center gap-2 text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium">
                                View All Services
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($services as $service)
                            <x-service-card :service="$service" />
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Products Section -->
            @if($products->count() > 0)
                <section class="mb-12">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-surface-900 dark:text-white">Products</h2>
                            <p class="text-surface-500 dark:text-surface-400 mt-1">Digital products by {{ $seller->store_name }}</p>
                        </div>
                        @if($totalProducts > 8)
                            <a href="{{ route('products.index', ['seller' => $seller->id]) }}" class="inline-flex items-center gap-2 text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium">
                                View All Products
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Empty State -->
            @if($products->count() === 0 && $services->count() === 0)
                <div class="text-center py-16 bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700">
                    <div class="w-20 h-20 rounded-full bg-surface-100 dark:bg-surface-700 flex items-center justify-center mx-auto mb-6">
                        <svg class="h-10 w-10 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-surface-900 dark:text-white mb-2">No listings yet</h3>
                    <p class="text-surface-500 dark:text-surface-400 mb-6 max-w-md mx-auto">This seller hasn't added any products or services yet. Check back later!</p>
                    <div class="flex items-center justify-center gap-4">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 transition-colors">
                            Browse Products
                        </a>
                        <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-surface-300 dark:border-surface-600 px-5 py-2.5 text-sm font-semibold text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                            Browse Services
                        </a>
                    </div>
                </div>
            @endif

            <!-- About Section -->
            @if($seller->description)
                <section class="mt-12">
                    <div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-8">
                        <h2 class="text-xl font-bold text-surface-900 dark:text-white mb-4">About {{ $seller->store_name }}</h2>
                        <div class="prose prose-surface dark:prose-invert max-w-none">
                            <p class="text-surface-600 dark:text-surface-400">{{ $seller->description }}</p>
                        </div>

                        @if($seller->website)
                            <div class="mt-6 pt-6 border-t border-surface-200 dark:border-surface-700">
                                <h3 class="text-sm font-semibold text-surface-900 dark:text-white mb-3">Connect</h3>
                                <a href="{{ $seller->website }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                    </svg>
                                    {{ parse_url($seller->website, PHP_URL_HOST) }}
                                </a>
                            </div>
                        @endif
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-layouts.app>
