<x-layouts.app title="Create Service">
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('seller.services.index') }}" class="inline-flex items-center gap-2 text-surface-600 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Services
                </a>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Create New Service</h1>
                <p class="text-surface-600 dark:text-surface-400 mt-1">Set up your service with packages and requirements</p>
            </div>

            <!-- Form -->
            <form action="{{ route('seller.services.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="serviceForm()">
                @csrf

                <!-- Basic Information -->
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 space-y-6">
                    <h2 class="text-lg font-semibold text-surface-900 dark:text-white pb-4 border-b border-surface-200 dark:border-surface-700">Basic Information</h2>

                    <div>
                        <label for="name" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Service Title *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white placeholder-surface-400" placeholder="I will design a professional logo">
                        @error('name')
                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Category *</label>
                        <select id="category_id" name="category_id" required class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white">
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Description *</label>
                        <textarea id="description" name="description" rows="6" required class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white placeholder-surface-400" placeholder="Describe your service in detail...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-file-upload
                        name="thumbnail"
                        label="Service Thumbnail"
                        accept="image/*"
                        :required="true"
                        icon="image"
                        hint="PNG, JPG or WebP. Recommended: 800x600px. Max 2MB."
                        maxSize="2MB"
                    />
                </div>

                <!-- Packages -->
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 space-y-6">
                    <h2 class="text-lg font-semibold text-surface-900 dark:text-white pb-4 border-b border-surface-200 dark:border-surface-700">Pricing Packages</h2>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Basic Package -->
                        <div class="border border-surface-200 dark:border-surface-700 rounded-lg p-4 space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-surface-900 dark:text-white">Basic</h3>
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="packages[basic][enabled]" value="1" checked class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                                    Enabled
                                </label>
                            </div>
                            <div>
                                <label class="block text-sm text-surface-600 dark:text-surface-400 mb-1">Name</label>
                                <input type="text" name="packages[basic][name]" value="{{ old('packages.basic.name', 'Basic') }}" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm text-surface-600 dark:text-surface-400 mb-1">Price ($) *</label>
                                <input type="number" name="packages[basic][price]" value="{{ old('packages.basic.price') }}" step="0.01" min="5" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500" placeholder="25.00">
                            </div>
                            <div>
                                <label class="block text-sm text-surface-600 dark:text-surface-400 mb-1">Delivery (days) *</label>
                                <input type="number" name="packages[basic][delivery_days]" value="{{ old('packages.basic.delivery_days') }}" min="1" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500" placeholder="3">
                            </div>
                            <div>
                                <label class="block text-sm text-surface-600 dark:text-surface-400 mb-1">Revisions</label>
                                <input type="number" name="packages[basic][revisions]" value="{{ old('packages.basic.revisions', 1) }}" min="0" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm text-surface-600 dark:text-surface-400 mb-1">What's Included</label>
                                <textarea name="packages[basic][deliverables]" rows="3" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500" placeholder="One concept&#10;Source file&#10;High resolution">{{ old('packages.basic.deliverables') }}</textarea>
                            </div>
                        </div>

                        <!-- Standard Package -->
                        <div class="border border-primary-200 dark:border-primary-800 rounded-lg p-4 space-y-4 bg-primary-50/50 dark:bg-primary-900/10">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-surface-900 dark:text-white">Standard</h3>
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="packages[standard][enabled]" value="1" checked class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                                    Enabled
                                </label>
                            </div>
                            <div>
                                <label class="block text-sm text-surface-600 dark:text-surface-400 mb-1">Name</label>
                                <input type="text" name="packages[standard][name]" value="{{ old('packages.standard.name', 'Standard') }}" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm text-surface-600 dark:text-surface-400 mb-1">Price ($) *</label>
                                <input type="number" name="packages[standard][price]" value="{{ old('packages.standard.price') }}" step="0.01" min="5" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500" placeholder="50.00">
                            </div>
                            <div>
                                <label class="block text-sm text-surface-600 dark:text-surface-400 mb-1">Delivery (days) *</label>
                                <input type="number" name="packages[standard][delivery_days]" value="{{ old('packages.standard.delivery_days') }}" min="1" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500" placeholder="5">
                            </div>
                            <div>
                                <label class="block text-sm text-surface-600 dark:text-surface-400 mb-1">Revisions</label>
                                <input type="number" name="packages[standard][revisions]" value="{{ old('packages.standard.revisions', 3) }}" min="0" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm text-surface-600 dark:text-surface-400 mb-1">What's Included</label>
                                <textarea name="packages[standard][deliverables]" rows="3" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500" placeholder="Two concepts&#10;Source files&#10;High resolution&#10;Social media kit">{{ old('packages.standard.deliverables') }}</textarea>
                            </div>
                        </div>

                        <!-- Premium Package -->
                        <div class="border border-surface-200 dark:border-surface-700 rounded-lg p-4 space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-surface-900 dark:text-white">Premium</h3>
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="packages[premium][enabled]" value="1" class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                                    Enabled
                                </label>
                            </div>
                            <div>
                                <label class="block text-sm text-surface-600 dark:text-surface-400 mb-1">Name</label>
                                <input type="text" name="packages[premium][name]" value="{{ old('packages.premium.name', 'Premium') }}" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm text-surface-600 dark:text-surface-400 mb-1">Price ($) *</label>
                                <input type="number" name="packages[premium][price]" value="{{ old('packages.premium.price') }}" step="0.01" min="5" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500" placeholder="100.00">
                            </div>
                            <div>
                                <label class="block text-sm text-surface-600 dark:text-surface-400 mb-1">Delivery (days) *</label>
                                <input type="number" name="packages[premium][delivery_days]" value="{{ old('packages.premium.delivery_days') }}" min="1" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500" placeholder="7">
                            </div>
                            <div>
                                <label class="block text-sm text-surface-600 dark:text-surface-400 mb-1">Revisions</label>
                                <input type="number" name="packages[premium][revisions]" value="{{ old('packages.premium.revisions', 5) }}" min="0" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm text-surface-600 dark:text-surface-400 mb-1">What's Included</label>
                                <textarea name="packages[premium][deliverables]" rows="3" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500" placeholder="Unlimited concepts&#10;All source files&#10;Brand guidelines&#10;Priority support">{{ old('packages.premium.deliverables') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Requirements -->
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-surface-200 dark:border-surface-700">
                        <div>
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white">Buyer Requirements</h2>
                            <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">Questions to ask buyers when they order</p>
                        </div>
                        <button type="button" @click="addRequirement()" class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Question
                        </button>
                    </div>

                    <div class="space-y-4" id="requirements-container">
                        <template x-for="(req, index) in requirements" :key="index">
                            <div class="flex gap-4 items-start p-4 bg-surface-50 dark:bg-surface-900/50 rounded-lg">
                                <div class="flex-1 space-y-3">
                                    <input type="text" :name="'requirements['+index+'][question]'" x-model="req.question" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500" placeholder="e.g., What style are you looking for?">
                                    <div class="flex gap-4">
                                        <select :name="'requirements['+index+'][type]'" x-model="req.type" class="px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                                            <option value="text">Short Text</option>
                                            <option value="textarea">Long Text</option>
                                            <option value="file">File Upload</option>
                                        </select>
                                        <label class="flex items-center gap-2 text-sm text-surface-600 dark:text-surface-400">
                                            <input type="checkbox" :name="'requirements['+index+'][is_required]'" x-model="req.is_required" value="1" class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                                            Required
                                        </label>
                                    </div>
                                </div>
                                <button type="button" @click="removeRequirement(index)" class="p-2 text-surface-400 hover:text-danger-600 dark:hover:text-danger-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <div x-show="requirements.length === 0" class="text-center py-8 text-surface-500 dark:text-surface-400">
                        <p>No requirements added yet. Click "Add Question" to ask buyers for information.</p>
                    </div>
                </div>

                <!-- FAQ -->
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-surface-200 dark:border-surface-700">
                        <div>
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white">FAQ</h2>
                            <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">Common questions about your service</p>
                        </div>
                        <button type="button" @click="addFaq()" class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add FAQ
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(faq, index) in faqs" :key="index">
                            <div class="flex gap-4 items-start p-4 bg-surface-50 dark:bg-surface-900/50 rounded-lg">
                                <div class="flex-1 space-y-3">
                                    <input type="text" :name="'faqs['+index+'][question]'" x-model="faq.question" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500" placeholder="Question">
                                    <textarea :name="'faqs['+index+'][answer]'" x-model="faq.answer" rows="2" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500" placeholder="Answer"></textarea>
                                </div>
                                <button type="button" @click="removeFaq(index)" class="p-2 text-surface-400 hover:text-danger-600 dark:hover:text-danger-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <div x-show="faqs.length === 0" class="text-center py-8 text-surface-500 dark:text-surface-400">
                        <p>No FAQs added yet.</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('seller.services.index') }}" class="px-6 py-2.5 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">Cancel</a>
                    <button type="submit" name="action" value="draft" class="px-6 py-2.5 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">Save as Draft</button>
                    <button type="submit" name="action" value="publish" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">Submit for Review</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function serviceForm() {
            return {
                requirements: [],
                faqs: [],
                addRequirement() {
                    this.requirements.push({ question: '', type: 'text', is_required: true });
                },
                removeRequirement(index) {
                    this.requirements.splice(index, 1);
                },
                addFaq() {
                    this.faqs.push({ question: '', answer: '' });
                },
                removeFaq(index) {
                    this.faqs.splice(index, 1);
                }
            }
        }
    </script>
    @endpush
</x-layouts.app>
