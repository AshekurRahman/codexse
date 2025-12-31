<x-layouts.app title="Edit Job - {{ $jobPosting->title }}">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('jobs.my-jobs') }}" class="inline-flex items-center gap-2 text-surface-600 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to My Jobs
                </a>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Edit Job Posting</h1>
                <p class="mt-1 text-surface-600 dark:text-surface-400">Update your job details</p>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 bg-danger-50 dark:bg-danger-900/30 border border-danger-200 dark:border-danger-800 rounded-lg text-danger-700 dark:text-danger-300">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('jobs.update', $jobPosting) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                    <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50">
                        <h2 class="font-semibold text-surface-900 dark:text-white">Basic Information</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Job Title *</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $jobPosting->title) }}" required
                                class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                placeholder="e.g., Build a responsive e-commerce website">
                            @error('title')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Category *</label>
                            <select name="category_id" id="category_id" required
                                class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                                <option value="">Select a category</option>
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $jobPosting->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Description *</label>
                            <textarea name="description" id="description" rows="6" required
                                class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                placeholder="Describe your project in detail...">{{ old('description', $jobPosting->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Budget & Timeline -->
                <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                    <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50">
                        <h2 class="font-semibold text-surface-900 dark:text-white">Budget & Timeline</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Budget Type -->
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Budget Type *</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative flex cursor-pointer rounded-lg border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 p-4 focus:outline-none has-[:checked]:ring-2 has-[:checked]:ring-primary-500 has-[:checked]:border-primary-500">
                                    <input type="radio" name="budget_type" value="fixed" class="sr-only peer" {{ old('budget_type', $jobPosting->budget_type) === 'fixed' ? 'checked' : '' }}>
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-medium text-surface-900 dark:text-white">Fixed Price</span>
                                            <span class="mt-1 flex items-center text-sm text-surface-500 dark:text-surface-400">Pay a fixed amount for the entire project</span>
                                        </span>
                                    </span>
                                </label>
                                <label class="relative flex cursor-pointer rounded-lg border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 p-4 focus:outline-none has-[:checked]:ring-2 has-[:checked]:ring-primary-500 has-[:checked]:border-primary-500">
                                    <input type="radio" name="budget_type" value="hourly" class="sr-only peer" {{ old('budget_type', $jobPosting->budget_type) === 'hourly' ? 'checked' : '' }}>
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-medium text-surface-900 dark:text-white">Hourly Rate</span>
                                            <span class="mt-1 flex items-center text-sm text-surface-500 dark:text-surface-400">Pay based on hours worked</span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Budget Range -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="budget_min" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Minimum Budget ($) *</label>
                                <input type="number" name="budget_min" id="budget_min" value="{{ old('budget_min', $jobPosting->budget_min) }}" required min="1" step="1"
                                    class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                                @error('budget_min')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="budget_max" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Maximum Budget ($) *</label>
                                <input type="number" name="budget_max" id="budget_max" value="{{ old('budget_max', $jobPosting->budget_max) }}" required min="1" step="1"
                                    class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                                @error('budget_max')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Duration Type -->
                        <div>
                            <label for="duration_type" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Project Duration</label>
                            <select name="duration_type" id="duration_type"
                                class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                                <option value="one_time" {{ old('duration_type', $jobPosting->duration_type) === 'one_time' ? 'selected' : '' }}>One-time Project</option>
                                <option value="ongoing" {{ old('duration_type', $jobPosting->duration_type) === 'ongoing' ? 'selected' : '' }}>Ongoing Project</option>
                            </select>
                        </div>

                        <!-- Deadline -->
                        <div>
                            <label for="deadline" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Deadline (Optional)</label>
                            <input type="date" name="deadline" id="deadline" value="{{ old('deadline', $jobPosting->deadline?->format('Y-m-d')) }}"
                                class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                            @error('deadline')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Skills Required -->
                <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                    <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50">
                        <h2 class="font-semibold text-surface-900 dark:text-white">Skills Required</h2>
                    </div>
                    <div class="p-6">
                        <label for="skills" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Required Skills</label>
                        <input type="text" name="skills" id="skills" value="{{ old('skills', is_array($jobPosting->skills_required) ? implode(', ', $jobPosting->skills_required) : $jobPosting->skills_required) }}"
                            class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                            placeholder="e.g., PHP, Laravel, Vue.js, MySQL (comma separated)">
                        <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">Separate skills with commas</p>
                    </div>
                </div>

                <!-- Status -->
                @if($jobPosting->status !== 'in_progress' && $jobPosting->status !== 'completed')
                    <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                        <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50">
                            <h2 class="font-semibold text-surface-900 dark:text-white">Status</h2>
                        </div>
                        <div class="p-6">
                            <select name="status" id="status"
                                class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                                <option value="draft" {{ old('status', $jobPosting->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="open" {{ old('status', $jobPosting->status) === 'open' ? 'selected' : '' }}>Open</option>
                            </select>
                        </div>
                    </div>
                @endif

                <!-- Submit -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('jobs.my-jobs') }}" class="px-6 py-3 text-sm font-medium text-surface-700 dark:text-surface-300 hover:text-surface-900 dark:hover:text-white">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Job
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
