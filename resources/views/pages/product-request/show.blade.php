<x-layouts.app title="{{ $productRequest->product_title }} - Product Request - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <!-- Back Link -->
            <a href="{{ route('product-request.index') }}" class="inline-flex items-center text-sm text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white mb-6">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to My Requests
            </a>

            <!-- Header Card -->
            <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">{{ $productRequest->product_title }}</h1>
                        <p class="mt-1 text-surface-600 dark:text-surface-400">
                            Submitted on {{ $productRequest->created_at->format('F d, Y \a\t g:i A') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($productRequest->status === 'fulfilled') bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-400
                            @elseif($productRequest->status === 'approved') bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400
                            @elseif($productRequest->status === 'reviewing') bg-info-100 text-info-800 dark:bg-info-900/30 dark:text-info-400
                            @elseif($productRequest->status === 'rejected') bg-danger-100 text-danger-800 dark:bg-danger-900/30 dark:text-danger-400
                            @elseif($productRequest->status === 'closed') bg-surface-100 text-surface-800 dark:bg-surface-700 dark:text-surface-400
                            @else bg-warning-100 text-warning-800 dark:bg-warning-900/30 dark:text-warning-400
                            @endif
                        ">
                            {{ ucfirst($productRequest->status) }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($productRequest->urgency === 'urgent') bg-danger-100 text-danger-800 dark:bg-danger-900/30 dark:text-danger-400
                            @elseif($productRequest->urgency === 'high') bg-warning-100 text-warning-800 dark:bg-warning-900/30 dark:text-warning-400
                            @elseif($productRequest->urgency === 'normal') bg-info-100 text-info-800 dark:bg-info-900/30 dark:text-info-400
                            @else bg-surface-100 text-surface-800 dark:bg-surface-700 dark:text-surface-400
                            @endif
                        ">
                            {{ ucfirst($productRequest->urgency) }} Priority
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Description -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Description</h2>
                        <div class="prose dark:prose-invert max-w-none text-surface-600 dark:text-surface-400">
                            {!! nl2br(e($productRequest->description)) !!}
                        </div>
                    </div>

                    <!-- Features -->
                    @if($productRequest->features)
                        <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Required Features</h2>
                            <div class="prose dark:prose-invert max-w-none text-surface-600 dark:text-surface-400">
                                {!! nl2br(e($productRequest->features)) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Reference URLs -->
                    @if($productRequest->reference_urls)
                        <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Reference URLs</h2>
                            <div class="space-y-2">
                                @foreach(explode("\n", $productRequest->reference_urls) as $url)
                                    @if(trim($url))
                                        <a href="{{ trim($url) }}" target="_blank" rel="noopener noreferrer" class="flex items-center text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            {{ trim($url) }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Attachments -->
                    @if($productRequest->attachments && count($productRequest->attachments) > 0)
                        <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Attachments</h2>
                            <div class="space-y-2">
                                @foreach($productRequest->attachments as $attachment)
                                    <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" class="flex items-center p-3 rounded-lg border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors">
                                        <svg class="w-5 h-5 text-surface-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-surface-900 dark:text-white">{{ $attachment['name'] }}</p>
                                            <p class="text-xs text-surface-500">{{ number_format($attachment['size'] / 1024, 2) }} KB</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Fulfilled Product -->
                    @if($productRequest->fulfilledByProduct)
                        <div class="bg-success-50 dark:bg-success-900/20 rounded-xl border border-success-200 dark:border-success-800 p-6">
                            <h2 class="text-lg font-semibold text-success-800 dark:text-success-400 mb-4">Fulfilled by Product</h2>
                            <a href="{{ route('products.show', $productRequest->fulfilledByProduct) }}" class="flex items-center gap-4 p-4 bg-white dark:bg-surface-800 rounded-lg hover:shadow-md transition-shadow">
                                <img src="{{ $productRequest->fulfilledByProduct->thumbnail_url }}" alt="{{ $productRequest->fulfilledByProduct->name }}" class="w-16 h-16 rounded-lg object-cover">
                                <div class="flex-1">
                                    <h3 class="font-medium text-surface-900 dark:text-white">{{ $productRequest->fulfilledByProduct->name }}</h3>
                                    <p class="text-sm text-surface-500">${{ number_format($productRequest->fulfilledByProduct->price, 2) }}</p>
                                </div>
                                <svg class="w-5 h-5 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Details -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Details</h2>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm text-surface-500 dark:text-surface-400">Category</dt>
                                <dd class="mt-1 text-sm font-medium text-surface-900 dark:text-white">
                                    {{ $productRequest->category?->name ?? 'Not specified' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm text-surface-500 dark:text-surface-400">Budget Range</dt>
                                <dd class="mt-1 text-sm font-medium text-surface-900 dark:text-white">
                                    {{ $productRequest->budget_range ?? 'Not specified' }}
                                </dd>
                            </div>
                            @if($productRequest->reviewed_at)
                                <div>
                                    <dt class="text-sm text-surface-500 dark:text-surface-400">Reviewed At</dt>
                                    <dd class="mt-1 text-sm font-medium text-surface-900 dark:text-white">
                                        {{ $productRequest->reviewed_at->format('M d, Y') }}
                                    </dd>
                                </div>
                            @endif
                            @if($productRequest->fulfilled_at)
                                <div>
                                    <dt class="text-sm text-surface-500 dark:text-surface-400">Fulfilled At</dt>
                                    <dd class="mt-1 text-sm font-medium text-surface-900 dark:text-white">
                                        {{ $productRequest->fulfilled_at->format('M d, Y') }}
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Contact Info -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Contact Info</h2>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm text-surface-500 dark:text-surface-400">Name</dt>
                                <dd class="mt-1 text-sm font-medium text-surface-900 dark:text-white">{{ $productRequest->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-surface-500 dark:text-surface-400">Email</dt>
                                <dd class="mt-1 text-sm font-medium text-surface-900 dark:text-white">{{ $productRequest->email }}</dd>
                            </div>
                            @if($productRequest->phone)
                                <div>
                                    <dt class="text-sm text-surface-500 dark:text-surface-400">Phone</dt>
                                    <dd class="mt-1 text-sm font-medium text-surface-900 dark:text-white">{{ $productRequest->phone }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
