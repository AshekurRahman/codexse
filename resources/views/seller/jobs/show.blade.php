<x-layouts.app title="{{ $job->title }}">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('seller.jobs.available') }}" class="inline-flex items-center gap-2 text-surface-600 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Jobs
                </a>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-success-50 dark:bg-success-900/30 border border-success-200 dark:border-success-800 rounded-lg text-success-700 dark:text-success-300">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Job Details -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <div>
                                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">{{ $job->title }}</h1>
                                <p class="text-surface-500 dark:text-surface-400 mt-1">Posted {{ $job->created_at->diffForHumans() }} by {{ $job->client->name ?? 'Unknown' }}</p>
                            </div>
                            @if($job->is_urgent)
                                <span class="px-3 py-1 text-sm font-medium bg-danger-100 text-danger-700 dark:bg-danger-900/30 dark:text-danger-300 rounded-lg">Urgent</span>
                            @endif
                        </div>

                        <div class="prose prose-sm dark:prose-invert max-w-none text-surface-600 dark:text-surface-400">
                            {!! nl2br(e($job->description)) !!}
                        </div>

                        @if($job->skills && count($job->skills) > 0)
                            <div class="mt-6 pt-6 border-t border-surface-200 dark:border-surface-700">
                                <h3 class="text-sm font-medium text-surface-900 dark:text-white mb-3">Required Skills</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($job->skills as $skill)
                                        <span class="px-3 py-1 bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-300 rounded-lg text-sm">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($job->attachments && count($job->attachments) > 0)
                            <div class="mt-6 pt-6 border-t border-surface-200 dark:border-surface-700">
                                <h3 class="text-sm font-medium text-surface-900 dark:text-white mb-3">Attachments</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($job->attachments as $attachment)
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

                    <!-- Proposal Form -->
                    @php
                        $existingProposal = $job->proposals->where('seller_id', auth()->user()->seller?->id)->first();
                    @endphp

                    @if($existingProposal)
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-success-200 dark:border-success-800 p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <svg class="w-8 h-8 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h2 class="text-lg font-semibold text-surface-900 dark:text-white">Proposal Submitted</h2>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">You've already submitted a proposal for this job</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="p-4 bg-surface-50 dark:bg-surface-900/50 rounded-lg">
                                    <p class="text-sm text-surface-500 dark:text-surface-400">Your Bid</p>
                                    <p class="text-xl font-bold text-surface-900 dark:text-white">{{ format_price($existingProposal->proposed_price) }}</p>
                                </div>
                                <div class="p-4 bg-surface-50 dark:bg-surface-900/50 rounded-lg">
                                    <p class="text-sm text-surface-500 dark:text-surface-400">Duration</p>
                                    <p class="text-xl font-bold text-surface-900 dark:text-white">{{ $existingProposal->proposed_duration }} days</p>
                                </div>
                            </div>

                            <x-status-badge :status="$existingProposal->status" />

                            @if($existingProposal->status === 'pending')
                                <form action="{{ route('seller.proposals.withdraw', $existingProposal) }}" method="POST" class="mt-4" onsubmit="return confirm('Are you sure you want to withdraw this proposal?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-danger-600 hover:text-danger-700 dark:text-danger-400 dark:hover:text-danger-300">Withdraw Proposal</button>
                                </form>
                            @endif
                        </div>
                    @elseif($job->status === 'open')
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Submit Your Proposal</h2>

                            <form action="{{ route('seller.proposals.store', $job) }}" method="POST" class="space-y-6">
                                @csrf

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="proposed_price" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Your Bid ($) *</label>
                                        <input type="number" id="proposed_price" name="proposed_price" value="{{ old('proposed_price') }}" step="0.01" min="1" required class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 text-surface-900 dark:text-white">
                                        <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">Client budget: {{ format_price($job->budget_min) }} - {{ format_price($job->budget_max) }}</p>
                                        @error('proposed_price')
                                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="proposed_duration" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Delivery Time (days) *</label>
                                        <input type="number" id="proposed_duration" name="proposed_duration" value="{{ old('proposed_duration') }}" min="1" required class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 text-surface-900 dark:text-white">
                                        @error('proposed_duration')
                                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="cover_letter" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Cover Letter *</label>
                                    <textarea id="cover_letter" name="cover_letter" rows="6" required class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 text-surface-900 dark:text-white placeholder-surface-400" placeholder="Explain why you're the best fit for this job...">{{ old('cover_letter') }}</textarea>
                                    @error('cover_letter')
                                        <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="p-4 bg-info-50 dark:bg-info-900/20 border border-info-200 dark:border-info-800 rounded-lg">
                                    <div class="flex gap-3">
                                        <svg class="w-5 h-5 text-info-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <div class="text-sm text-info-700 dark:text-info-300">
                                            <p class="font-medium">Tips for a great proposal:</p>
                                            <ul class="mt-1 list-disc list-inside text-info-600 dark:text-info-400">
                                                <li>Show you understand the project requirements</li>
                                                <li>Highlight relevant experience</li>
                                                <li>Be specific about your approach</li>
                                                <li>Set realistic expectations</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">Submit Proposal</button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 text-center">
                            <svg class="w-12 h-12 text-surface-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">This job is no longer accepting proposals</h3>
                            <p class="text-surface-500 dark:text-surface-400">The client has closed this job or it has been filled.</p>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Budget & Timeline -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Job Details</h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-surface-500 dark:text-surface-400">Budget</p>
                                <p class="text-xl font-bold text-surface-900 dark:text-white">{{ format_price($job->budget_min) }} - {{ format_price($job->budget_max) }}</p>
                                <p class="text-sm text-surface-500 dark:text-surface-400">{{ ucfirst($job->budget_type) }} price</p>
                            </div>
                            @if($job->deadline)
                                <div>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">Deadline</p>
                                    <p class="font-medium text-surface-900 dark:text-white">{{ $job->deadline->format('M d, Y') }}</p>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm text-surface-500 dark:text-surface-400">Proposals</p>
                                <p class="font-medium text-surface-900 dark:text-white">{{ $job->proposals_count ?? 0 }} received</p>
                            </div>
                            <div>
                                <p class="text-sm text-surface-500 dark:text-surface-400">Category</p>
                                <p class="font-medium text-surface-900 dark:text-white">{{ $job->category->name ?? 'Uncategorized' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Client Info -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="font-semibold text-surface-900 dark:text-white mb-4">About the Client</h3>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                <span class="text-lg font-medium text-primary-700 dark:text-primary-300">{{ strtoupper(substr($job->client->name ?? 'U', 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-surface-900 dark:text-white">{{ $job->client->name ?? 'Unknown' }}</p>
                                <p class="text-sm text-surface-500 dark:text-surface-400">Member since {{ $job->client?->created_at?->format('M Y') ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-surface-500 dark:text-surface-400">Jobs Posted</span>
                                <span class="text-surface-900 dark:text-white">{{ $job->client?->jobPostings?->count() ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-surface-500 dark:text-surface-400">Hire Rate</span>
                                <span class="text-surface-900 dark:text-white">{{ $job->client?->hire_rate ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
