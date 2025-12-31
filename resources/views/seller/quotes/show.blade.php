<x-layouts.app title="Quote Request - {{ $quoteRequest->title }}">
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('seller.quotes.index') }}" class="inline-flex items-center gap-2 text-surface-600 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Quote Requests
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">{{ $quoteRequest->title }}</h1>
                    <x-status-badge :status="$quoteRequest->status" />
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-success-50 dark:bg-success-900/30 border border-success-200 dark:border-success-800 rounded-lg text-success-700 dark:text-success-300">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Request Details -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Request Details</h2>
                        <div class="prose prose-sm dark:prose-invert max-w-none text-surface-600 dark:text-surface-400">
                            {!! nl2br(e($quoteRequest->description)) !!}
                        </div>

                        @if($quoteRequest->attachments && count($quoteRequest->attachments) > 0)
                            <div class="mt-6 pt-6 border-t border-surface-200 dark:border-surface-700">
                                <h3 class="text-sm font-medium text-surface-900 dark:text-white mb-3">Attachments</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($quoteRequest->attachments as $attachment)
                                        <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-200 dark:hover:bg-surface-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            {{ $attachment['name'] ?? 'File' }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Your Quote -->
                    @if($quoteRequest->quote)
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-semibold text-surface-900 dark:text-white">Your Quote</h2>
                                <x-status-badge :status="$quoteRequest->quote->status" size="sm" />
                            </div>

                            <div class="grid grid-cols-3 gap-4 mb-4">
                                <div class="text-center p-4 bg-surface-50 dark:bg-surface-900/50 rounded-lg">
                                    <p class="text-sm text-surface-500 dark:text-surface-400">Price</p>
                                    <p class="text-xl font-bold text-surface-900 dark:text-white">${{ number_format($quoteRequest->quote->price, 2) }}</p>
                                </div>
                                <div class="text-center p-4 bg-surface-50 dark:bg-surface-900/50 rounded-lg">
                                    <p class="text-sm text-surface-500 dark:text-surface-400">Delivery</p>
                                    <p class="text-xl font-bold text-surface-900 dark:text-white">{{ $quoteRequest->quote->delivery_days }} days</p>
                                </div>
                                <div class="text-center p-4 bg-surface-50 dark:bg-surface-900/50 rounded-lg">
                                    <p class="text-sm text-surface-500 dark:text-surface-400">Revisions</p>
                                    <p class="text-xl font-bold text-surface-900 dark:text-white">{{ $quoteRequest->quote->revisions }}</p>
                                </div>
                            </div>

                            <div class="prose prose-sm dark:prose-invert max-w-none text-surface-600 dark:text-surface-400">
                                {!! nl2br(e($quoteRequest->quote->description)) !!}
                            </div>

                            @if($quoteRequest->quote->expires_at)
                                <div class="mt-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                                    <p class="text-sm text-surface-500 dark:text-surface-400">
                                        @if($quoteRequest->quote->expires_at->isPast())
                                            <span class="text-danger-600 dark:text-danger-400">Expired on {{ $quoteRequest->quote->expires_at->format('M d, Y') }}</span>
                                        @else
                                            Expires on {{ $quoteRequest->quote->expires_at->format('M d, Y') }} ({{ $quoteRequest->quote->expires_at->diffForHumans() }})
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>
                    @elseif($quoteRequest->status === 'pending')
                        <!-- Quote Form -->
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Send Your Quote</h2>

                            <form action="{{ route('seller.quotes.submit', $quoteRequest) }}" method="POST" class="space-y-6">
                                @csrf

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label for="price" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Price ($) *</label>
                                        <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01" min="1" required class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 text-surface-900 dark:text-white" placeholder="0.00">
                                        @error('price')
                                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="delivery_days" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Delivery (days) *</label>
                                        <input type="number" id="delivery_days" name="delivery_days" value="{{ old('delivery_days') }}" min="1" required class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 text-surface-900 dark:text-white" placeholder="7">
                                        @error('delivery_days')
                                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="revisions" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Revisions *</label>
                                        <input type="number" id="revisions" name="revisions" value="{{ old('revisions', 2) }}" min="0" required class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 text-surface-900 dark:text-white" placeholder="2">
                                        @error('revisions')
                                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Description *</label>
                                    <textarea id="description" name="description" rows="4" required class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 text-surface-900 dark:text-white placeholder-surface-400" placeholder="Describe what you'll deliver and any important details...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="expires_at" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Quote Valid Until</label>
                                    <input type="date" id="expires_at" name="expires_at" value="{{ old('expires_at', now()->addDays(7)->format('Y-m-d')) }}" min="{{ now()->addDay()->format('Y-m-d') }}" class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 text-surface-900 dark:text-white">
                                    @error('expires_at')
                                        <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex justify-end gap-4">
                                    <a href="{{ route('seller.quotes.index') }}" class="px-6 py-2.5 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">Cancel</a>
                                    <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">Send Quote</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Buyer Info -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Buyer</h3>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                <span class="text-lg font-medium text-primary-700 dark:text-primary-300">{{ strtoupper(substr($quoteRequest->buyer->name ?? 'U', 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-surface-900 dark:text-white">{{ $quoteRequest->buyer->name ?? 'Unknown' }}</p>
                                <p class="text-sm text-surface-500 dark:text-surface-400">Member since {{ $quoteRequest->buyer?->created_at?->format('M Y') ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @if($quoteRequest->conversation)
                            <a href="{{ route('conversations.show', $quoteRequest->conversation) }}" class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-2 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Message Buyer
                            </a>
                        @endif
                    </div>

                    <!-- Budget Range -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Budget Range</h3>
                        <p class="text-2xl font-bold text-surface-900 dark:text-white">${{ number_format($quoteRequest->budget_min) }} - ${{ number_format($quoteRequest->budget_max) }}</p>
                    </div>

                    <!-- Related Service -->
                    @if($quoteRequest->service)
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Related Service</h3>
                            <div class="flex items-center gap-3">
                                @if($quoteRequest->service->thumbnail)
                                    <img src="{{ Storage::url($quoteRequest->service->thumbnail) }}" alt="" class="w-12 h-10 object-cover rounded-lg">
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-surface-900 dark:text-white truncate">{{ $quoteRequest->service->name }}</p>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">Starting at ${{ number_format($quoteRequest->service->packages->min('price') ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Request Date -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Request Info</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Requested</span>
                                <span class="text-surface-900 dark:text-white">{{ $quoteRequest->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Status</span>
                                <x-status-badge :status="$quoteRequest->status" size="sm" />
                            </div>
                        </div>
                    </div>

                    <!-- Decline Option -->
                    @if($quoteRequest->status === 'pending' && !$quoteRequest->quote)
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Not Interested?</h3>
                            <form action="{{ route('seller.quotes.decline', $quoteRequest) }}" method="POST" onsubmit="return confirm('Are you sure you want to decline this request?')">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 border border-danger-300 dark:border-danger-700 text-danger-600 dark:text-danger-400 rounded-lg hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors">Decline Request</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
