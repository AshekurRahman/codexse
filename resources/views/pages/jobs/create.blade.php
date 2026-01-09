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
                <input type="hidden" name="status" value="open">

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
                                <label class="relative flex cursor-pointer rounded-lg border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 p-4 focus:outline-none has-[:checked]:border-primary-500 has-[:checked]:ring-2 has-[:checked]:ring-primary-500">
                                    <input type="radio" name="budget_type" value="fixed" class="sr-only peer" {{ old('budget_type', 'fixed') === 'fixed' ? 'checked' : '' }}>
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
                                <label class="relative flex cursor-pointer rounded-lg border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 p-4 focus:outline-none has-[:checked]:border-primary-500 has-[:checked]:ring-2 has-[:checked]:ring-primary-500">
                                    <input type="radio" name="budget_type" value="hourly" class="sr-only peer" {{ old('budget_type') === 'hourly' ? 'checked' : '' }}>
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
                            <div x-data="datepicker({ value: '{{ old('deadline') }}', minDate: 'today' })" class="datepicker-wrapper">
                                <input type="text" name="deadline" id="deadline" x-ref="input" readonly
                                    class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500 cursor-pointer"
                                    placeholder="Select deadline date">
                                <svg class="datepicker-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
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

                <!-- Hidden input for uploaded files -->
                <input type="hidden" name="uploaded_files" id="uploaded-files-input" value="[]">

                <!-- Attachments -->
                <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                    <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50">
                        <h2 class="font-semibold text-surface-900 dark:text-white">Attachments (Optional)</h2>
                    </div>
                    <div class="p-6">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Upload Files</label>

                        <!-- Upload Area -->
                        <div id="upload-area" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-surface-300 dark:border-surface-600 border-dashed rounded-lg hover:border-primary-400 dark:hover:border-primary-500 transition-colors cursor-pointer">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-surface-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-surface-600 dark:text-surface-400">
                                    <label for="file-input" class="relative cursor-pointer rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none">
                                        <span>Upload files</span>
                                        <input id="file-input" type="file" class="sr-only" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.zip,.txt">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-surface-500 dark:text-surface-400">PDF, DOC, PNG, JPG, ZIP up to 10MB each</p>
                            </div>
                        </div>

                        <!-- Uploaded Files Preview -->
                        <div id="uploaded-files" class="mt-4 space-y-3"></div>

                        <!-- Upload Error -->
                        <div id="upload-error" class="mt-2 text-sm text-danger-600 hidden"></div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('jobs.index') }}" class="px-6 py-3 text-sm font-medium text-surface-700 dark:text-surface-300 hover:text-surface-900 dark:hover:text-white">
                        Cancel
                    </a>
                    <button type="submit" id="submit-btn" class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Post Job
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadArea = document.getElementById('upload-area');
            const fileInput = document.getElementById('file-input');
            const uploadedFilesContainer = document.getElementById('uploaded-files');
            const uploadedFilesInput = document.getElementById('uploaded-files-input');
            const uploadError = document.getElementById('upload-error');
            const submitBtn = document.getElementById('submit-btn');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

            let uploadedFiles = [];
            let uploadingCount = 0;

            // Click to upload
            uploadArea.addEventListener('click', (e) => {
                if (e.target.tagName !== 'INPUT') {
                    fileInput.click();
                }
            });

            // Drag and drop
            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
            });

            uploadArea.addEventListener('dragleave', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
            });

            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
                handleFiles(e.dataTransfer.files);
            });

            // File input change
            fileInput.addEventListener('change', () => {
                handleFiles(fileInput.files);
                fileInput.value = ''; // Reset input
            });

            function handleFiles(files) {
                hideError();

                for (const file of files) {
                    // Validate file size (10MB)
                    if (file.size > 10 * 1024 * 1024) {
                        showError(`File "${file.name}" is too large. Maximum size is 10MB.`);
                        continue;
                    }

                    // Validate file type
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/x-zip-compressed', 'text/plain'];
                    if (!allowedTypes.includes(file.type)) {
                        showError(`File "${file.name}" is not a supported file type.`);
                        continue;
                    }

                    uploadFile(file);
                }
            }

            function uploadFile(file) {
                const fileId = 'file-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
                const isImage = file.type.startsWith('image/');

                // Create preview element
                const previewEl = document.createElement('div');
                previewEl.id = fileId;
                previewEl.className = 'flex items-center gap-4 p-3 bg-surface-50 dark:bg-surface-700/50 rounded-lg border border-surface-200 dark:border-surface-600';

                // Determine icon/preview
                let preview = '';
                if (isImage) {
                    preview = `<div class="w-16 h-16 rounded-lg overflow-hidden bg-surface-200 dark:bg-surface-600 flex-shrink-0" id="${fileId}-preview"></div>`;
                } else {
                    const icon = getFileIcon(file.name);
                    preview = `<div class="w-16 h-16 rounded-lg bg-surface-200 dark:bg-surface-600 flex items-center justify-center flex-shrink-0">${icon}</div>`;
                }

                previewEl.innerHTML = `
                    ${preview}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-surface-900 dark:text-white truncate">${escapeHtml(file.name)}</p>
                        <p class="text-xs text-surface-500">${formatFileSize(file.size)}</p>
                        <div class="mt-2 w-full bg-surface-200 dark:bg-surface-600 rounded-full h-1.5" id="${fileId}-progress-container">
                            <div class="bg-primary-500 h-1.5 rounded-full transition-all duration-300" id="${fileId}-progress" style="width: 0%"></div>
                        </div>
                        <p class="text-xs text-surface-500 mt-1" id="${fileId}-status">Uploading...</p>
                    </div>
                    <button type="button" class="p-2 text-surface-400 hover:text-danger-500 transition-colors" id="${fileId}-remove" style="display: none;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                `;

                uploadedFilesContainer.appendChild(previewEl);

                // Show image preview
                if (isImage) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const previewImg = document.getElementById(`${fileId}-preview`);
                        if (previewImg) {
                            previewImg.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover" alt="${escapeHtml(file.name)}">`;
                        }
                    };
                    reader.readAsDataURL(file);
                }

                // Upload file
                uploadingCount++;
                updateSubmitButton();

                const formData = new FormData();
                formData.append('file', file);

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route("jobs.upload") }}');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.setRequestHeader('Accept', 'application/json');

                xhr.upload.onprogress = (e) => {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        const progressBar = document.getElementById(`${fileId}-progress`);
                        if (progressBar) {
                            progressBar.style.width = percent + '%';
                        }
                    }
                };

                xhr.onload = () => {
                    uploadingCount--;
                    updateSubmitButton();

                    const progressContainer = document.getElementById(`${fileId}-progress-container`);
                    const statusEl = document.getElementById(`${fileId}-status`);
                    const removeBtn = document.getElementById(`${fileId}-remove`);

                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            // Store file info
                            uploadedFiles.push({
                                id: fileId,
                                ...response.file
                            });
                            updateHiddenInput();

                            // Update UI
                            if (progressContainer) progressContainer.style.display = 'none';
                            if (statusEl) {
                                statusEl.textContent = 'Uploaded';
                                statusEl.classList.remove('text-surface-500');
                                statusEl.classList.add('text-success-600');
                            }
                            if (removeBtn) {
                                removeBtn.style.display = 'block';
                                removeBtn.addEventListener('click', () => removeFile(fileId, response.file.path));
                            }
                        } else {
                            handleUploadError(fileId, response.message || 'Upload failed');
                        }
                    } else {
                        let errorMessage = 'Upload failed';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || response.errors?.file?.[0] || errorMessage;
                        } catch (e) {}
                        handleUploadError(fileId, errorMessage);
                    }
                };

                xhr.onerror = () => {
                    uploadingCount--;
                    updateSubmitButton();
                    handleUploadError(fileId, 'Network error. Please try again.');
                };

                xhr.send(formData);
            }

            function handleUploadError(fileId, message) {
                const el = document.getElementById(fileId);
                if (el) {
                    el.classList.add('border-danger-300', 'bg-danger-50', 'dark:bg-danger-900/20');
                    el.classList.remove('border-surface-200', 'bg-surface-50');

                    const statusEl = document.getElementById(`${fileId}-status`);
                    if (statusEl) {
                        statusEl.textContent = message;
                        statusEl.classList.remove('text-surface-500');
                        statusEl.classList.add('text-danger-600');
                    }

                    const progressContainer = document.getElementById(`${fileId}-progress-container`);
                    if (progressContainer) progressContainer.style.display = 'none';

                    // Add remove button for failed uploads
                    const removeBtn = document.getElementById(`${fileId}-remove`);
                    if (removeBtn) {
                        removeBtn.style.display = 'block';
                        removeBtn.addEventListener('click', () => {
                            el.remove();
                        });
                    }
                }
            }

            function removeFile(fileId, path) {
                // Remove from server
                fetch('{{ route("jobs.delete-upload") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ path: path })
                });

                // Remove from array
                uploadedFiles = uploadedFiles.filter(f => f.id !== fileId);
                updateHiddenInput();

                // Remove from DOM
                const el = document.getElementById(fileId);
                if (el) el.remove();
            }

            function updateHiddenInput() {
                const filesData = uploadedFiles.map(f => ({
                    path: f.path,
                    name: f.name,
                    size: f.size,
                    type: f.type
                }));
                uploadedFilesInput.value = JSON.stringify(filesData);
            }

            function updateSubmitButton() {
                submitBtn.disabled = uploadingCount > 0;
                if (uploadingCount > 0) {
                    submitBtn.innerHTML = `
                        <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Uploading...
                    `;
                } else {
                    submitBtn.innerHTML = `
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Post Job
                    `;
                }
            }

            function showError(message) {
                uploadError.textContent = message;
                uploadError.classList.remove('hidden');
            }

            function hideError() {
                uploadError.classList.add('hidden');
            }

            function getFileIcon(filename) {
                const ext = filename.split('.').pop().toLowerCase();
                let color = 'text-surface-400';

                if (ext === 'pdf') color = 'text-danger-500';
                else if (['doc', 'docx'].includes(ext)) color = 'text-primary-500';
                else if (['zip', 'rar'].includes(ext)) color = 'text-warning-500';
                else if (ext === 'txt') color = 'text-surface-500';

                return `
                    <div class="text-center">
                        <svg class="w-8 h-8 mx-auto ${color}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-xs font-medium ${color}">${ext.toUpperCase()}</span>
                    </div>
                `;
            }

            function formatFileSize(bytes) {
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        });
    </script>
</x-layouts.app>
