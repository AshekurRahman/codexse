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

            <div x-data="proposalForm()" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Proposal Form -->
                <div class="lg:col-span-2">
                    <!-- Success Message -->
                    <div x-show="successMessage" x-cloak class="mb-6 p-4 bg-success-50 dark:bg-success-900/30 border border-success-200 dark:border-success-800 rounded-lg text-success-700 dark:text-success-300">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span x-text="successMessage"></span>
                        </div>
                        <p class="mt-2 text-sm">Redirecting to your proposal...</p>
                    </div>

                    <!-- Error Message -->
                    <div x-show="errorMessage" x-cloak class="mb-6 p-4 bg-danger-50 dark:bg-danger-900/30 border border-danger-200 dark:border-danger-800 rounded-lg text-danger-700 dark:text-danger-300">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span x-text="errorMessage"></span>
                        </div>
                    </div>

                    <form @submit.prevent="submitForm" enctype="multipart/form-data" class="space-y-6">
                        <!-- Cover Letter -->
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Cover Letter</h2>
                            <div>
                                <label for="cover_letter" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                                    Introduce yourself and explain why you're the best fit for this job
                                </label>
                                <textarea
                                    id="cover_letter"
                                    x-model="form.cover_letter"
                                    rows="8"
                                    class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                    :class="{ 'border-danger-500': errors.cover_letter }"
                                    placeholder="Describe your relevant experience, approach to the project, and why the client should choose you..."
                                    required
                                ></textarea>
                                <template x-if="errors.cover_letter">
                                    <p class="mt-1 text-sm text-danger-600" x-text="errors.cover_letter[0]"></p>
                                </template>
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
                                        x-model="form.proposed_price"
                                        min="5"
                                        step="0.01"
                                        class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                        :class="{ 'border-danger-500': errors.proposed_price }"
                                        required
                                    >
                                    <template x-if="errors.proposed_price">
                                        <p class="mt-1 text-sm text-danger-600" x-text="errors.proposed_price[0]"></p>
                                    </template>
                                </div>

                                <div>
                                    <label for="proposed_duration" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                                        Duration
                                    </label>
                                    <input
                                        type="number"
                                        id="proposed_duration"
                                        x-model="form.proposed_duration"
                                        min="1"
                                        class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                        :class="{ 'border-danger-500': errors.proposed_duration }"
                                        required
                                    >
                                    <template x-if="errors.proposed_duration">
                                        <p class="mt-1 text-sm text-danger-600" x-text="errors.proposed_duration[0]"></p>
                                    </template>
                                </div>

                                <div>
                                    <label for="duration_type" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                                        Duration Type
                                    </label>
                                    <select
                                        id="duration_type"
                                        x-model="form.duration_type"
                                        class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                        :class="{ 'border-danger-500': errors.duration_type }"
                                        required
                                    >
                                        <option value="days">Days</option>
                                        <option value="weeks">Weeks</option>
                                        <option value="months">Months</option>
                                    </select>
                                    <template x-if="errors.duration_type">
                                        <p class="mt-1 text-sm text-danger-600" x-text="errors.duration_type[0]"></p>
                                    </template>
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
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-semibold text-surface-900 dark:text-white">Milestones (Optional)</h2>
                                <button type="button" @click="addMilestone()" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">
                                    + Add Milestone
                                </button>
                            </div>

                            <template x-for="(milestone, index) in form.milestones" :key="index">
                                <div class="border border-surface-200 dark:border-surface-700 rounded-lg p-4 mb-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Milestone <span x-text="index + 1"></span></span>
                                        <button type="button" @click="removeMilestone(index)" class="text-danger-600 hover:text-danger-700 text-sm">Remove</button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <input
                                                type="text"
                                                x-model="milestone.title"
                                                placeholder="Milestone title"
                                                class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white text-sm"
                                                required
                                            >
                                        </div>
                                        <div>
                                            <input
                                                type="number"
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
                                                x-model="milestone.description"
                                                placeholder="Description (optional)"
                                                rows="2"
                                                class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white text-sm"
                                            ></textarea>
                                        </div>
                                        <div class="datepicker-wrapper">
                                            <input
                                                type="text"
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

                            <p x-show="form.milestones.length === 0" class="text-sm text-surface-500 dark:text-surface-400">
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
                                    @change="handleFiles($event)"
                                    multiple
                                    accept=".pdf,.doc,.docx,.png,.jpg,.jpeg,.zip"
                                    class="w-full text-sm text-surface-600 dark:text-surface-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900/30 dark:file:text-primary-400"
                                >
                                <p class="mt-2 text-xs text-surface-500 dark:text-surface-400">
                                    Accepted formats: PDF, DOC, DOCX, PNG, JPG, ZIP. Max 10MB per file.
                                </p>

                                <!-- Selected Files Preview -->
                                <div x-show="selectedFiles.length > 0" class="mt-3 space-y-2">
                                    <template x-for="(file, index) in selectedFiles" :key="index">
                                        <div class="flex items-center justify-between p-2 bg-surface-50 dark:bg-surface-700/50 rounded-lg">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                </svg>
                                                <span class="text-sm text-surface-700 dark:text-surface-300" x-text="file.name"></span>
                                                <span class="text-xs text-surface-500" x-text="formatFileSize(file.size)"></span>
                                            </div>
                                            <button type="button" @click="removeFile(index)" class="text-danger-600 hover:text-danger-700">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('seller.jobs.available') }}" class="px-6 py-3 text-surface-700 dark:text-surface-300 hover:text-surface-900 dark:hover:text-white font-medium">
                                Cancel
                            </a>
                            <button
                                type="submit"
                                class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors inline-flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="isSubmitting"
                            >
                                <svg x-show="isSubmitting" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="isSubmitting ? 'Submitting...' : 'Submit Proposal'"></span>
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

    <script>
        function proposalForm() {
            return {
                form: {
                    cover_letter: '',
                    proposed_price: '{{ $jobPosting->budget_max ?? $jobPosting->budget_min ?? '' }}',
                    proposed_duration: '7',
                    duration_type: 'weeks',
                    milestones: []
                },
                selectedFiles: [],
                isSubmitting: false,
                successMessage: '',
                errorMessage: '',
                errors: {},

                addMilestone() {
                    this.form.milestones.push({ title: '', description: '', amount: '', due_date: '' });
                },

                removeMilestone(index) {
                    this.form.milestones.splice(index, 1);
                },

                handleFiles(event) {
                    this.selectedFiles = Array.from(event.target.files);
                },

                removeFile(index) {
                    this.selectedFiles.splice(index, 1);
                },

                formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                },

                async submitForm() {
                    this.isSubmitting = true;
                    this.errorMessage = '';
                    this.successMessage = '';
                    this.errors = {};

                    const formData = new FormData();
                    formData.append('cover_letter', this.form.cover_letter);
                    formData.append('proposed_price', this.form.proposed_price);
                    formData.append('proposed_duration', this.form.proposed_duration);
                    formData.append('duration_type', this.form.duration_type);

                    // Add milestones
                    this.form.milestones.forEach((milestone, index) => {
                        formData.append(`milestones[${index}][title]`, milestone.title);
                        formData.append(`milestones[${index}][description]`, milestone.description || '');
                        formData.append(`milestones[${index}][amount]`, milestone.amount);
                        if (milestone.due_date) {
                            formData.append(`milestones[${index}][due_date]`, milestone.due_date);
                        }
                    });

                    // Add files
                    this.selectedFiles.forEach((file) => {
                        formData.append('attachments[]', file);
                    });

                    try {
                        const response = await fetch('{{ route("seller.proposals.store", $jobPosting) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.successMessage = data.message;
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 1500);
                        } else {
                            this.errorMessage = data.message || 'An error occurred. Please try again.';
                            if (data.errors) {
                                this.errors = data.errors;
                            }
                            this.isSubmitting = false;
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.errorMessage = 'A network error occurred. Please try again.';
                        this.isSubmitting = false;
                    }
                }
            }
        }
    </script>
</x-layouts.app>
