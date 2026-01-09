<x-layouts.app title="Edit Proposal">
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('seller.proposals.show', $proposal) }}" class="inline-flex items-center gap-2 text-surface-600 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Proposal
                </a>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Edit Proposal</h1>
                <p class="text-surface-600 dark:text-surface-400 mt-1">for "{{ $proposal->jobPosting->title ?? 'Job Deleted' }}"</p>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 bg-danger-50 dark:bg-danger-900/30 border border-danger-200 dark:border-danger-800 rounded-lg text-danger-700 dark:text-danger-300">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('seller.proposals.update', $proposal) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Cover Letter -->
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                    <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Cover Letter</h2>
                    <div>
                        <textarea
                            id="cover_letter"
                            name="cover_letter"
                            rows="8"
                            class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                            required
                        >{{ old('cover_letter', $proposal->cover_letter) }}</textarea>
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
                                value="{{ old('proposed_price', $proposal->proposed_price) }}"
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
                                value="{{ old('proposed_duration', $proposal->proposed_duration) }}"
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
                                <option value="days" {{ old('duration_type', $proposal->duration_type) === 'days' ? 'selected' : '' }}>Days</option>
                                <option value="weeks" {{ old('duration_type', $proposal->duration_type) === 'weeks' ? 'selected' : '' }}>Weeks</option>
                                <option value="months" {{ old('duration_type', $proposal->duration_type) === 'months' ? 'selected' : '' }}>Months</option>
                            </select>
                            @error('duration_type')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Milestones (Optional) -->
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6" x-data="{ milestones: {{ json_encode(old('milestones', $proposal->milestones ?? [])) }} }">
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
                                <div class="datepicker-wrapper">
                                    <input
                                        type="text"
                                        :name="'milestones[' + index + '][due_date]'"
                                        x-ref="datepicker"
                                        x-init="$nextTick(() => {
                                            const fp = flatpickr($refs.datepicker, {
                                                dateFormat: 'Y-m-d',
                                                altInput: true,
                                                altFormat: 'F j, Y',
                                                minDate: 'today',
                                                disableMobile: true,
                                                defaultDate: milestone.due_date || null,
                                                onChange: (dates, dateStr) => { milestone.due_date = dateStr; }
                                            });
                                            if (document.documentElement.classList.contains('dark')) {
                                                fp.calendarContainer?.classList.add('dark-theme');
                                            }
                                        })"
                                        readonly
                                        placeholder="Due date"
                                        class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white text-sm cursor-pointer"
                                    >
                                    <svg class="datepicker-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </template>

                    <p x-show="milestones.length === 0" class="text-sm text-surface-500 dark:text-surface-400">
                        Break down your project into milestones for easier tracking and payments.
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('seller.proposals.show', $proposal) }}" class="px-6 py-3 text-surface-700 dark:text-surface-300 hover:text-surface-900 dark:hover:text-white font-medium">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                        Update Proposal
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
