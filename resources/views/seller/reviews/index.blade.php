<x-layouts.app title="Reviews">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Customer Reviews</h1>
                <p class="text-surface-600 dark:text-surface-400 mt-1">Manage and respond to reviews on your products</p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-surface-600 dark:text-surface-400">Total Reviews</p>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white mt-1">{{ number_format($stats['total']) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center text-primary-600 dark:text-primary-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-surface-600 dark:text-surface-400">Awaiting Response</p>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white mt-1">{{ number_format($stats['pending_response']) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-warning-100 dark:bg-warning-900/30 rounded-lg flex items-center justify-center text-warning-600 dark:text-warning-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-surface-600 dark:text-surface-400">Average Rating</p>
                            <div class="flex items-center gap-2 mt-1">
                                <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ number_format($stats['average_rating'], 1) }}</p>
                                <div class="flex items-center text-yellow-500">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= round($stats['average_rating']) ? 'fill-current' : 'text-surface-300' }}" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <div class="w-12 h-12 bg-success-100 dark:bg-success-900/30 rounded-lg flex items-center justify-center text-success-600 dark:text-success-400">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4 mb-6">
                <form method="GET" class="flex flex-wrap items-center gap-4">
                    <select name="status" class="rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-900 text-surface-900 dark:text-white text-sm">
                        <option value="">All Statuses</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>

                    <select name="rating" class="rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-900 text-surface-900 dark:text-white text-sm">
                        <option value="">All Ratings</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>

                    <select name="responded" class="rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-900 text-surface-900 dark:text-white text-sm">
                        <option value="">All</option>
                        <option value="no" {{ request('responded') === 'no' ? 'selected' : '' }}>Needs Response</option>
                        <option value="yes" {{ request('responded') === 'yes' ? 'selected' : '' }}>Responded</option>
                    </select>

                    <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Filter
                    </button>
                    @if(request()->hasAny(['status', 'rating', 'responded']))
                        <a href="{{ route('seller.reviews.index') }}" class="text-sm text-surface-500 hover:text-surface-700 dark:text-surface-400 dark:hover:text-surface-300">
                            Clear filters
                        </a>
                    @endif
                </form>
            </div>

            <!-- Reviews List -->
            <div class="space-y-6">
                @forelse($reviews as $review)
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden" x-data="{ showResponse: {{ $review->seller_response ? 'true' : 'false' }} }">
                        <div class="p-6">
                            <div class="flex items-start gap-4">
                                <div class="h-12 w-12 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                                    <span class="text-lg font-medium text-primary-600 dark:text-primary-400">{{ substr($review->user->name, 0, 1) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span class="font-semibold text-surface-900 dark:text-white">{{ $review->user->name }}</span>
                                        @if($review->is_verified_purchase)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-400">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                                Verified
                                            </span>
                                        @endif
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            @if($review->status === 'approved') bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-400
                                            @elseif($review->status === 'pending') bg-warning-100 dark:bg-warning-900/30 text-warning-700 dark:text-warning-400
                                            @else bg-danger-100 dark:bg-danger-900/30 text-danger-700 dark:text-danger-400 @endif">
                                            {{ ucfirst($review->status) }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="flex items-center text-yellow-500">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-5 h-5 {{ $i <= $review->rating ? 'fill-current' : 'text-surface-300' }}" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="text-sm text-surface-500 dark:text-surface-400">{{ $review->created_at->format('M d, Y') }}</span>
                                    </div>

                                    <a href="{{ route('products.show', $review->product) }}" class="inline-flex items-center gap-2 text-sm text-primary-600 dark:text-primary-400 hover:underline mb-3">
                                        <span class="font-medium">{{ $review->product->name }}</span>
                                    </a>

                                    <p class="text-surface-600 dark:text-surface-400">{{ $review->comment }}</p>

                                    <div class="flex items-center gap-2 mt-3 text-sm text-surface-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                        </svg>
                                        {{ $review->helpful_count }} found helpful
                                    </div>

                                    <!-- Seller Response -->
                                    @if($review->seller_response)
                                        <div class="mt-4 ml-4 pl-4 border-l-2 border-primary-500 bg-primary-50 dark:bg-primary-900/10 p-4 rounded-r-lg">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">Your Response</span>
                                                    <span class="text-xs text-surface-400">{{ $review->seller_responded_at?->diffForHumans() }}</span>
                                                </div>
                                                <form action="{{ route('seller.reviews.response.delete', $review) }}" method="POST" onsubmit="return confirm('Delete your response?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs text-danger-600 hover:text-danger-700">Delete</button>
                                                </form>
                                            </div>
                                            <p class="text-sm text-surface-600 dark:text-surface-400">{{ $review->seller_response }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Response Form -->
                            @if(!$review->seller_response && $review->status === 'approved')
                                <div class="mt-6 pt-6 border-t border-surface-200 dark:border-surface-700">
                                    <button @click="showResponse = !showResponse" class="inline-flex items-center gap-2 text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                        </svg>
                                        <span x-text="showResponse ? 'Cancel' : 'Write a Response'"></span>
                                    </button>

                                    <form x-show="showResponse" x-cloak action="{{ route('seller.reviews.respond', $review) }}" method="POST" class="mt-4">
                                        @csrf
                                        <textarea name="seller_response" rows="3" required minlength="10" maxlength="1000"
                                            class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-900 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                            placeholder="Write your response to this review..."></textarea>
                                        <div class="flex justify-end mt-3">
                                            <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                Submit Response
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-12 text-center">
                        <svg class="w-16 h-16 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">No reviews yet</h3>
                        <p class="text-surface-600 dark:text-surface-400">Reviews from customers will appear here</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($reviews->hasPages())
                <div class="mt-8">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
