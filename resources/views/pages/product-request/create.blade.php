<x-layouts.app title="Request a Product - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-surface-900 dark:text-white">Request a Product</h1>
                <p class="mt-2 text-surface-600 dark:text-surface-400">
                    Can't find what you're looking for? Let us know and we'll try to help!
                </p>
            </div>

            <!-- Form Card -->
            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-6 md:p-8">
                <form action="{{ route('product-request.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" data-ajax-form>
                    @csrf

                    <!-- Contact Information -->
                    <div class="border-b border-surface-200 dark:border-surface-700 pb-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Contact Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
                                    Your Name <span class="text-danger-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    id="name"
                                    value="{{ old('name', auth()->user()?->name) }}"
                                    class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                    data-validate="required|min:2"
                                >
                                @error('name')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
                                    Email Address <span class="text-danger-500">*</span>
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    value="{{ old('email', auth()->user()?->email) }}"
                                    class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                    data-validate="required|email"
                                >
                                @error('email')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="phone" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
                                    Phone Number <span class="text-surface-400">(optional)</span>
                                </label>
                                <input
                                    type="tel"
                                    name="phone"
                                    id="phone"
                                    value="{{ old('phone') }}"
                                    class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                >
                                @error('phone')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Product Details -->
                    <div class="border-b border-surface-200 dark:border-surface-700 pb-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Product Details</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="product_title" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
                                    Product Title <span class="text-danger-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="product_title"
                                    id="product_title"
                                    value="{{ old('product_title') }}"
                                    placeholder="e.g., E-commerce Dashboard Template"
                                    class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                    data-validate="required|min:5"
                                >
                                @error('product_title')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="category_id" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
                                    Category <span class="text-surface-400">(optional)</span>
                                </label>
                                <select
                                    name="category_id"
                                    id="category_id"
                                    class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                >
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
                                    Description <span class="text-danger-500">*</span>
                                </label>
                                <textarea
                                    name="description"
                                    id="description"
                                    rows="5"
                                    placeholder="Describe in detail what kind of product you're looking for, its purpose, and any specific requirements..."
                                    class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                    data-validate="required|min:50"
                                >{{ old('description') }}</textarea>
                                <p class="mt-1 text-xs text-surface-500">Minimum 50 characters</p>
                                @error('description')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="features" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
                                    Required Features <span class="text-surface-400">(optional)</span>
                                </label>
                                <textarea
                                    name="features"
                                    id="features"
                                    rows="3"
                                    placeholder="List any specific features you need, e.g.:&#10;- User authentication&#10;- Payment integration&#10;- Dark mode support"
                                    class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                >{{ old('features') }}</textarea>
                                @error('features')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Budget & Urgency -->
                    <div class="border-b border-surface-200 dark:border-surface-700 pb-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Budget & Timeline</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="budget_min" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
                                    Min Budget ($) <span class="text-surface-400">(optional)</span>
                                </label>
                                <input
                                    type="number"
                                    name="budget_min"
                                    id="budget_min"
                                    value="{{ old('budget_min') }}"
                                    min="0"
                                    step="0.01"
                                    placeholder="0.00"
                                    class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                >
                                @error('budget_min')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="budget_max" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
                                    Max Budget ($) <span class="text-surface-400">(optional)</span>
                                </label>
                                <input
                                    type="number"
                                    name="budget_max"
                                    id="budget_max"
                                    value="{{ old('budget_max') }}"
                                    min="0"
                                    step="0.01"
                                    placeholder="0.00"
                                    class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                >
                                @error('budget_max')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="urgency" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
                                    Urgency <span class="text-danger-500">*</span>
                                </label>
                                <select
                                    name="urgency"
                                    id="urgency"
                                    required
                                    class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                >
                                    <option value="low" {{ old('urgency') == 'low' ? 'selected' : '' }}>Low - No rush</option>
                                    <option value="normal" {{ old('urgency', 'normal') == 'normal' ? 'selected' : '' }}>Normal - Within a month</option>
                                    <option value="high" {{ old('urgency') == 'high' ? 'selected' : '' }}>High - Within 2 weeks</option>
                                    <option value="urgent" {{ old('urgency') == 'urgent' ? 'selected' : '' }}>Urgent - ASAP</option>
                                </select>
                                @error('urgency')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="pb-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Additional Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="reference_urls" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
                                    Reference URLs <span class="text-surface-400">(optional)</span>
                                </label>
                                <textarea
                                    name="reference_urls"
                                    id="reference_urls"
                                    rows="2"
                                    placeholder="Add links to similar products or examples (one per line)"
                                    class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                >{{ old('reference_urls') }}</textarea>
                                @error('reference_urls')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
                                    Attachments <span class="text-surface-400">(optional)</span>
                                </label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-surface-300 dark:border-surface-600 border-dashed rounded-lg hover:border-primary-400 transition-colors">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-surface-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-surface-600 dark:text-surface-400">
                                            <label for="attachments" class="relative cursor-pointer rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none">
                                                <span>Upload files</span>
                                                <input id="attachments" name="attachments[]" type="file" class="sr-only" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.zip">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-surface-500">PNG, JPG, PDF, DOC, ZIP up to 5MB each</p>
                                    </div>
                                </div>
                                @error('attachments.*')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-between pt-4 border-t border-surface-200 dark:border-surface-700">
                        <a href="{{ route('products.index') }}" class="text-sm text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white">
                            &larr; Back to Products
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
