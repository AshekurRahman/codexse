<x-layouts.app title="Add Product">
    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('seller.products.index') }}" class="inline-flex items-center gap-2 text-surface-600 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Products
                </a>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Add New Product</h1>
                <p class="text-surface-600 dark:text-surface-400 mt-1">Fill in the details below to list your product</p>
            </div>

            <!-- Form -->
            <form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 space-y-6">
                    <h2 class="text-lg font-semibold text-surface-900 dark:text-white pb-4 border-b border-surface-200 dark:border-surface-700">Basic Information</h2>

                    <div>
                        <label for="name" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Product Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white placeholder-surface-400">
                        @error('name')
                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="short_description" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Short Description</label>
                        <input type="text" id="short_description" name="short_description" value="{{ old('short_description') }}" maxlength="500" class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white placeholder-surface-400" placeholder="Brief description for listings">
                        @error('short_description')
                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Full Description *</label>
                        <textarea id="description" name="description" rows="6" required class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white placeholder-surface-400" placeholder="Detailed description of your product">{{ old('description') }}</textarea>
                        @error('description')
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
                </div>

                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 space-y-6">
                    <h2 class="text-lg font-semibold text-surface-900 dark:text-white pb-4 border-b border-surface-200 dark:border-surface-700">Pricing</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="price" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Price ($) *</label>
                            <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" required class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white" placeholder="0.00">
                            @error('price')
                                <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sale_price" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Sale Price ($)</label>
                            <input type="number" id="sale_price" name="sale_price" value="{{ old('sale_price') }}" step="0.01" min="0" class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white" placeholder="0.00">
                            @error('sale_price')
                                <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 space-y-6">
                    <h2 class="text-lg font-semibold text-surface-900 dark:text-white pb-4 border-b border-surface-200 dark:border-surface-700">Files</h2>

                    <div>
                        <label for="thumbnail" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Thumbnail Image *</label>
                        <input type="file" id="thumbnail" name="thumbnail" accept="image/*" required class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 dark:file:bg-primary-900/30 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-primary-900/50">
                        <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">Recommended size: 800x600px. Max 2MB.</p>
                        @error('thumbnail')
                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="file" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Product File *</label>
                        <input type="file" id="file" name="file" required class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 dark:file:bg-primary-900/30 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-primary-900/50">
                        <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">Upload a ZIP file. Max 100MB.</p>
                        @error('file')
                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 space-y-6">
                    <h2 class="text-lg font-semibold text-surface-900 dark:text-white pb-4 border-b border-surface-200 dark:border-surface-700">Preview & Demo Links</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="demo_url" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                                Live Demo URL
                                <span class="text-surface-400 font-normal">(optional)</span>
                            </label>
                            <input type="url" id="demo_url" name="demo_url" value="{{ old('demo_url') }}" class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white placeholder-surface-400" placeholder="https://demo.example.com">
                            <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">Link to a live demo of your product</p>
                            @error('demo_url')
                                <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="preview_url" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                                Preview URL
                                <span class="text-surface-400 font-normal">(optional)</span>
                            </label>
                            <input type="url" id="preview_url" name="preview_url" value="{{ old('preview_url') }}" class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white placeholder-surface-400" placeholder="https://preview.example.com">
                            <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">Link to preview images or documentation</p>
                            @error('preview_url')
                                <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('seller.products.index') }}" class="px-6 py-2.5 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">Submit for Review</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
