<x-layouts.app title="Edit Product">
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
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Edit Product</h1>
                <p class="text-surface-600 dark:text-surface-400 mt-1">Update your product details</p>
            </div>

            <!-- Form -->
            <form action="{{ route('seller.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 space-y-6">
                    <h2 class="text-lg font-semibold text-surface-900 dark:text-white pb-4 border-b border-surface-200 dark:border-surface-700">Basic Information</h2>

                    <div>
                        <label for="name" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Product Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white placeholder-surface-400">
                        @error('name')
                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="short_description" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Short Description</label>
                        <input type="text" id="short_description" name="short_description" value="{{ old('short_description', $product->short_description) }}" maxlength="500" class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white placeholder-surface-400" placeholder="Brief description for listings">
                        @error('short_description')
                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Full Description *</label>
                        <textarea id="description" name="description" rows="6" required class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white placeholder-surface-400">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Category *</label>
                        <select id="category_id" name="category_id" required class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white">
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
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
                            <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white">
                            @error('price')
                                <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sale_price" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Sale Price ($)</label>
                            <input type="number" id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" step="0.01" min="0" class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white">
                            @error('sale_price')
                                <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Product Variations -->
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 space-y-6"
                     x-data="{
                         hasVariations: {{ $product->has_variations ? 'true' : 'false' }},
                         variations: {{ json_encode($product->variations->map(fn($v) => [
                             'id' => $v->id,
                             'name' => $v->name,
                             'description' => $v->description ?? '',
                             'price' => $v->price,
                             'regular_price' => $v->regular_price ?? '',
                             'features' => $v->features ?? [],
                             'license_type' => $v->license_type,
                             'support_months' => $v->support_months,
                             'updates_months' => $v->updates_months,
                             'is_default' => $v->is_default,
                             'is_active' => $v->is_active,
                         ])->toArray()) }},
                         newFeature: '',
                         addVariation() {
                             this.variations.push({
                                 id: null,
                                 name: '',
                                 description: '',
                                 price: '',
                                 regular_price: '',
                                 features: [],
                                 license_type: 'regular',
                                 support_months: 6,
                                 updates_months: 12,
                                 is_default: this.variations.length === 0,
                                 is_active: true
                             });
                         },
                         removeVariation(index) {
                             this.variations.splice(index, 1);
                             if (this.variations.length > 0 && !this.variations.some(v => v.is_default)) {
                                 this.variations[0].is_default = true;
                             }
                         },
                         setDefault(index) {
                             this.variations.forEach((v, i) => v.is_default = i === index);
                         },
                         addFeature(index) {
                             if (this.newFeature.trim()) {
                                 this.variations[index].features.push(this.newFeature.trim());
                                 this.newFeature = '';
                             }
                         },
                         removeFeature(varIndex, featIndex) {
                             this.variations[varIndex].features.splice(featIndex, 1);
                         }
                     }">
                    <div class="flex items-center justify-between pb-4 border-b border-surface-200 dark:border-surface-700">
                        <div>
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white">Product Variations</h2>
                            <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">Offer multiple versions/tiers (e.g., Basic, Pro, Enterprise)</p>
                        </div>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <span class="text-sm text-surface-600 dark:text-surface-400">Enable Variations</span>
                            <input type="checkbox" name="has_variations" value="1" x-model="hasVariations" class="w-5 h-5 rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                        </label>
                    </div>

                    <template x-if="hasVariations">
                        <div class="space-y-6">
                            <template x-for="(variation, index) in variations" :key="index">
                                <div class="border border-surface-200 dark:border-surface-700 rounded-lg p-4 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-medium text-surface-900 dark:text-white" x-text="variation.name || 'New Variation'"></h3>
                                        <div class="flex items-center gap-2">
                                            <button type="button" @click="setDefault(index)" class="text-xs px-2 py-1 rounded" :class="variation.is_default ? 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400' : 'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-400 hover:bg-surface-200'">
                                                <span x-text="variation.is_default ? 'Default' : 'Set as Default'"></span>
                                            </button>
                                            <button type="button" @click="removeVariation(index)" class="text-danger-600 hover:text-danger-700 dark:text-danger-400">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <input type="hidden" :name="'variations[' + index + '][id]'" :value="variation.id">
                                    <input type="hidden" :name="'variations[' + index + '][is_default]'" :value="variation.is_default ? 1 : 0">
                                    <input type="hidden" :name="'variations[' + index + '][is_active]'" :value="variation.is_active ? 1 : 0">
                                    <input type="hidden" :name="'variations[' + index + '][sort_order]'" :value="index">

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Name *</label>
                                            <input type="text" :name="'variations[' + index + '][name]'" x-model="variation.name" required class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-surface-900 dark:text-white text-sm" placeholder="e.g., Basic, Pro, Enterprise">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">License Type</label>
                                            <select :name="'variations[' + index + '][license_type]'" x-model="variation.license_type" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-surface-900 dark:text-white text-sm">
                                                <option value="regular">Regular License</option>
                                                <option value="extended">Extended License</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Description</label>
                                        <textarea :name="'variations[' + index + '][description]'" x-model="variation.description" rows="2" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-surface-900 dark:text-white text-sm" placeholder="Brief description of this tier"></textarea>
                                    </div>

                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Price ($) *</label>
                                            <input type="number" :name="'variations[' + index + '][price]'" x-model="variation.price" step="0.01" min="0" required class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-surface-900 dark:text-white text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Regular Price</label>
                                            <input type="number" :name="'variations[' + index + '][regular_price]'" x-model="variation.regular_price" step="0.01" min="0" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-surface-900 dark:text-white text-sm" placeholder="For discounts">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Support (months)</label>
                                            <input type="number" :name="'variations[' + index + '][support_months]'" x-model="variation.support_months" min="0" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-surface-900 dark:text-white text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Updates (months)</label>
                                            <input type="number" :name="'variations[' + index + '][updates_months]'" x-model="variation.updates_months" min="0" class="w-full px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-surface-900 dark:text-white text-sm">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Features</label>
                                        <div class="flex flex-wrap gap-2 mb-2">
                                            <template x-for="(feature, fIndex) in variation.features" :key="fIndex">
                                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 rounded text-sm">
                                                    <span x-text="feature"></span>
                                                    <input type="hidden" :name="'variations[' + index + '][features][]'" :value="feature">
                                                    <button type="button" @click="removeFeature(index, fIndex)" class="hover:text-primary-900">&times;</button>
                                                </span>
                                            </template>
                                        </div>
                                        <div class="flex gap-2">
                                            <input type="text" x-model="newFeature" @keydown.enter.prevent="addFeature(index)" class="flex-1 px-3 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-surface-900 dark:text-white text-sm" placeholder="Add a feature and press Enter">
                                            <button type="button" @click="addFeature(index)" class="px-3 py-2 bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-200 dark:hover:bg-surface-600 text-sm">Add</button>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4 pt-2 border-t border-surface-200 dark:border-surface-700">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" x-model="variation.is_active" class="w-4 h-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                                            <span class="text-sm text-surface-600 dark:text-surface-400">Active</span>
                                        </label>
                                    </div>
                                </div>
                            </template>

                            <button type="button" @click="addVariation()" class="w-full py-3 border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-lg text-surface-600 dark:text-surface-400 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                <span class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Add Variation
                                </span>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 space-y-6">
                    <h2 class="text-lg font-semibold text-surface-900 dark:text-white pb-4 border-b border-surface-200 dark:border-surface-700">Files</h2>

                    <x-file-upload
                        name="thumbnail"
                        label="Thumbnail Image"
                        accept="image/*"
                        :required="false"
                        icon="image"
                        hint="Leave empty to keep current. PNG, JPG or WebP. Max 2MB."
                        maxSize="2MB"
                        :preview="$product->thumbnail ? $product->thumbnail_url : null"
                    />

                    <x-file-upload
                        name="file"
                        label="Product File"
                        accept=".zip,.rar,.7z"
                        :required="false"
                        icon="file"
                        hint="Leave empty to keep current. ZIP, RAR or 7Z. Max 100MB."
                        maxSize="100MB"
                    />
                </div>

                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 space-y-6">
                    <h2 class="text-lg font-semibold text-surface-900 dark:text-white pb-4 border-b border-surface-200 dark:border-surface-700">Preview & Demo Links</h2>

                    <div>
                        <label for="video_url" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                            Product Video URL
                            <span class="text-surface-400 font-normal">(optional)</span>
                        </label>
                        <input type="url" id="video_url" name="video_url" value="{{ old('video_url', $product->video_url) }}" class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white placeholder-surface-400" placeholder="https://youtube.com/watch?v=...">
                        <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">YouTube or Vimeo URL to showcase your product</p>
                        @error('video_url')
                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="demo_url" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                                Live Demo URL
                                <span class="text-surface-400 font-normal">(optional)</span>
                            </label>
                            <input type="url" id="demo_url" name="demo_url" value="{{ old('demo_url', $product->demo_url) }}" class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white placeholder-surface-400" placeholder="https://demo.example.com">
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
                            <input type="url" id="preview_url" name="preview_url" value="{{ old('preview_url', $product->preview_url) }}" class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white placeholder-surface-400" placeholder="https://preview.example.com">
                            <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">Link to preview images or documentation</p>
                            @error('preview_url')
                                <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('seller.products.index') }}" class="px-6 py-2.5 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
