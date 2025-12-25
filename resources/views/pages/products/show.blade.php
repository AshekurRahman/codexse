<x-layouts.app :title="$product->name . ' - Codexse'">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm mb-6">
                <a href="{{ route('home') }}" class="text-surface-500 dark:text-surface-400 hover:text-primary-600 dark:hover:text-primary-400">Home</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('products.index') }}" class="text-surface-500 dark:text-surface-400 hover:text-primary-600 dark:hover:text-primary-400">Products</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('categories.show', $product->category) }}" class="text-surface-500 dark:text-surface-400 hover:text-primary-600 dark:hover:text-primary-400">{{ $product->category->name }}</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-surface-900 dark:text-white font-medium truncate">{{ $product->name }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Product Preview -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Main Image -->
                    <div class="aspect-video rounded-2xl overflow-hidden bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700">
                        @if($product->thumbnail)
                            <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-surface-300 dark:text-surface-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Preview Images -->
                    @if($product->preview_images && count($product->preview_images) > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            @foreach($product->preview_images as $image)
                                <div class="aspect-video rounded-lg overflow-hidden bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 cursor-pointer hover:border-primary-500 transition-colors">
                                    <img src="{{ $image }}" alt="Preview" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Tabs -->
                    <div x-data="{ tab: 'description' }" class="rounded-2xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                        <div class="flex border-b border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800">
                            <button @click="tab = 'description'" :class="tab === 'description' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white'" class="px-6 py-4 text-sm font-medium border-b-2 -mb-px transition-colors">
                                Description
                            </button>
                            <button @click="tab = 'reviews'" :class="tab === 'reviews' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white'" class="px-6 py-4 text-sm font-medium border-b-2 -mb-px transition-colors">
                                Reviews ({{ $product->reviews_count }})
                            </button>
                            <button @click="tab = 'changelog'" :class="tab === 'changelog' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white'" class="px-6 py-4 text-sm font-medium border-b-2 -mb-px transition-colors">
                                Changelog
                            </button>
                        </div>

                        <div class="p-6 bg-surface-50 dark:bg-surface-900">
                            <!-- Description -->
                            <div x-show="tab === 'description'" class="prose dark:prose-invert max-w-none">
                                {!! nl2br(e($product->description)) !!}
                            </div>

                            <!-- Reviews -->
                            <div x-show="tab === 'reviews'" x-cloak>
                                @if($product->reviews && $product->reviews->count() > 0)
                                    <div class="space-y-6">
                                        @foreach($product->reviews as $review)
                                            <div class="flex gap-4">
                                                <div class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                                                    <span class="text-sm font-medium text-primary-600 dark:text-primary-400">{{ substr($review->user->name, 0, 1) }}</span>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="font-medium text-surface-900 dark:text-white">{{ $review->user->name }}</span>
                                                        <div class="flex items-center text-yellow-500">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $i <= $review->rating ? 'fill-current' : 'text-surface-300' }}" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                                </svg>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                    <p class="text-surface-600 dark:text-surface-400">{{ $review->comment }}</p>
                                                    <span class="text-xs text-surface-400 mt-2 block">{{ $review->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        <p class="text-surface-600 dark:text-surface-400">No reviews yet. Be the first to review this product!</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Changelog -->
                            <div x-show="tab === 'changelog'" x-cloak>
                                @if($product->changelog)
                                    <div class="prose dark:prose-invert max-w-none">
                                        {!! nl2br(e($product->changelog)) !!}
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <p class="text-surface-600 dark:text-surface-400">No changelog available.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Purchase Card -->
                    <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6 sticky top-24">
                        <!-- Seller Info -->
                        <a href="{{ route('sellers.show', $product->seller) }}" class="flex items-center gap-3 mb-6 group">
                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center">
                                @if($product->seller->logo)
                                    <img src="{{ $product->seller->logo_url }}" alt="{{ $product->seller->store_name }}" class="h-12 w-12 rounded-full object-cover">
                                @else
                                    <span class="text-lg font-bold text-white">{{ substr($product->seller->store_name, 0, 1) }}</span>
                                @endif
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-surface-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ $product->seller->store_name }}</span>
                                    @if($product->seller->is_verified)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>
                                <span class="text-sm text-surface-500 dark:text-surface-400">{{ $product->seller->products_count ?? 0 }} products</span>
                            </div>
                        </a>

                        <!-- Price -->
                        <div class="mb-6">
                            @if($product->sale_price)
                                <div class="flex items-baseline gap-3">
                                    <span class="text-3xl font-bold text-surface-900 dark:text-white">${{ number_format($product->sale_price, 2) }}</span>
                                    <span class="text-lg text-surface-400 line-through">${{ number_format($product->price, 2) }}</span>
                                    <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:text-green-400">
                                        {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}% OFF
                                    </span>
                                </div>
                            @else
                                <span class="text-3xl font-bold text-surface-900 dark:text-white">${{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>

                        <!-- Add to Cart -->
                        <form action="{{ route('cart.add', $product) }}" method="POST" class="space-y-3">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-primary-600 px-6 py-4 text-base font-semibold text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 hover:shadow-xl transition-all">
                                Add to Cart
                            </button>
                        </form>

                        <button class="w-full mt-3 rounded-xl border-2 border-surface-200 dark:border-surface-700 px-6 py-4 text-base font-semibold text-surface-700 dark:text-surface-300 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            Add to Wishlist
                        </button>

                        <!-- Product Info -->
                        <div class="mt-6 pt-6 border-t border-surface-200 dark:border-surface-700 space-y-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-surface-500 dark:text-surface-400">Category</span>
                                <a href="{{ route('categories.show', $product->category) }}" class="font-medium text-surface-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">{{ $product->category->name }}</a>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-surface-500 dark:text-surface-400">Version</span>
                                <span class="font-medium text-surface-900 dark:text-white">{{ $product->version }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-surface-500 dark:text-surface-400">Sales</span>
                                <span class="font-medium text-surface-900 dark:text-white">{{ number_format($product->sales_count) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-surface-500 dark:text-surface-400">Rating</span>
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="font-medium text-surface-900 dark:text-white">{{ number_format($product->average_rating, 1) }}</span>
                                    <span class="text-surface-400">({{ $product->reviews_count }})</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-surface-500 dark:text-surface-400">Updated</span>
                                <span class="font-medium text-surface-900 dark:text-white">{{ $product->updated_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            @if($relatedProducts->count() > 0)
                <section class="mt-16">
                    <h2 class="text-2xl font-bold text-surface-900 dark:text-white mb-8">Related Products</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($relatedProducts as $relatedProduct)
                            <x-product-card :product="$relatedProduct" />
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-layouts.app>
