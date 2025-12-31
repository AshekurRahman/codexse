<x-layouts.app title="Post a Job - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-2 text-surface-600 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Jobs
                </a>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Post a New Job</h1>
                <p class="mt-1 text-surface-600 dark:text-surface-400">Find the perfect freelancer for your project</p>
            </div>

            <form action="{{ route('jobs.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Basic Information -->
                <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                    <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50">
                        <h2 class="font-semibold text-surface-900 dark:text-white">Basic Information</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Job Title *</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
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
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
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
                                placeholder="Describe your project in detail. Include what you need, any specific requirements, and expected deliverables...">{{ old('description') }}</textarea>
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
                                <label class="relative flex cursor-pointer rounded-lg border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 p-4 focus:outline-none">
                                    <input type="radio" name="budget_type" value="fixed" class="sr-only" {{ old('budget_type', 'fixed') === 'fixed' ? 'checked' : '' }}>
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-medium text-surface-900 dark:text-white">Fixed Price</span>
                                            <span class="mt-1 flex items-center text-sm text-surface-500 dark:text-surface-400">Pay a fixed amount for the entire project</span>
                                        </span>
                                    </span>
                                    <svg class="h-5 w-5 text-primary-600 hidden peer-checked:block" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </label>
                                <label class="relative flex cursor-pointer rounded-lg border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 p-4 focus:outline-none">
                                    <input type="radio" name="budget_type" value="hourly" class="sr-only" {{ old('budget_type') === 'hourly' ? 'checked' : '' }}>
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-medium text-surface-900 dark:text-white">Hourly Rate</span>
                                            <span class="mt-1 flex items-center text-sm text-surface-500 dark:text-surface-400">Pay based on hours worked</span>
                                        </span>
                                    </span>
                                    <svg class="h-5 w-5 text-primary-600 hidden peer-checked:block" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </label>
                            </div>
                        </div>

                        <!-- Budget Range -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="budget_min" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Minimum Budget ($) *</label>
                                <input type="number" name="budget_min" id="budget_min" value="{{ old('budget_min') }}" required min="1" step="1"
                                    class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                    placeholder="100">
                                @error('budget_min')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="budget_max" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Maximum Budget ($) *</label>
                                <input type="number" name="budget_max" id="budget_max" value="{{ old('budget_max') }}" required min="1" step="1"
                                    class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                    placeholder="500">
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
                                <option value="one_time" {{ old('duration_type') === 'one_time' ? 'selected' : '' }}>One-time Project</option>
                                <option value="ongoing" {{ old('duration_type') === 'ongoing' ? 'selected' : '' }}>Ongoing Project</option>
                            </select>
                        </div>

                        <!-- Deadline -->
                        <div>
                            <label for="deadline" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Deadline (Optional)</label>
                            <input type="date" name="deadline" id="deadline" value="{{ old('deadline') }}"
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
                        <input type="text" name="skills" id="skills" value="{{ old('skills') }}"
                            class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                            placeholder="e.g., PHP, Laravel, Vue.js, MySQL (comma separated)">
                        <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">Separate skills with commas</p>
                    </div>
                </div>

                <!-- Attachments -->
                <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                    <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50">
                        <h2 class="font-semibold text-surface-900 dark:text-white">Attachments (Optional)</h2>
                    </div>
                    <div class="p-6">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Upload Files</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-surface-300 dark:border-surface-600 border-dashed rounded-lg">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-surface-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-surface-600 dark:text-surface-400">
                                    <label for="attachments" class="relative cursor-pointer bg-white dark:bg-surface-800 rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none">
                                        <span>Upload files</span>
                                        <input id="attachments" name="attachments[]" type="file" class="sr-only" multiple>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-surface-500 dark:text-surface-400">PDF, DOC, PNG, JPG up to 10MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('jobs.index') }}" class="px-6 py-3 text-sm font-medium text-surface-700 dark:text-surface-300 hover:text-surface-900 dark:hover:text-white">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Post Job
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
