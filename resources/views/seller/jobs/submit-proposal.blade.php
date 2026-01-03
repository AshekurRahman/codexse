<x-layouts.app title="Submit Proposal - {{ $jobPosting->title }}">
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('seller.jobs.available') }}" class="inline-flex items-center gap-2 text-surface-600 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Available Jobs
                </a>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Submit Proposal</h1>
                <p class="text-surface-600 dark:text-surface-400 mt-1">for "{{ $jobPosting->title }}"</p>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 bg-danger-50 dark:bg-danger-900/30 border border-danger-200 dark:border-danger-800 rounded-lg text-danger-700 dark:text-danger-300">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Proposal Form -->
                <div class="lg:col-span-2">
                    <form action="{{ route('seller.proposals.store', $jobPosting) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Cover Letter -->
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Cover Letter</h2>
                            <div>
                                <label for="cover_letter" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                                    Introduce yourself and explain why you're the best fit for this job
                                </label>
                                <textarea
                                    id="cover_letter"
                                    name="cover_letter"
                                    rows="8"
                                    class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                    placeholder="Describe your relevant experience, approach to the project, and why the client should choose you..."
                                    required
                                >{{ old('cover_letter') }}</textarea>
                                @error('cover_letter')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Pricing & Timeline -->
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Pricing & Timeline</h2>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="proposed_price" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                                        Your Price ($)
                                    </label>
                                    <input
                                        type="number"
                                        id="proposed_price"
                                        name="proposed_price"
                                        min="5"
                                        step="0.01"
                                        value="{{ old('proposed_price', $jobPosting->budget_max ?? $jobPosting->budget_min) }}"
                                        class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                        required
                                    >
                                    @error('proposed_price')
                                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="proposed_duration" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                                        Duration
                                    </label>
                                    <input
                                        type="number"
                                        id="proposed_duration"
                                        name="proposed_duration"
                                        min="1"
                                        value="{{ old('proposed_duration', 7) }}"
                                        class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                        required
                                    >
                                    @error('proposed_duration')
                                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="duration_type" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                                        Duration Type
                                    </label>
                                    <select
                                        id="duration_type"
                                        name="duration_type"
                                        class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                        required
                                    >
                                        <option value="days" {{ old('duration_type') === 'days' ? 'selected' : '' }}>Days</option>
                                        <option value="weeks" {{ old('duration_type', 'weeks') === 'weeks' ? 'selected' : '' }}>Weeks</option>
                                        <option value="months" {{ old('duration_type') === 'months' ? 'selected' : '' }}>Months</option>
                                    </select>
                                    @error('duration_type')
                                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            @if($jobPosting->budget_min || $jobPosting->budget_max)
                                <p class="mt-3 text-sm text-surface-500 dark:text-surface-400">
                                    Client's budget:
                                    @if($jobPosting->budget_min && $jobPosting->budget_max)
                                        {{ format_price($jobPosting->budget_min) }} - {{ format_price($jobPosting->budget_max) }}
                                    @elseif($jobPosting->budget_min)
                                        From {{ format_price($jobPosting->budget_min) }}
                                    @else
                                        Up to {{ format_price($jobPosting->budget_max) }}
                                    @endif
                                </p>
                            @endif
                        </div>

                        <!-- Milestones (Optional) -->
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6" x-data="{ milestones: [] }">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-semibold text-surface-900 dark:text-white">Milestones (Optional)</h2>
                                <button type="button" @click="milestones.push({ title: '', description: '', amount: '', due_date: '' })" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">
                                    + Add Milestone
                                </button>
                            </div>

                            <template x-for="(milestone, index) in milestones" :key="index">
                                <div class="border border-surface-200 dark:border-surface-700 rounded-lg p-4 mb-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Milestone <span x-text="index + 1"></span></span>
                                        <button type="button" @click="milestones.splice(index, 1)" class="text-danger-600 hover:text-danger-700 text-sm">Remove</button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <input
                                                type="text"
                                                :name="'milestones[' + index + '][title]'"
                                                x-model="milestone.title"
                                                placeholder="Milestone title"
                                                class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white text-sm"
                                                required
                                            >
                                        </div>
                                        <div>
                                            <input
                                                type="number"
                                                :name="'milestones[' + index + '][amount]'"
                                                x-model="milestone.amount"
                                                placeholder="Amount ($)"
                                                min="1"
                                                step="0.01"
                                                class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white text-sm"
                                                required
                                            >
                                        </div>
                                        <div class="md:col-span-2">
                                            <textarea
                                                :name="'milestones[' + index + '][description]'"
                                                x-model="milestone.description"
                                                placeholder="Description (optional)"
                                                rows="2"
                                                class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white text-sm"
                                            ></textarea>
                                        </div>
                                        <div>
                                            <input
                                                type="date"
                                                :name="'milestones[' + index + '][due_date]'"
                                                x-model="milestone.due_date"
                                                class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white text-sm"
                                            >
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <p x-show="milestones.length === 0" class="text-sm text-surface-500 dark:text-surface-400">
                                Break down your project into milestones for easier tracking and payments.
                            </p>
                        </div>

                        <!-- Attachments -->
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Attachments (Optional)</h2>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                                    Upload relevant files (portfolio, samples, etc.)
                                </label>
                                <input
                                    type="file"
                                    name="attachments[]"
                                    multiple
                                    accept=".pdf,.doc,.docx,.png,.jpg,.jpeg,.zip"
                                    class="w-full text-sm text-surface-600 dark:text-surface-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900/30 dark:file:text-primary-400"
                                >
                                <p class="mt-2 text-xs text-surface-500 dark:text-surface-400">
                                    Accepted formats: PDF, DOC, DOCX, PNG, JPG, ZIP. Max 10MB per file.
                                </p>
                                @error('attachments.*')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('seller.jobs.available') }}" class="px-6 py-3 text-surface-700 dark:text-surface-300 hover:text-surface-900 dark:hover:text-white font-medium">
                                Cancel
                            </a>
                            <button type="submit" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                                Submit Proposal
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Job Summary Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 sticky top-8">
                        <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Job Summary</h3>

                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-surface-500 dark:text-surface-400">Posted by</p>
                                <p class="font-medium text-surface-900 dark:text-white">{{ $jobPosting->client->name ?? 'Unknown' }}</p>
                            </div>

                            @if($jobPosting->category)
                                <div>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">Category</p>
                                    <p class="font-medium text-surface-900 dark:text-white">{{ $jobPosting->category->name }}</p>
                                </div>
                            @endif

                            <div>
                                <p class="text-sm text-surface-500 dark:text-surface-400">Budget</p>
                                <p class="font-medium text-surface-900 dark:text-white">
                                    @if($jobPosting->budget_min && $jobPosting->budget_max)
                                        {{ format_price($jobPosting->budget_min) }} - {{ format_price($jobPosting->budget_max) }}
                                    @elseif($jobPosting->budget_min)
                                        From {{ format_price($jobPosting->budget_min) }}
                                    @elseif($jobPosting->budget_max)
                                        Up to {{ format_price($jobPosting->budget_max) }}
                                    @else
                                        Not specified
                                    @endif
                                </p>
                            </div>

                            @if($jobPosting->duration)
                                <div>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">Expected Duration</p>
                                    <p class="font-medium text-surface-900 dark:text-white">{{ $jobPosting->duration }}</p>
                                </div>
                            @endif

                            <div>
                                <p class="text-sm text-surface-500 dark:text-surface-400">Posted</p>
                                <p class="font-medium text-surface-900 dark:text-white">{{ $jobPosting->created_at->diffForHumans() }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-surface-500 dark:text-surface-400">Proposals</p>
                                <p class="font-medium text-surface-900 dark:text-white">{{ $jobPosting->proposals_count ?? 0 }} submitted</p>
                            </div>
                        </div>

                        @if($jobPosting->skills && count($jobPosting->skills) > 0)
                            <div class="mt-6 pt-6 border-t border-surface-200 dark:border-surface-700">
                                <p class="text-sm text-surface-500 dark:text-surface-400 mb-3">Required Skills</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($jobPosting->skills as $skill)
                                        <span class="px-2 py-1 bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-300 rounded text-xs">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
