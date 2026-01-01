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
                    <!-- Product Title (visible on mobile, hidden on desktop - shown in sidebar on desktop) -->
                    <div class="lg:hidden">
                        <div class="flex items-start gap-3 mb-2">
                            @if($product->category)
                                <span class="text-sm font-medium text-primary-600 dark:text-primary-400 uppercase tracking-wide">{{ $product->category->name }}</span>
                            @endif
                            @if($product->sale_price)
                                <x-badge type="sale" icon="bolt">{{ round((1 - $product->sale_price / $product->price) * 100) }}% OFF</x-badge>
                            @endif
                        </div>
                        <h1 class="text-2xl font-bold text-surface-900 dark:text-white mb-3">{{ $product->name }}</h1>
                        <div class="flex items-center gap-4 text-sm text-surface-600 dark:text-surface-400">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-warning-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="font-medium">{{ number_format($product->average_rating, 1) }}</span>
                                <span>({{ $product->reviews_count }} reviews)</span>
                            </div>
                            <span class="w-1 h-1 rounded-full bg-surface-400"></span>
                            <span>{{ number_format($product->downloads_count) }} sales</span>
                        </div>
                    </div>

                    <!-- Product Gallery with Lightbox and Video Support -->
                    <x-product-gallery :product="$product" />

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
                                {!! $product->description !!}
                            </div>

                            <!-- Reviews -->
                            <div x-show="tab === 'reviews'" x-cloak>
                                @php
                                    $approvedReviews = $product->reviews()->approved()->with('user')->latest()->get();
                                    $userReview = auth()->check() ? $product->reviews()->where('user_id', auth()->id())->first() : null;
                                    $canReview = auth()->check() && !$userReview && auth()->user()->orders()
                                        ->where('status', 'completed')
                                        ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
                                        ->exists();
                                @endphp

                                <!-- Write Review Form -->
                                @auth
                                    @if($canReview)
                                        <div class="mb-8 p-6 rounded-xl bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700" x-data="{
                                            rating: 5,
                                            comment: '',
                                            loading: false,
                                            submitted: false,
                                            error: '',
                                            async submitReview() {
                                                if (this.loading || this.comment.length < 10) return;
                                                this.loading = true;
                                                this.error = '';

                                                try {
                                                    const response = await fetch('{{ route('reviews.store', $product) }}', {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                            'Accept': 'application/json',
                                                        },
                                                        body: JSON.stringify({
                                                            rating: this.rating,
                                                            comment: this.comment,
                                                        }),
                                                    });

                                                    const data = await response.json();

                                                    if (data.success) {
                                                        this.submitted = true;
                                                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                                                    } else {
                                                        this.error = data.message || 'Something went wrong';
                                                    }
                                                } catch (error) {
                                                    console.error('Error:', error);
                                                    this.error = 'Something went wrong. Please try again.';
                                                } finally {
                                                    this.loading = false;
                                                }
                                            }
                                        }">
                                            <template x-if="!submitted">
                                                <div>
                                                    <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Write a Review</h3>
                                                    <div class="mb-4">
                                                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Rating</label>
                                                        <div class="flex items-center gap-1">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <button type="button" @click="rating = {{ $i }}" class="focus:outline-none">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 transition-colors" :class="rating >= {{ $i }} ? 'text-yellow-500 fill-current' : 'text-surface-300 dark:text-surface-600'" viewBox="0 0 20 20" fill="currentColor">
                                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                                    </svg>
                                                                </button>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label for="comment" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Your Review</label>
                                                        <textarea x-model="comment" id="comment" rows="4" minlength="10" maxlength="2000"
                                                            class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-900 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                                            placeholder="Share your experience with this product..."></textarea>
                                                        <p x-show="error" class="mt-1 text-sm text-danger-600" x-text="error"></p>
                                                        <p x-show="comment.length > 0 && comment.length < 10" class="mt-1 text-sm text-surface-500">Minimum 10 characters required</p>
                                                    </div>
                                                    <button
                                                        @click="submitReview()"
                                                        :disabled="loading || comment.length < 10"
                                                        class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                    >
                                                        <svg x-show="loading" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                        <span x-text="loading ? 'Submitting...' : 'Submit Review'"></span>
                                                    </button>
                                                </div>
                                            </template>
                                            <template x-if="submitted">
                                                <div class="text-center py-4">
                                                    <svg class="w-12 h-12 text-success-500 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <h4 class="font-semibold text-surface-900 dark:text-white mb-1">Thank you for your review!</h4>
                                                    <p class="text-sm text-surface-500 dark:text-surface-400">Your review will be visible after approval.</p>
                                                </div>
                                            </template>
                                        </div>
                                    @elseif($userReview)
                                        <div class="mb-6 p-4 rounded-lg bg-info-50 dark:bg-info-900/20 border border-info-200 dark:border-info-800">
                                            <p class="text-sm text-info-700 dark:text-info-300">
                                                You have already reviewed this product.
                                                @if($userReview->status === 'pending')
                                                    <span class="font-medium">Your review is pending approval.</span>
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                @endauth

                                @if($approvedReviews->count() > 0)
                                    <div class="space-y-6">
                                        @foreach($approvedReviews as $review)
                                            <div class="flex gap-4 p-4 rounded-xl bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700" x-data="{
                                                voted: {{ auth()->check() && \DB::table('review_votes')->where('user_id', auth()->id())->where('review_id', $review->id)->exists() ? 'true' : 'false' }},
                                                helpfulCount: {{ $review->helpful_count }},
                                                loading: false,
                                                async vote() {
                                                    @guest
                                                        window.location.href = '{{ route('login') }}';
                                                        return;
                                                    @endguest
                                                    if (this.loading) return;
                                                    this.loading = true;
                                                    try {
                                                        const response = await fetch('{{ route('reviews.vote', $review) }}', {
                                                            method: 'POST',
                                                            headers: {
                                                                'Content-Type': 'application/json',
                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                'Accept': 'application/json'
                                                            }
                                                        });
                                                        const data = await response.json();
                                                        if (!data.error) {
                                                            this.voted = data.voted;
                                                            this.helpfulCount = data.helpful_count;
                                                        }
                                                    } catch (error) {
                                                        console.error('Error:', error);
                                                    } finally {
                                                        this.loading = false;
                                                    }
                                                }
                                            }">
                                                <div class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                                                    <span class="text-sm font-medium text-primary-600 dark:text-primary-400">{{ substr($review->user->name, 0, 1) }}</span>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                                        <span class="font-medium text-surface-900 dark:text-white">{{ $review->user->name }}</span>
                                                        @if($review->is_verified_purchase)
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-400">
                                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                                </svg>
                                                                Verified Purchase
                                                            </span>
                                                        @endif
                                                        <div class="flex items-center text-yellow-500">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $i <= $review->rating ? 'fill-current' : 'text-surface-300' }}" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                                </svg>
                                                            @endfor
                                                        </div>
                                                        <span class="text-xs text-surface-400">{{ $review->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <p class="text-surface-600 dark:text-surface-400 mb-3">{{ $review->comment }}</p>

                                                    <!-- Seller Response -->
                                                    @if($review->seller_response)
                                                        <div class="mt-4 ml-4 pl-4 border-l-2 border-primary-500">
                                                            <div class="flex items-center gap-2 mb-1">
                                                                <span class="text-sm font-medium text-primary-600 dark:text-primary-400">Seller Response</span>
                                                                <span class="text-xs text-surface-400">{{ $review->seller_responded_at?->diffForHumans() }}</span>
                                                            </div>
                                                            <p class="text-sm text-surface-600 dark:text-surface-400">{{ $review->seller_response }}</p>
                                                        </div>
                                                    @endif

                                                    <!-- Helpful Vote -->
                                                    @if(auth()->id() !== $review->user_id)
                                                        <div class="mt-3 pt-3 border-t border-surface-100 dark:border-surface-700">
                                                            <button @click="vote()" :disabled="loading"
                                                                class="inline-flex items-center gap-2 text-sm transition-colors"
                                                                :class="voted ? 'text-primary-600 dark:text-primary-400' : 'text-surface-500 hover:text-surface-700 dark:text-surface-400 dark:hover:text-surface-300'">
                                                                <svg class="w-4 h-4" :fill="voted ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                                                </svg>
                                                                <span x-text="'Helpful (' + helpfulCount + ')'"></span>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        <p class="text-surface-600 dark:text-surface-400 mb-2">No reviews yet.</p>
                                        @guest
                                            <p class="text-sm text-surface-500 dark:text-surface-400">
                                                <a href="{{ route('login') }}" class="text-primary-600 dark:text-primary-400 hover:underline">Sign in</a> to write a review.
                                            </p>
                                        @else
                                            @if(!$canReview && !$userReview)
                                                <p class="text-sm text-surface-500 dark:text-surface-400">Purchase this product to leave a review.</p>
                                            @endif
                                        @endguest
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
                        <!-- Product Title (Desktop Only) -->
                        <div class="hidden lg:block mb-6 pb-6 border-b border-surface-100 dark:border-surface-700">
                            <div class="flex items-center gap-2 mb-2">
                                @if($product->category)
                                    <span class="text-xs font-medium text-primary-600 dark:text-primary-400 uppercase tracking-wide">{{ $product->category->name }}</span>
                                @endif
                                @if($product->sale_price)
                                    <x-badge type="sale" icon="bolt" size="xs">{{ round((1 - $product->sale_price / $product->price) * 100) }}% OFF</x-badge>
                                @endif
                            </div>
                            <h1 class="text-xl font-bold text-surface-900 dark:text-white mb-3">{{ $product->name }}</h1>
                            <div class="flex items-center gap-3 text-sm text-surface-600 dark:text-surface-400">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4 text-warning-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span class="font-medium">{{ number_format($product->average_rating, 1) }}</span>
                                    <span class="text-surface-400">({{ $product->reviews_count }})</span>
                                </div>
                                <span class="w-1 h-1 rounded-full bg-surface-300 dark:bg-surface-600"></span>
                                <span>{{ number_format($product->downloads_count) }} sales</span>
                            </div>
                        </div>

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

                        <!-- Seller Vacation Notice -->
                        @if($product->seller->isOnVacation())
                            <div class="mb-4 p-3 bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-xl">
                                <div class="flex items-start gap-2">
                                    <svg class="h-5 w-5 text-warning-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-warning-800 dark:text-warning-300">Seller on vacation</p>
                                        <p class="text-xs text-warning-700 dark:text-warning-400 mt-0.5">
                                            @if($product->seller->vacation_ends_at)
                                                Returns {{ $product->seller->vacation_ends_at->diffForHumans() }}
                                            @else
                                                Support responses may be delayed
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Price & Variations -->
                        @if($product->has_variations && $product->activeVariations->count() > 0)
                            <div x-data="{
                                variations: {{ $product->activeVariations->toJson() }},
                                selectedIndex: {{ $product->activeVariations->search(fn($v) => $v->is_default) !== false ? $product->activeVariations->search(fn($v) => $v->is_default) : 0 }},
                                get selected() { return this.variations[this.selectedIndex]; },
                                loading: false,
                                added: false,
                                async addToCart() {
                                    if (this.loading) return;
                                    this.loading = true;

                                    try {
                                        const response = await fetch('{{ route('cart.add', $product) }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json',
                                            },
                                            body: JSON.stringify({
                                                variation_id: this.selected.id
                                            })
                                        });

                                        const data = await response.json();

                                        if (data.success) {
                                            this.added = true;
                                            window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
                                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                                        } else {
                                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'info' } }));
                                        }
                                    } catch (error) {
                                        console.error('Error:', error);
                                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Something went wrong', type: 'error' } }));
                                    } finally {
                                        this.loading = false;
                                    }
                                }
                            }" class="mb-6">
                                <!-- Variation Selector -->
                                <div class="space-y-3 mb-6">
                                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Select Version</label>
                                    <div class="grid gap-3">
                                        <template x-for="(variation, index) in variations" :key="variation.id">
                                            <button type="button" @click="selectedIndex = index; added = false"
                                                class="w-full p-4 rounded-xl border-2 text-left transition-all"
                                                :class="selectedIndex === index
                                                    ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                                                    : 'border-surface-200 dark:border-surface-700 hover:border-surface-300 dark:hover:border-surface-600'">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-semibold text-surface-900 dark:text-white" x-text="variation.name"></span>
                                                            <span x-show="variation.is_default" class="text-xs px-2 py-0.5 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 rounded">Popular</span>
                                                        </div>
                                                        <p x-show="variation.description" class="text-sm text-surface-500 dark:text-surface-400 mt-1" x-text="variation.description"></p>
                                                    </div>
                                                    <div class="text-right">
                                                        <div class="flex items-center gap-2">
                                                            <span x-show="variation.regular_price && parseFloat(variation.regular_price) > parseFloat(variation.price)" class="text-sm text-surface-400 line-through" x-text="'$' + parseFloat(variation.regular_price).toFixed(2)"></span>
                                                            <span class="text-xl font-bold text-surface-900 dark:text-white" x-text="'$' + parseFloat(variation.price).toFixed(2)"></span>
                                                        </div>
                                                        <span class="text-xs text-surface-500" x-text="variation.license_type === 'extended' ? 'Extended License' : 'Regular License'"></span>
                                                    </div>
                                                </div>
                                                <!-- Features -->
                                                <div x-show="variation.features && variation.features.length > 0" class="mt-3 pt-3 border-t border-surface-200 dark:border-surface-700">
                                                    <div class="flex flex-wrap gap-2">
                                                        <template x-for="feature in variation.features" :key="feature">
                                                            <span class="inline-flex items-center gap-1 text-xs text-surface-600 dark:text-surface-400">
                                                                <svg class="w-3 h-3 text-success-500" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                                </svg>
                                                                <span x-text="feature"></span>
                                                            </span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <!-- Selected Price Display -->
                                <div class="mb-6 p-4 bg-surface-50 dark:bg-surface-900 rounded-xl">
                                    <div class="flex items-baseline justify-between">
                                        <span class="text-surface-600 dark:text-surface-400">Selected:</span>
                                        <div class="text-right">
                                            <span class="text-2xl font-bold text-surface-900 dark:text-white" x-text="'$' + parseFloat(selected.price).toFixed(2)"></span>
                                            <div class="text-sm text-surface-500" x-text="selected.support_months > 0 ? selected.support_months + ' months support' : 'Lifetime support'"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Add to Cart Button -->
                                <button
                                    @click="addToCart()"
                                    :disabled="loading"
                                    class="w-full rounded-xl px-6 py-4 text-base font-semibold shadow-lg transition-all flex items-center justify-center gap-2"
                                    :class="added ? 'bg-success-600 text-white shadow-success-500/30' : 'bg-primary-600 text-white shadow-primary-500/30 hover:bg-primary-700 hover:shadow-xl'"
                                >
                                    <svg x-show="loading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <svg x-show="!loading && added" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span x-text="added ? 'Added to Cart' : 'Add to Cart'"></span>
                                </button>
                            </div>
                        @else
                            <!-- Regular Price Display -->
                            <div class="mb-6">
                                @if($product->sale_price)
                                    <div class="flex items-baseline gap-3">
                                        <span class="text-3xl font-bold text-surface-900 dark:text-white">{{ format_price($product->sale_price) }}</span>
                                        <span class="text-lg text-surface-400 line-through">{{ format_price($product->price) }}</span>
                                        <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:text-green-400">
                                            {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}% OFF
                                        </span>
                                    </div>
                                @else
                                    <span class="text-3xl font-bold text-surface-900 dark:text-white">{{ format_price($product->price) }}</span>
                                @endif
                            </div>

                            <!-- Add to Cart -->
                            <div x-data="{
                                loading: false,
                                added: false,
                                async addToCart() {
                                    if (this.loading) return;
                                    this.loading = true;

                                    try {
                                        const response = await fetch('{{ route('cart.add', $product) }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json',
                                            },
                                        });

                                        const data = await response.json();

                                        if (data.success) {
                                            this.added = true;
                                            // Update cart count in navbar
                                            window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
                                            // Show toast notification
                                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                                        } else {
                                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'info' } }));
                                        }
                                    } catch (error) {
                                        console.error('Error:', error);
                                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Something went wrong', type: 'error' } }));
                                    } finally {
                                        this.loading = false;
                                    }
                                }
                            }">
                                <button
                                    @click="addToCart()"
                                    :disabled="loading"
                                    class="w-full rounded-xl px-6 py-4 text-base font-semibold shadow-lg transition-all flex items-center justify-center gap-2"
                                    :class="added ? 'bg-success-600 text-white shadow-success-500/30' : 'bg-primary-600 text-white shadow-primary-500/30 hover:bg-primary-700 hover:shadow-xl'"
                                >
                                    <svg x-show="loading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <svg x-show="!loading && added" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span x-text="added ? 'Added to Cart' : 'Add to Cart'"></span>
                                </button>
                            </div>
                        @endif

                        <div x-data="{
                            inWishlist: {{ auth()->check() && auth()->user()->wishlists()->where('product_id', $product->id)->exists() ? 'true' : 'false' }},
                            loading: false,
                            async toggleWishlist() {
                                @guest
                                    window.location.href = '{{ route('login') }}';
                                    return;
                                @endguest

                                if (this.loading) return;
                                this.loading = true;

                                try {
                                    const response = await fetch('{{ route('wishlist.toggle', $product) }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        }
                                    });
                                    const data = await response.json();
                                    this.inWishlist = data.in_wishlist;
                                } catch (error) {
                                    console.error('Error:', error);
                                } finally {
                                    this.loading = false;
                                }
                            }
                        }">
                            <button
                                @click="toggleWishlist()"
                                :disabled="loading"
                                class="w-full mt-3 rounded-xl border-2 px-6 py-4 text-base font-semibold transition-all flex items-center justify-center gap-2"
                                :class="inWishlist
                                    ? 'border-danger-500 bg-danger-50 dark:bg-danger-900/20 text-danger-600 dark:text-danger-400'
                                    : 'border-surface-200 dark:border-surface-700 text-surface-700 dark:text-surface-300 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400'"
                            >
                                <svg x-show="!loading" class="h-5 w-5" :fill="inWishlist ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <svg x-show="loading" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24" x-cloak>
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="inWishlist ? 'In Wishlist' : 'Add to Wishlist'"></span>
                            </button>
                        </div>

                        <!-- Action Buttons -->
                        @if($product->demo_url || $product->preview_url)
                            <div class="mt-4 flex gap-3">
                                @if($product->demo_url)
                                    <a href="{{ $product->demo_url }}" target="_blank" rel="noopener noreferrer"
                                        class="flex-1 rounded-xl border-2 border-surface-200 dark:border-surface-700 px-4 py-3 text-sm font-semibold text-surface-700 dark:text-surface-300 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 transition-all flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Live Demo
                                    </a>
                                @endif
                                @if($product->preview_url)
                                    <a href="{{ $product->preview_url }}" target="_blank" rel="noopener noreferrer"
                                        class="flex-1 rounded-xl border-2 border-surface-200 dark:border-surface-700 px-4 py-3 text-sm font-semibold text-surface-700 dark:text-surface-300 hover:border-accent-500 hover:text-accent-600 dark:hover:text-accent-400 transition-all flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        Preview
                                    </a>
                                @endif
                            </div>
                        @endif

                        <!-- Subscription Plans -->
                        @if($product->hasSubscriptionPlans())
                            <div class="mt-6 pt-6 border-t border-surface-200 dark:border-surface-700">
                                <div class="flex items-center gap-2 mb-4">
                                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    <h3 class="font-semibold text-surface-900 dark:text-white">Subscription Available</h3>
                                </div>
                                <p class="text-sm text-surface-600 dark:text-surface-400 mb-4">Get ongoing access with a subscription plan</p>
                                <div class="space-y-3">
                                    @foreach($product->activeSubscriptionPlans->take(2) as $plan)
                                        <a href="{{ route('subscriptions.show', $plan) }}" class="block p-3 rounded-xl border border-surface-200 dark:border-surface-700 hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <span class="font-medium text-surface-900 dark:text-white">{{ $plan->name }}</span>
                                                    @if($plan->trial_days > 0)
                                                        <span class="ml-2 text-xs text-success-600 dark:text-success-400">{{ $plan->trial_days }}-day trial</span>
                                                    @endif
                                                </div>
                                                <div class="text-right">
                                                    <span class="font-bold text-surface-900 dark:text-white">{{ $plan->formatted_price }}</span>
                                                    <span class="text-xs text-surface-500">/{{ strtolower($plan->billing_period_label) }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                                @if($product->activeSubscriptionPlans->count() > 2)
                                    <a href="{{ route('subscriptions.index', ['product_id' => $product->id]) }}" class="inline-flex items-center gap-1 mt-3 text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                                        View all plans
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        @endif

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

    <!-- Recently Viewed Products -->
    @if(isset($recentlyViewed) && $recentlyViewed->count() > 0)
        <x-recently-viewed :products="$recentlyViewed" />
    @endif
</x-layouts.app>
