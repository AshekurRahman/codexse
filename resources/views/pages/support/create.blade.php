<x-layouts.app title="New Support Ticket - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <a href="{{ route('support.index') }}" class="inline-flex items-center text-sm text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Tickets
                </a>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Create Support Ticket</h1>
                <p class="mt-1 text-surface-600 dark:text-surface-400">Describe your issue and we'll get back to you</p>
            </div>

            <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                <form action="{{ route('support.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf

                    <div>
                        <label for="subject" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Subject</label>
                        <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-primary-500" placeholder="Brief description of your issue">
                        @error('subject')
                            <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="category" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Category</label>
                            <select name="category" id="category" required class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                                <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>General Inquiry</option>
                                <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>Technical Issue</option>
                                <option value="billing" {{ old('category') === 'billing' ? 'selected' : '' }}>Billing Question</option>
                                <option value="refund" {{ old('category') === 'refund' ? 'selected' : '' }}>Refund Request</option>
                                <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="priority" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Priority</label>
                            <select name="priority" id="priority" required class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                            </select>
                            @error('priority')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    @if($orders->count() > 0)
                        <div>
                            <label for="order_id" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Related Order (Optional)</label>
                            <select name="order_id" id="order_id" class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                                <option value="">Select an order...</option>
                                @foreach($orders as $order)
                                    <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>
                                        {{ $order->order_number }} - ${{ number_format($order->total, 2) }} ({{ $order->created_at->format('M d, Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label for="description" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Description</label>
                        <textarea name="description" id="description" rows="6" required class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-primary-500" placeholder="Please describe your issue in detail...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                            Submit Ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
