<x-layouts.app title="New Message - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <a href="{{ route('conversations.index') }}" class="inline-flex items-center text-sm text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Messages
                </a>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">New Message</h1>
                <p class="mt-1 text-surface-600 dark:text-surface-400">Start a conversation with a seller</p>
            </div>

            <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                <form action="{{ route('conversations.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf

                    @if($seller)
                        <input type="hidden" name="seller_id" value="{{ $seller->id }}">
                        <div class="flex items-center gap-4 p-4 rounded-lg bg-surface-50 dark:bg-surface-700">
                            <img src="{{ $seller->user->avatar_url }}" alt="{{ $seller->store_name }}" class="h-12 w-12 rounded-full object-cover">
                            <div>
                                <p class="font-medium text-surface-900 dark:text-white">{{ $seller->store_name }}</p>
                                <p class="text-sm text-surface-500 dark:text-surface-400">Seller</p>
                            </div>
                        </div>
                    @else
                        <div>
                            <label for="seller_id" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Select Seller</label>
                            <select name="seller_id" id="seller_id" required class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                                <option value="">Choose a seller...</option>
                            </select>
                            @error('seller_id')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    @if($product)
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="flex items-center gap-4 p-4 rounded-lg bg-surface-50 dark:bg-surface-700">
                            <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="h-12 w-12 rounded-lg object-cover">
                            <div>
                                <p class="font-medium text-surface-900 dark:text-white">{{ $product->name }}</p>
                                <p class="text-sm text-surface-500 dark:text-surface-400">Product</p>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label for="subject" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Subject</label>
                        <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-primary-500" placeholder="What is this about?">
                        @error('subject')
                            <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Message</label>
                        <textarea name="message" id="message" rows="6" required class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-primary-500" placeholder="Write your message...">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
