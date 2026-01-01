<x-layouts.app title="Open a Dispute">
    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ $disputableType === 'contract' ? route('contracts.show', $disputable) : route('service-orders.show', $disputable) }}" class="inline-flex items-center gap-2 text-surface-600 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </a>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Open a Dispute</h1>
                <p class="text-surface-600 dark:text-surface-400 mt-1">Submit a dispute for resolution by our team</p>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 bg-danger-50 dark:bg-danger-900/30 border border-danger-200 dark:border-danger-800 rounded-lg text-danger-700 dark:text-danger-300">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Context Info -->
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 mb-6">
                <h3 class="font-medium text-surface-900 dark:text-white mb-4">
                    @if($disputableType === 'contract')
                        Contract Details
                    @else
                        Service Order Details
                    @endif
                </h3>

                <div class="flex items-start gap-4">
                    @if($disputableType === 'contract')
                        <div class="flex-1">
                            <p class="font-semibold text-surface-900 dark:text-white">{{ $disputable->title ?? 'Contract' }}</p>
                            <p class="text-sm text-surface-500 dark:text-surface-400">
                                With: {{ $disputable->client_id === auth()->id() ? ($disputable->freelancer->name ?? 'Freelancer') : ($disputable->client->name ?? 'Client') }}
                            </p>
                            <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">
                                Budget: {{ format_price($disputable->total_amount ?? 0) }}
                            </p>
                        </div>
                    @else
                        @if($disputable->service?->thumbnail)
                            <img src="{{ Storage::url($disputable->service->thumbnail) }}" alt="" class="w-16 h-12 object-cover rounded-lg">
                        @endif
                        <div class="flex-1">
                            <p class="font-semibold text-surface-900 dark:text-white">{{ $disputable->service->name ?? 'Service' }}</p>
                            <p class="text-sm text-surface-500 dark:text-surface-400">
                                Order #{{ $disputable->order_number ?? $disputable->id }}
                            </p>
                            <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">
                                Amount: {{ format_price($disputable->price ?? 0) }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Dispute Form -->
            <form action="{{ route('disputes.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="disputable_type" value="{{ $disputableType }}">
                <input type="hidden" name="disputable_id" value="{{ $disputable->id }}">

                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 space-y-6">
                    <h2 class="text-lg font-semibold text-surface-900 dark:text-white pb-4 border-b border-surface-200 dark:border-surface-700">Dispute Details</h2>

                    <!-- Reason -->
                    <div>
                        <label for="reason" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Reason for Dispute *</label>
                        <select id="reason" name="reason" required class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white">
                            <option value="">Select a reason</option>
                            @foreach($reasons as $key => $label)
                                <option value="{{ $key }}" {{ old('reason') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('reason')
                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Detailed Description *</label>
                        <textarea id="description" name="description" rows="6" required minlength="50" class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white placeholder-surface-400" placeholder="Please describe the issue in detail. Include what happened, what you expected, and any attempts made to resolve it directly (minimum 50 characters)...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">Minimum 50 characters required</p>
                    </div>

                    <!-- Evidence -->
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Supporting Evidence (Optional)</label>
                        <div class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-lg p-6 text-center" x-data="{ files: [] }">
                            <input type="file" name="evidence[]" multiple id="evidence" class="hidden" @change="files = Array.from($event.target.files)">
                            <label for="evidence" class="cursor-pointer">
                                <svg class="w-10 h-10 text-surface-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="text-surface-600 dark:text-surface-400 mb-1">Upload screenshots, files, or other evidence</p>
                                <p class="text-sm text-surface-400">Max 10MB per file</p>
                            </label>
                            <template x-if="files.length > 0">
                                <div class="mt-4 space-y-2">
                                    <template x-for="file in files" :key="file.name">
                                        <div class="flex items-center justify-between p-2 bg-surface-50 dark:bg-surface-900/50 rounded-lg">
                                            <span class="text-sm text-surface-700 dark:text-surface-300" x-text="file.name"></span>
                                            <span class="text-xs text-surface-500" x-text="(file.size / 1024 / 1024).toFixed(2) + ' MB'"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                        @error('evidence.*')
                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Warning -->
                    <div class="p-4 bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-lg">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-warning-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div class="text-sm">
                                <p class="font-medium text-warning-700 dark:text-warning-300">Before opening a dispute</p>
                                <ul class="mt-2 text-warning-600 dark:text-warning-400 space-y-1 list-disc list-inside">
                                    <li>Try to resolve the issue directly with the other party first</li>
                                    <li>Disputes are reviewed by our team and may take 3-5 business days</li>
                                    <li>Provide accurate information and evidence to help us make a fair decision</li>
                                    <li>False disputes may result in account restrictions</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <a href="{{ $disputableType === 'contract' ? route('contracts.show', $disputable) : route('service-orders.show', $disputable) }}" class="px-6 py-2.5 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-danger-600 hover:bg-danger-700 text-white font-medium rounded-lg transition-colors inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Submit Dispute
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
