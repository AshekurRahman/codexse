<x-layouts.app title="Submit Milestone - {{ $milestone->title }}">
    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('seller.contracts.show', $contract) }}" class="inline-flex items-center gap-2 text-surface-600 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Contract
                </a>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Submit Milestone Work</h1>
                <p class="text-surface-600 dark:text-surface-400 mt-1">Submit your completed work for client review</p>
            </div>

            <!-- Milestone Summary -->
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 mb-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-semibold text-surface-900 dark:text-white">{{ $milestone->title }}</h3>
                        <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">{{ $contract->jobPosting->title ?? 'Contract' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ format_price($milestone->amount) }}</p>
                        @if($milestone->due_date)
                            <p class="text-sm text-surface-500 dark:text-surface-400">Due: {{ $milestone->due_date->format('M d, Y') }}</p>
                        @endif
                    </div>
                </div>
                @if($milestone->description)
                    <div class="mt-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                        <p class="text-sm text-surface-600 dark:text-surface-400">{{ $milestone->description }}</p>
                    </div>
                @endif
            </div>

            <!-- Submission Form -->
            <form action="{{ route('seller.milestones.submit', $milestone) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 space-y-6">
                    <h2 class="text-lg font-semibold text-surface-900 dark:text-white pb-4 border-b border-surface-200 dark:border-surface-700">Submission Details</h2>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Work Notes *</label>
                        <textarea id="notes" name="notes" rows="6" required class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white placeholder-surface-400" placeholder="Describe the work you've completed, any important notes for the client, and how to use/access the deliverables...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Files -->
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Deliverable Files (Optional)</label>
                        <div class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-lg p-6 text-center" x-data="{ files: [] }">
                            <input type="file" name="files[]" multiple id="files" class="hidden" @change="files = Array.from($event.target.files)">
                            <label for="files" class="cursor-pointer">
                                <svg class="w-10 h-10 text-surface-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="text-surface-600 dark:text-surface-400 mb-1">Drag and drop files here, or click to browse</p>
                                <p class="text-sm text-surface-400">ZIP, PDF, images, and other files up to 50MB each</p>
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
                        @error('files.*')
                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Info Box -->
                    <div class="p-4 bg-info-50 dark:bg-info-900/20 border border-info-200 dark:border-info-800 rounded-lg">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-info-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm">
                                <p class="font-medium text-info-700 dark:text-info-300">What happens next?</p>
                                <ul class="mt-2 text-info-600 dark:text-info-400 space-y-1">
                                    <li>The client will be notified of your submission</li>
                                    <li>They can approve or request revisions</li>
                                    <li>Once approved, funds will be released from escrow</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('seller.contracts.show', $contract) }}" class="px-6 py-2.5 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Submit for Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
