<x-layouts.app :title="'Request Quote - ' . $service->name">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-surface-500 dark:text-surface-400 mb-6">
                <a href="{{ route('services.index') }}" class="hover:text-primary-600">Services</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('services.show', $service) }}" class="hover:text-primary-600">{{ $service->name }}</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-surface-900 dark:text-white">Request Quote</span>
            </nav>

            <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-5 border-b border-surface-200 dark:border-surface-700">
                    <h1 class="text-xl font-bold text-surface-900 dark:text-white">Request a Custom Quote</h1>
                    <p class="mt-1 text-sm text-surface-600 dark:text-surface-400">
                        Describe your project requirements and {{ $service->seller->store_name }} will send you a personalized quote. The more details you provide, the more accurate your quote will be.
                    </p>
                </div>

                <!-- Service Info -->
                <div class="px-6 py-4 bg-surface-50 dark:bg-surface-700/50 border-b border-surface-200 dark:border-surface-700">
                    <div class="flex items-center gap-4">
                        @if($service->thumbnail)
                            <img src="{{ asset('storage/' . $service->thumbnail) }}" alt="{{ $service->name }}" class="w-16 h-16 rounded-lg object-cover">
                        @else
                            <div class="w-16 h-16 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                        <div>
                            <h3 class="font-semibold text-surface-900 dark:text-white">{{ $service->name }}</h3>
                            <p class="text-sm text-surface-600 dark:text-surface-400">by {{ $service->seller->store_name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form action="{{ route('quotes.store', $service) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6" data-ajax-form x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                    @csrf

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                            Project Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}"
                            class="w-full rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-900 px-4 py-2.5 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                            placeholder="e.g., Custom Logo Design for Tech Startup" data-validate="required|min:5">
                        @error('title')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                            Describe Your Project <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" id="description" rows="6"
                            class="w-full rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-900 px-4 py-2.5 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                            placeholder="Please provide as much detail as possible about your requirements..." data-validate="required|min:20">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">
                            The more details you provide, the better quote you'll receive.
                        </p>
                    </div>

                    <!-- Budget Range -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="budget_min" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                                Budget Minimum
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-surface-400">$</span>
                                <input type="number" name="budget_min" id="budget_min" value="{{ old('budget_min') }}" step="0.01" min="0"
                                    class="w-full rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-900 pl-8 pr-4 py-2.5 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                                    placeholder="0.00">
                            </div>
                            @error('budget_min')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="budget_max" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                                Budget Maximum
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-surface-400">$</span>
                                <input type="number" name="budget_max" id="budget_max" value="{{ old('budget_max') }}" step="0.01" min="0"
                                    class="w-full rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-900 pl-8 pr-4 py-2.5 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                                    placeholder="0.00">
                            </div>
                            @error('budget_max')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Deadline -->
                    <div>
                        <label for="deadline" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                            Deadline (optional)
                        </label>
                        <div x-data="datepicker({ value: '{{ old('deadline') }}', minDate: 'today' })" class="datepicker-wrapper">
                            <input type="text" name="deadline" id="deadline" x-ref="input" readonly
                                class="w-full rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-900 px-4 py-2.5 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500 cursor-pointer"
                                placeholder="Select deadline date">
                            <svg class="datepicker-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        @error('deadline')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Attachments -->
                    <div x-data="fileUpload()">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                            Attachments (optional)
                        </label>
                        <div
                            class="border-2 border-dashed rounded-lg p-6 text-center transition-colors"
                            :class="isDragging ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-surface-200 dark:border-surface-700 hover:border-primary-500'"
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="handleDrop($event)"
                        >
                            <input type="file" name="attachments[]" id="attachments" multiple class="hidden" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.zip" @change="handleFiles($event)">
                            <label for="attachments" class="cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="mt-2 text-sm text-surface-600 dark:text-surface-400">
                                    Click to upload or drag and drop
                                </p>
                                <p class="text-xs text-surface-500 dark:text-surface-500">
                                    Images, PDFs, DOCs up to 10MB each
                                </p>
                            </label>
                        </div>

                        <!-- File List -->
                        <div x-show="files.length > 0" x-cloak class="mt-4 space-y-2">
                            <p class="text-sm font-medium text-surface-700 dark:text-surface-300">
                                Selected files (<span x-text="files.length"></span>):
                            </p>
                            <template x-for="(file, index) in files" :key="index">
                                <div class="flex items-center justify-between p-3 bg-surface-50 dark:bg-surface-700/50 rounded-lg">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="shrink-0">
                                            <template x-if="file.type.startsWith('image/')">
                                                <img :src="file.preview" class="w-10 h-10 rounded object-cover" alt="">
                                            </template>
                                            <template x-if="!file.type.startsWith('image/')">
                                                <div class="w-10 h-10 rounded bg-surface-200 dark:bg-surface-600 flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-surface-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-surface-900 dark:text-white truncate" x-text="file.name"></p>
                                            <p class="text-xs text-surface-500" x-text="formatSize(file.size)"></p>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeFile(index)" class="shrink-0 p-1 text-surface-400 hover:text-danger-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        @error('attachments.*')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <script>
                        function fileUpload() {
                            return {
                                files: [],
                                isDragging: false,

                                handleFiles(event) {
                                    this.addFiles(event.target.files);
                                },

                                handleDrop(event) {
                                    this.isDragging = false;
                                    this.addFiles(event.dataTransfer.files);
                                },

                                addFiles(fileList) {
                                    for (let i = 0; i < fileList.length; i++) {
                                        const file = fileList[i];
                                        if (file.size > 10 * 1024 * 1024) {
                                            alert(`File "${file.name}" is too large. Maximum size is 10MB.`);
                                            continue;
                                        }

                                        const fileObj = {
                                            name: file.name,
                                            size: file.size,
                                            type: file.type,
                                            preview: null,
                                            file: file
                                        };

                                        this.files.push(fileObj);

                                        if (file.type.startsWith('image/')) {
                                            const reader = new FileReader();
                                            const idx = this.files.length - 1;
                                            reader.onload = (e) => {
                                                // Trigger Alpine reactivity by reassigning the array item
                                                this.files[idx] = { ...this.files[idx], preview: e.target.result };
                                            };
                                            reader.readAsDataURL(file);
                                        }
                                    }

                                    this.updateInput();
                                },

                                removeFile(index) {
                                    this.files.splice(index, 1);
                                    this.updateInput();
                                },

                                updateInput() {
                                    const input = document.getElementById('attachments');
                                    const dt = new DataTransfer();
                                    this.files.forEach(f => dt.items.add(f.file));
                                    input.files = dt.files;
                                },

                                formatSize(bytes) {
                                    if (bytes === 0) return '0 Bytes';
                                    const k = 1024;
                                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                                }
                            };
                        }
                    </script>

                    <!-- Submit -->
                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                        <a href="{{ route('services.show', $service) }}" class="px-6 py-2.5 text-sm font-medium text-surface-700 dark:text-surface-300 hover:text-surface-900 dark:hover:text-white transition-colors" x-show="!isSubmitting">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 transition-all disabled:opacity-70 disabled:cursor-not-allowed" :disabled="isSubmitting">
                            <template x-if="!isSubmitting">
                                <span>Submit Quote Request</span>
                            </template>
                            <template x-if="isSubmitting">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Uploading...
                                </span>
                            </template>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
