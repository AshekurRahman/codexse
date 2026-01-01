<x-layouts.app title="Seller Application - Codexse">
    <section class="bg-surface-50 dark:bg-surface-900 min-h-screen py-12">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <a href="{{ route('become-seller') }}" class="inline-flex items-center text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 mb-4">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Seller Info
                </a>
                <h1 class="text-3xl font-bold text-surface-900 dark:text-white">Seller Application</h1>
                <p class="mt-2 text-surface-600 dark:text-surface-400">Tell us about yourself and what you plan to sell</p>
            </div>

            <!-- Application Form -->
            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-6 sm:p-8">
                <form action="{{ route('seller.apply.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" data-ajax-form>
                    @csrf

                    <!-- Store Name -->
                    <div>
                        <label for="store_name" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                            Store Name <span class="text-danger-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="store_name"
                            name="store_name"
                            value="{{ old('store_name', $existingApplication?->store_name) }}"
                            placeholder="e.g., DesignStudio Pro"
                            class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500 focus:ring-primary-500"
                            data-validate="required|min:2"
                        >
                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">This will be your public store name on Codexse</p>
                        @error('store_name')
                            <p class="mt-1 text-sm text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Store Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                            Store Description <span class="text-danger-500">*</span>
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            placeholder="Tell us about your store, what products you create, and your design philosophy..."
                            class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500 focus:ring-primary-500"
                            data-validate="required|min:50"
                        >{{ old('description', $existingApplication?->description) }}</textarea>
                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Minimum 50 characters. This will appear on your store page.</p>
                        @error('description')
                            <p class="mt-1 text-sm text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Product Types -->
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                            What will you sell? <span class="text-danger-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach([
                                'ui-kits' => 'UI Kits',
                                'templates' => 'Templates',
                                'icons' => 'Icons',
                                'illustrations' => 'Illustrations',
                                'themes' => 'Themes',
                                'code' => 'Code & Scripts',
                                'fonts' => 'Fonts',
                                'mockups' => 'Mockups',
                                'other' => 'Other',
                            ] as $value => $label)
                                <label class="relative flex items-center justify-center p-3 rounded-lg border border-surface-300 dark:border-surface-600 cursor-pointer hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 dark:has-[:checked]:bg-primary-900/20">
                                    <input
                                        type="checkbox"
                                        name="product_types[]"
                                        value="{{ $value }}"
                                        class="sr-only"
                                        {{ in_array($value, old('product_types', [])) ? 'checked' : '' }}
                                    >
                                    <span class="text-sm font-medium text-surface-700 dark:text-surface-300">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('product_types')
                            <p class="mt-1 text-sm text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Experience Level -->
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                            Experience Level <span class="text-danger-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach([
                                'beginner' => ['label' => 'Beginner', 'desc' => '0-2 years'],
                                'intermediate' => ['label' => 'Intermediate', 'desc' => '2-5 years'],
                                'expert' => ['label' => 'Expert', 'desc' => '5+ years'],
                            ] as $value => $data)
                                <label class="relative flex flex-col items-center p-4 rounded-lg border border-surface-300 dark:border-surface-600 cursor-pointer hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 dark:has-[:checked]:bg-primary-900/20">
                                    <input
                                        type="radio"
                                        name="experience"
                                        value="{{ $value }}"
                                        class="sr-only"
                                        {{ old('experience') === $value ? 'checked' : '' }}
                                        required
                                    >
                                    <span class="text-sm font-medium text-surface-700 dark:text-surface-300">{{ $data['label'] }}</span>
                                    <span class="text-xs text-surface-500 dark:text-surface-400">{{ $data['desc'] }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('experience')
                            <p class="mt-1 text-sm text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Logo Upload -->
                    <div>
                        <label for="logo" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                            Store Logo
                        </label>
                        <div class="flex items-center gap-4">
                            <div class="shrink-0">
                                <div class="h-16 w-16 rounded-xl bg-surface-100 dark:bg-surface-700 flex items-center justify-center overflow-hidden" id="logo-preview">
                                    @if($existingApplication?->logo)
                                        <img src="{{ $existingApplication->logo_url }}" alt="Current logo" class="h-full w-full object-cover">
                                    @else
                                        <svg class="h-8 w-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1">
                                <input
                                    type="file"
                                    id="logo"
                                    name="logo"
                                    accept="image/jpeg,image/png,image/webp"
                                    class="block w-full text-sm text-surface-500 dark:text-surface-400
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-lg file:border-0
                                        file:text-sm file:font-medium
                                        file:bg-primary-50 file:text-primary-600
                                        dark:file:bg-primary-900/30 dark:file:text-primary-400
                                        hover:file:bg-primary-100 dark:hover:file:bg-primary-900/50
                                        cursor-pointer"
                                    onchange="previewLogo(this)"
                                >
                                <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">JPG, PNG or WebP. Max 2MB. Recommended: 200x200px</p>
                            </div>
                        </div>
                        @error('logo')
                            <p class="mt-1 text-sm text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Website -->
                    <div>
                        <label for="website" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                            Website / Portfolio URL
                        </label>
                        <input
                            type="url"
                            id="website"
                            name="website"
                            value="{{ old('website', $existingApplication?->website) }}"
                            placeholder="https://yourportfolio.com"
                            class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500 focus:ring-primary-500"
                        >
                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Optional. Link to your portfolio or existing work</p>
                        @error('website')
                            <p class="mt-1 text-sm text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Portfolio URL -->
                    <div>
                        <label for="portfolio_url" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                            Dribbble / Behance Profile
                        </label>
                        <input
                            type="url"
                            id="portfolio_url"
                            name="portfolio_url"
                            value="{{ old('portfolio_url') }}"
                            placeholder="https://dribbble.com/yourprofile"
                            class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500 focus:ring-primary-500"
                        >
                        @error('portfolio_url')
                            <p class="mt-1 text-sm text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Terms Agreement -->
                    <div class="bg-surface-50 dark:bg-surface-700/50 rounded-lg p-4">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                name="terms"
                                value="1"
                                class="mt-1 rounded border-surface-300 dark:border-surface-600 text-primary-600 focus:ring-primary-500"
                                required
                            >
                            <span class="text-sm text-surface-600 dark:text-surface-400">
                                I agree to the <a href="{{ route('terms') }}" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline">Seller Terms and Conditions</a> and understand that Codexse takes a commission on each sale. I confirm that I own or have the rights to sell the products I will upload.
                            </span>
                        </label>
                        @error('terms')
                            <p class="mt-1 text-sm text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-primary-600 to-primary-500 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-primary-500/30 hover:shadow-primary-500/40 transition-all hover:-translate-y-0.5"
                        >
                            Submit Application
                            <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Help Box -->
            <div class="mt-6 bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-xl p-4">
                <div class="flex gap-3">
                    <svg class="h-5 w-5 text-primary-600 dark:text-primary-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h4 class="font-medium text-primary-800 dark:text-primary-200">Need help?</h4>
                        <p class="text-sm text-primary-700 dark:text-primary-300 mt-1">
                            If you have questions about becoming a seller, check our <a href="{{ route('become-seller') }}#how-it-works" class="underline">FAQ section</a> or contact us at <a href="mailto:sellers@codexse.com" class="underline">sellers@codexse.com</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function previewLogo(input) {
            const preview = document.getElementById('logo-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Logo preview" class="h-full w-full object-cover">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-layouts.app>
