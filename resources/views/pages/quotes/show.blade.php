<x-layouts.app :title="'Quote Request: ' . $quoteRequest->title">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-surface-500 dark:text-surface-400 mb-6">
                <a href="{{ route('quotes.index') }}" class="hover:text-primary-600">My Quotes</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-surface-900 dark:text-white truncate">{{ $quoteRequest->title }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Request Details -->
                    <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                        <div class="px-6 py-5 border-b border-surface-200 dark:border-surface-700 flex items-center justify-between">
                            <h1 class="text-xl font-bold text-surface-900 dark:text-white">{{ $quoteRequest->title }}</h1>
                            <x-status-badge :status="$quoteRequest->status" />
                        </div>

                        <div class="p-6">
                            <!-- Service Info -->
                            <div class="flex items-center gap-4 p-4 rounded-lg bg-surface-50 dark:bg-surface-700/50 mb-6">
                                @if($quoteRequest->service->thumbnail)
                                    <img src="{{ asset('storage/' . $quoteRequest->service->thumbnail) }}" alt="{{ $quoteRequest->service->name }}" class="w-14 h-14 rounded-lg object-cover">
                                @else
                                    <div class="w-14 h-14 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <a href="{{ route('services.show', $quoteRequest->service) }}" class="font-semibold text-surface-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">
                                        {{ $quoteRequest->service->name }}
                                    </a>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">by {{ $quoteRequest->seller->store_name }}</p>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mb-6">
                                <h3 class="text-sm font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider mb-2">Project Description</h3>
                                <div class="prose prose-surface dark:prose-invert max-w-none">
                                    {!! nl2br(e($quoteRequest->description)) !!}
                                </div>
                            </div>

                            <!-- Details Grid -->
                            <div class="grid grid-cols-2 gap-4">
                                @if($quoteRequest->budget_min || $quoteRequest->budget_max)
                                    <div class="p-3 rounded-lg bg-surface-50 dark:bg-surface-700/50">
                                        <p class="text-xs text-surface-500 dark:text-surface-400 uppercase">Budget Range</p>
                                        <p class="font-semibold text-surface-900 dark:text-white">
                                            ${{ number_format($quoteRequest->budget_min ?? 0) }} - ${{ number_format($quoteRequest->budget_max ?? 0) }}
                                        </p>
                                    </div>
                                @endif
                                @if($quoteRequest->deadline)
                                    <div class="p-3 rounded-lg bg-surface-50 dark:bg-surface-700/50">
                                        <p class="text-xs text-surface-500 dark:text-surface-400 uppercase">Deadline</p>
                                        <p class="font-semibold text-surface-900 dark:text-white">{{ $quoteRequest->deadline->format('M d, Y') }}</p>
                                    </div>
                                @endif
                                <div class="p-3 rounded-lg bg-surface-50 dark:bg-surface-700/50">
                                    <p class="text-xs text-surface-500 dark:text-surface-400 uppercase">Requested</p>
                                    <p class="font-semibold text-surface-900 dark:text-white">{{ $quoteRequest->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>

                            <!-- Attachments -->
                            @if($quoteRequest->attachments && count($quoteRequest->attachments) > 0)
                                <div class="mt-6">
                                    <h3 class="text-sm font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider mb-2">Attachments</h3>
                                    <div class="space-y-2">
                                        @foreach($quoteRequest->attachments as $attachment)
                                            <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank"
                                                class="flex items-center gap-3 p-3 rounded-lg border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                                <span class="text-sm text-surface-700 dark:text-surface-300">{{ $attachment['name'] }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quote Response (if exists) -->
                    @if($quoteRequest->quote)
                        <div class="rounded-2xl border-2 border-primary-500 bg-white dark:bg-surface-800 overflow-hidden">
                            <div class="px-6 py-4 bg-primary-50 dark:bg-primary-900/20 border-b border-primary-200 dark:border-primary-800 flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-bold text-surface-900 dark:text-white">Quote from {{ $quoteRequest->seller->store_name }}</h2>
                                    <p class="text-sm text-surface-600 dark:text-surface-400">Received {{ $quoteRequest->quote->created_at->diffForHumans() }}</p>
                                </div>
                                <x-status-badge :status="$quoteRequest->quote->status" />
                            </div>

                            <div class="p-6">
                                <!-- Price & Delivery -->
                                <div class="grid grid-cols-3 gap-4 mb-6">
                                    <div class="text-center p-4 rounded-lg bg-surface-50 dark:bg-surface-700/50">
                                        <p class="text-2xl font-bold text-surface-900 dark:text-white">${{ number_format($quoteRequest->quote->price, 2) }}</p>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">Total Price</p>
                                    </div>
                                    <div class="text-center p-4 rounded-lg bg-surface-50 dark:bg-surface-700/50">
                                        <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ $quoteRequest->quote->delivery_days }}</p>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">Days Delivery</p>
                                    </div>
                                    <div class="text-center p-4 rounded-lg bg-surface-50 dark:bg-surface-700/50">
                                        <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ $quoteRequest->quote->revisions ?: 'Unlimited' }}</p>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">Revisions</p>
                                    </div>
                                </div>

                                <!-- Description -->
                                @if($quoteRequest->quote->description)
                                    <div class="mb-6">
                                        <h3 class="text-sm font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider mb-2">What's Included</h3>
                                        <div class="prose prose-surface dark:prose-invert max-w-none">
                                            {!! nl2br(e($quoteRequest->quote->description)) !!}
                                        </div>
                                    </div>
                                @endif

                                <!-- Expiry -->
                                @if($quoteRequest->quote->expires_at)
                                    <p class="text-sm text-surface-500 dark:text-surface-400 mb-6">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        This quote expires on {{ $quoteRequest->quote->expires_at->format('F d, Y') }}
                                    </p>
                                @endif

                                <!-- Actions -->
                                @if($quoteRequest->quote->status === 'pending' && $quoteRequest->buyer_id === auth()->id())
                                    <div class="flex items-center gap-4">
                                        <form action="{{ route('quotes.accept', $quoteRequest) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit" class="w-full inline-flex items-center justify-center rounded-xl bg-green-600 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-green-500/30 hover:bg-green-700 transition-all">
                                                Accept & Pay ${{ number_format($quoteRequest->quote->price, 2) }}
                                            </button>
                                        </form>
                                        <form action="{{ route('quotes.reject', $quoteRequest) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center justify-center rounded-xl border-2 border-red-500 px-6 py-3 text-base font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                                                Decline
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24 space-y-6">
                        <!-- Seller Card -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <div class="flex items-center gap-4 mb-4">
                                <img src="{{ $quoteRequest->seller->logo_url }}" alt="{{ $quoteRequest->seller->store_name }}" class="w-14 h-14 rounded-full object-cover">
                                <div>
                                    <a href="{{ route('sellers.show', $quoteRequest->seller) }}" class="font-semibold text-surface-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">
                                        {{ $quoteRequest->seller->store_name }}
                                    </a>
                                    @if($quoteRequest->seller->level)
                                        <p class="text-sm text-surface-500 dark:text-surface-400">{{ ucfirst($quoteRequest->seller->level) }} Seller</p>
                                    @endif
                                </div>
                            </div>
                            @if($quoteRequest->conversation)
                                <a href="{{ route('conversations.show', $quoteRequest->conversation) }}"
                                    class="w-full inline-flex items-center justify-center rounded-lg border border-surface-200 dark:border-surface-700 px-4 py-2 text-sm font-medium text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    Message Seller
                                </a>
                            @endif
                        </div>

                        <!-- Quick Actions -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Quick Actions</h3>
                            <div class="space-y-2">
                                <a href="{{ route('services.show', $quoteRequest->service) }}"
                                    class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Original Service
                                </a>
                                <a href="{{ route('quotes.index') }}"
                                    class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    All Quote Requests
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
