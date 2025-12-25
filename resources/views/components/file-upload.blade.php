@props([
    'name',
    'label' => 'Upload file',
    'accept' => '*/*',
    'required' => false,
    'hint' => null,
    'icon' => 'image',
    'preview' => null,
    'maxSize' => '10MB',
])

<div x-data="{
    isDragging: false,
    fileName: '',
    fileSize: '',
    previewUrl: '{{ $preview }}',
    hasFile: {{ $preview ? 'true' : 'false' }},
    handleDrop(e) {
        this.isDragging = false;
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            this.handleFile(files[0]);
            this.$refs.input.files = files;
        }
    },
    handleFile(file) {
        this.fileName = file.name;
        this.fileSize = this.formatSize(file.size);
        this.hasFile = true;

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.previewUrl = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            this.previewUrl = '';
        }
    },
    formatSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },
    clearFile() {
        this.fileName = '';
        this.fileSize = '';
        this.previewUrl = '';
        this.hasFile = false;
        this.$refs.input.value = '';
    }
}" class="w-full">
    @if($label)
        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-danger-500">*</span>
            @endif
        </label>
    @endif

    <div
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleDrop($event)"
        @click="$refs.input.click()"
        :class="isDragging ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-surface-300 dark:border-surface-600 hover:border-primary-400 dark:hover:border-primary-500'"
        class="relative cursor-pointer rounded-xl border-2 border-dashed transition-all duration-200"
    >
        <input
            type="file"
            name="{{ $name }}"
            id="{{ $name }}"
            accept="{{ $accept }}"
            {{ $required ? 'required' : '' }}
            @change="handleFile($event.target.files[0])"
            x-ref="input"
            class="sr-only"
        >

        <!-- Empty State -->
        <div x-show="!hasFile" class="p-8 text-center">
            <div class="mx-auto w-16 h-16 rounded-full bg-surface-100 dark:bg-surface-700 flex items-center justify-center mb-4">
                @if($icon === 'image')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-surface-400 dark:text-surface-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                @elseif($icon === 'file')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-surface-400 dark:text-surface-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-surface-400 dark:text-surface-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                @endif
            </div>
            <div class="space-y-1">
                <p class="text-surface-700 dark:text-surface-300">
                    <span class="font-semibold text-primary-600 dark:text-primary-400">Click to upload</span>
                    <span class="text-surface-500 dark:text-surface-400"> or drag and drop</span>
                </p>
                <p class="text-sm text-surface-500 dark:text-surface-400">
                    @if($hint)
                        {{ $hint }}
                    @else
                        Max file size: {{ $maxSize }}
                    @endif
                </p>
            </div>
        </div>

        <!-- File Selected State -->
        <div x-show="hasFile" x-cloak class="p-4">
            <div class="flex items-center gap-4">
                <!-- Preview -->
                <div class="flex-shrink-0">
                    <template x-if="previewUrl">
                        <img :src="previewUrl" alt="Preview" class="w-20 h-20 rounded-lg object-cover border border-surface-200 dark:border-surface-600">
                    </template>
                    <template x-if="!previewUrl && hasFile">
                        <div class="w-20 h-20 rounded-lg bg-surface-100 dark:bg-surface-700 flex items-center justify-center border border-surface-200 dark:border-surface-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </template>
                </div>

                <!-- File Info -->
                <div class="flex-1 min-w-0">
                    <p x-text="fileName" class="font-medium text-surface-900 dark:text-white truncate"></p>
                    <p x-text="fileSize" class="text-sm text-surface-500 dark:text-surface-400"></p>
                    <p class="text-xs text-success-600 dark:text-success-400 mt-1">Ready to upload</p>
                </div>

                <!-- Actions -->
                <div class="flex-shrink-0 flex items-center gap-2">
                    <button
                        type="button"
                        @click.stop="$refs.input.click()"
                        class="p-2 rounded-lg text-surface-500 hover:text-surface-700 hover:bg-surface-100 dark:text-surface-400 dark:hover:text-surface-200 dark:hover:bg-surface-700 transition-colors"
                        title="Change file"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                    <button
                        type="button"
                        @click.stop="clearFile()"
                        class="p-2 rounded-lg text-danger-500 hover:text-danger-700 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors"
                        title="Remove file"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @error($name)
        <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
    @enderror
</div>
