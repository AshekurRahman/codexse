<x-layouts.app title="Compare Products - {{ config('app.name') }}">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-primary-50 via-white to-accent-50 dark:from-surface-900 dark:via-surface-900 dark:to-surface-800">
        <div class="absolute top-0 left-0 w-96 h-96 bg-primary-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 dark:opacity-10 -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-accent-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 dark:opacity-10 translate-x-1/2 -translate-y-1/2"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 rounded-full mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="font-semibold">Product Comparison</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold text-surface-900 dark:text-white">
                        Compare Products
                    </h1>
                    <p class="mt-2 text-surface-600 dark:text-surface-400">
                        {{ $products->count() }} {{ Str::plural('product', $products->count()) }} selected for comparison
                    </p>
                </div>

                @if($products->count() > 0)
                <div class="flex items-center gap-3">
                    <button
                        onclick="clearAllCompare()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-surface-700 dark:text-surface-300 font-medium rounded-xl hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors shadow-sm"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Clear All
                    </button>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition-colors shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add More
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Comparison Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if($products->count() > 0)
            <!-- Comparison Table -->
            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-lg shadow-surface-200/50 dark:shadow-none border border-surface-100 dark:border-surface-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <!-- Product Headers -->
                        <thead>
                            <tr class="border-b border-surface-200 dark:border-surface-700">
                                <th class="w-48 p-6 text-left bg-surface-50 dark:bg-surface-700/50">
                                    <span class="text-sm font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">Product</span>
                                </th>
                                @foreach($products as $product)
                                <th class="p-6 min-w-[280px]">
                                    <div class="relative group">
                                        <!-- Remove Button -->
                                        <button
                                            onclick="removeFromCompare({{ $product->id }})"
                                            class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center hover:bg-red-600"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>

                                        <!-- Product Image -->
                                        <a href="{{ route('products.show', $product) }}" class="block mb-4">
                                            <div class="aspect-[4/3] rounded-xl overflow-hidden bg-surface-100 dark:bg-surface-700">
                                                <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                            </div>
                                        </a>

                                        <!-- Product Name -->
                                        <a href="{{ route('products.show', $product) }}" class="font-semibold text-surface-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 transition-colors line-clamp-2">
                                            {{ $product->name }}
                                        </a>
                                    </div>
                                </th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-surface-200 dark:divide-surface-700">
                            <!-- Price -->
                            <tr>
                                <td class="p-6 bg-surface-50 dark:bg-surface-700/50">
                                    <span class="font-medium text-surface-700 dark:text-surface-300">Price</span>
                                </td>
                                @foreach($products as $product)
                                <td class="p-6 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        @if($product->isOnSale())
                                            <span class="text-2xl font-bold text-green-600 dark:text-green-400">${{ number_format($product->sale_price, 2) }}</span>
                                            <span class="text-sm text-surface-400 line-through">${{ number_format($product->price, 2) }}</span>
                                        @else
                                            <span class="text-2xl font-bold text-surface-900 dark:text-white">${{ number_format($product->price, 2) }}</span>
                                        @endif
                                    </div>
                                </td>
                                @endforeach
                            </tr>

                            <!-- Category -->
                            <tr>
                                <td class="p-6 bg-surface-50 dark:bg-surface-700/50">
                                    <span class="font-medium text-surface-700 dark:text-surface-300">Category</span>
                                </td>
                                @foreach($products as $product)
                                <td class="p-6 text-center">
                                    @if($product->category)
                                        <a href="{{ route('categories.show', $product->category) }}" class="inline-flex items-center px-3 py-1.5 bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 rounded-full text-sm font-medium hover:bg-primary-200 dark:hover:bg-primary-900 transition-colors">
                                            {{ $product->category->name }}
                                        </a>
                                    @else
                                        <span class="text-surface-400">-</span>
                                    @endif
                                </td>
                                @endforeach
                            </tr>

                            <!-- Seller -->
                            <tr>
                                <td class="p-6 bg-surface-50 dark:bg-surface-700/50">
                                    <span class="font-medium text-surface-700 dark:text-surface-300">Seller</span>
                                </td>
                                @foreach($products as $product)
                                <td class="p-6 text-center">
                                    @if($product->seller)
                                        <a href="{{ route('sellers.show', $product->seller) }}" class="inline-flex items-center gap-2 text-surface-700 dark:text-surface-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                            <img src="{{ $product->seller->user->avatar_url ?? '' }}" alt="{{ $product->seller->business_name }}" class="w-6 h-6 rounded-full">
                                            <span class="font-medium">{{ $product->seller->business_name }}</span>
                                        </a>
                                    @else
                                        <span class="text-surface-400">-</span>
                                    @endif
                                </td>
                                @endforeach
                            </tr>

                            <!-- Rating -->
                            <tr>
                                <td class="p-6 bg-surface-50 dark:bg-surface-700/50">
                                    <span class="font-medium text-surface-700 dark:text-surface-300">Rating</span>
                                </td>
                                @foreach($products as $product)
                                <td class="p-6 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <div class="flex items-center gap-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-5 h-5 {{ $i <= round($product->average_rating) ? 'text-amber-400' : 'text-surface-300 dark:text-surface-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="text-sm text-surface-500">{{ number_format($product->average_rating, 1) }} ({{ $product->reviews_count }} reviews)</span>
                                    </div>
                                </td>
                                @endforeach
                            </tr>

                            <!-- Sales -->
                            <tr>
                                <td class="p-6 bg-surface-50 dark:bg-surface-700/50">
                                    <span class="font-medium text-surface-700 dark:text-surface-300">Sales</span>
                                </td>
                                @foreach($products as $product)
                                <td class="p-6 text-center">
                                    <span class="text-surface-700 dark:text-surface-300 font-medium">{{ number_format($product->sales_count) }}</span>
                                </td>
                                @endforeach
                            </tr>

                            <!-- Version -->
                            <tr>
                                <td class="p-6 bg-surface-50 dark:bg-surface-700/50">
                                    <span class="font-medium text-surface-700 dark:text-surface-300">Version</span>
                                </td>
                                @foreach($products as $product)
                                <td class="p-6 text-center">
                                    <span class="text-surface-700 dark:text-surface-300">{{ $product->version ?? '-' }}</span>
                                </td>
                                @endforeach
                            </tr>

                            <!-- Software Compatibility -->
                            @if(count($allCompatibility) > 0)
                            <tr>
                                <td class="p-6 bg-surface-50 dark:bg-surface-700/50">
                                    <span class="font-medium text-surface-700 dark:text-surface-300">Compatibility</span>
                                </td>
                                @foreach($products as $product)
                                <td class="p-6">
                                    <div class="flex flex-wrap justify-center gap-2">
                                        @if($product->software_compatibility && count($product->software_compatibility) > 0)
                                            @foreach($product->software_compatibility as $software)
                                                <span class="px-2.5 py-1 bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-400 rounded-lg text-xs font-medium">
                                                    {{ $software }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-surface-400">-</span>
                                        @endif
                                    </div>
                                </td>
                                @endforeach
                            </tr>
                            @endif

                            <!-- License Types -->
                            @if(count($allLicenseTypes) > 0)
                            <tr>
                                <td class="p-6 bg-surface-50 dark:bg-surface-700/50">
                                    <span class="font-medium text-surface-700 dark:text-surface-300">License Options</span>
                                </td>
                                @foreach($products as $product)
                                <td class="p-6">
                                    <div class="flex flex-wrap justify-center gap-2">
                                        @if($product->license_types && count($product->license_types) > 0)
                                            @foreach($product->license_types as $type => $price)
                                                <span class="px-2.5 py-1 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 rounded-lg text-xs font-medium">
                                                    {{ ucfirst($type) }}: ${{ number_format($price, 2) }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-surface-400">-</span>
                                        @endif
                                    </div>
                                </td>
                                @endforeach
                            </tr>
                            @endif

                            <!-- Demo/Preview -->
                            <tr>
                                <td class="p-6 bg-surface-50 dark:bg-surface-700/50">
                                    <span class="font-medium text-surface-700 dark:text-surface-300">Preview</span>
                                </td>
                                @foreach($products as $product)
                                <td class="p-6 text-center">
                                    @if($product->preview_url || $product->demo_url)
                                        <a href="{{ $product->preview_url ?? $product->demo_url }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 bg-accent-100 dark:bg-accent-900/50 text-accent-700 dark:text-accent-300 rounded-full text-sm font-medium hover:bg-accent-200 dark:hover:bg-accent-900 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Live Preview
                                        </a>
                                    @else
                                        <span class="text-surface-400">-</span>
                                    @endif
                                </td>
                                @endforeach
                            </tr>

                            <!-- Actions -->
                            <tr>
                                <td class="p-6 bg-surface-50 dark:bg-surface-700/50">
                                    <span class="font-medium text-surface-700 dark:text-surface-300">Actions</span>
                                </td>
                                @foreach($products as $product)
                                <td class="p-6">
                                    <div class="flex flex-col items-center gap-3">
                                        <form action="{{ route('cart.add', $product) }}" method="POST" class="w-full">
                                            @csrf
                                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                Add to Cart
                                            </button>
                                        </form>
                                        <a href="{{ route('products.show', $product) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-300 font-medium rounded-xl hover:bg-surface-200 dark:hover:bg-surface-600 transition-colors">
                                            View Details
                                        </a>
                                    </div>
                                </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-20 bg-white dark:bg-surface-800 rounded-3xl shadow-lg shadow-surface-200/50 dark:shadow-none border border-surface-100 dark:border-surface-700">
                <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-surface-100 dark:bg-surface-700 flex items-center justify-center">
                    <svg class="w-10 h-10 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-surface-900 dark:text-white mb-2">No products to compare</h3>
                <p class="text-surface-500 dark:text-surface-400 mb-6 max-w-md mx-auto">Add products to your comparison list by clicking the compare icon on any product card.</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Browse Products
                </a>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        const baseUrl = '{{ url('/') }}';

        function removeFromCompare(productId) {
            fetch(baseUrl + `/compare/remove/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
        }

        function clearAllCompare() {
            fetch(baseUrl + '/compare/clear', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
        }
    </script>
    @endpush
</x-layouts.app>
