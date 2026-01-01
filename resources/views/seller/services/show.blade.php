<x-layouts.app title="{{ $service->name }}">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('seller.services.index') }}" class="inline-flex items-center gap-2 text-surface-600 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Services
                </a>
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-bold text-surface-900 dark:text-white">{{ $service->name }}</h1>
                            <x-status-badge :status="$service->status" />
                        </div>
                        <p class="text-surface-600 dark:text-surface-400 mt-1">{{ $service->category->name ?? 'Uncategorized' }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('services.show', $service->slug) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            View Live
                        </a>
                        <a href="{{ route('seller.services.edit', $service) }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Service
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Thumbnail -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                        @if($service->thumbnail)
                            <img src="{{ Storage::url($service->thumbnail) }}" alt="{{ $service->name }}" class="w-full h-64 object-cover">
                        @else
                            <div class="w-full h-64 bg-surface-100 dark:bg-surface-700 flex items-center justify-center">
                                <svg class="w-16 h-16 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Description -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Description</h2>
                        <div class="prose prose-sm dark:prose-invert max-w-none text-surface-600 dark:text-surface-400">
                            {!! nl2br(e($service->description)) !!}
                        </div>
                    </div>

                    <!-- Packages -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Pricing Packages</h2>

                        @if($service->packages->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-{{ min($service->packages->count(), 3) }} gap-4">
                                @foreach($service->packages as $package)
                                    <div class="border {{ $package->tier === 'standard' ? 'border-primary-300 dark:border-primary-700 bg-primary-50/50 dark:bg-primary-900/10' : 'border-surface-200 dark:border-surface-700' }} rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <h3 class="font-semibold text-surface-900 dark:text-white">{{ $package->name }}</h3>
                                            @if($package->tier === 'standard')
                                                <span class="text-xs bg-primary-100 text-primary-700 dark:bg-primary-900/50 dark:text-primary-300 px-2 py-0.5 rounded">Popular</span>
                                            @endif
                                        </div>
                                        <p class="text-2xl font-bold text-surface-900 dark:text-white mb-4">{{ format_price($package->price) }}</p>
                                        <ul class="space-y-2 text-sm text-surface-600 dark:text-surface-400">
                                            <li class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                {{ $package->delivery_days }} day{{ $package->delivery_days > 1 ? 's' : '' }} delivery
                                            </li>
                                            <li class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                {{ $package->revisions }} revision{{ $package->revisions != 1 ? 's' : '' }}
                                            </li>
                                            @if($package->deliverables)
                                                @foreach(is_array($package->deliverables) ? $package->deliverables : explode("\n", $package->deliverables) as $item)
                                                    @if(trim($item))
                                                        <li class="flex items-center gap-2">
                                                            <svg class="w-4 h-4 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                            {{ trim($item) }}
                                                        </li>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-surface-500 dark:text-surface-400">No packages configured yet.</p>
                        @endif
                    </div>

                    <!-- Requirements -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Buyer Requirements</h2>

                        @if($service->requirements->count() > 0)
                            <div class="space-y-3">
                                @foreach($service->requirements as $requirement)
                                    <div class="flex items-start gap-3 p-3 bg-surface-50 dark:bg-surface-900/50 rounded-lg">
                                        <div class="flex-1">
                                            <p class="text-surface-900 dark:text-white">{{ $requirement->question }}</p>
                                            <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">
                                                Type: {{ ucfirst($requirement->type) }}
                                                @if($requirement->is_required)
                                                    <span class="text-danger-600 dark:text-danger-400">(Required)</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-surface-500 dark:text-surface-400">No requirements configured.</p>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Stats -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Statistics</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Total Orders</span>
                                <span class="font-semibold text-surface-900 dark:text-white">{{ $service->orders_count ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Active Orders</span>
                                <span class="font-semibold text-surface-900 dark:text-white">{{ $service->orders()->whereIn('status', ['pending', 'in_progress'])->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Completed</span>
                                <span class="font-semibold text-success-600 dark:text-success-400">{{ $service->orders()->where('status', 'completed')->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Total Earnings</span>
                                <span class="font-semibold text-surface-900 dark:text-white">{{ format_price($service->orders()->where('status', 'completed')->sum('price')) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Views</span>
                                <span class="font-semibold text-surface-900 dark:text-white">{{ number_format($service->views ?? 0) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Info -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Quick Info</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Created</span>
                                <span class="text-surface-900 dark:text-white">{{ $service->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Last Updated</span>
                                <span class="text-surface-900 dark:text-white">{{ $service->updated_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Starting Price</span>
                                <span class="text-surface-900 dark:text-white">{{ format_price($service->packages->min('price') ?? 0) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Packages</span>
                                <span class="text-surface-900 dark:text-white">{{ $service->packages->count() }}</span>
                            </div>
                            @if($service->is_featured)
                                <div class="flex items-center justify-between">
                                    <span class="text-surface-600 dark:text-surface-400">Featured</span>
                                    <span class="inline-flex items-center gap-1 text-warning-600 dark:text-warning-400">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        Yes
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-surface-900 dark:text-white">Recent Orders</h3>
                            <a href="{{ route('seller.service-orders.index') }}?service={{ $service->id }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400">View All</a>
                        </div>

                        @if($service->orders->count() > 0)
                            <div class="space-y-3">
                                @foreach($service->orders->take(5) as $order)
                                    <a href="{{ route('seller.service-orders.show', $order) }}" class="block p-3 bg-surface-50 dark:bg-surface-900/50 rounded-lg hover:bg-surface-100 dark:hover:bg-surface-700 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium text-surface-900 dark:text-white">#{{ $order->order_number }}</span>
                                            <x-status-badge :status="$order->status" size="sm" />
                                        </div>
                                        <div class="flex items-center justify-between mt-1 text-sm text-surface-500 dark:text-surface-400">
                                            <span>{{ $order->buyer->name }}</span>
                                            <span>{{ format_price($order->price) }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-surface-500 dark:text-surface-400 text-center py-4">No orders yet</p>
                        @endif
                    </div>

                    <!-- Danger Zone -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-danger-200 dark:border-danger-800 p-6">
                        <h3 class="font-semibold text-danger-600 dark:text-danger-400 mb-4">Danger Zone</h3>
                        <p class="text-sm text-surface-600 dark:text-surface-400 mb-4">Deleting this service will remove all associated data. This action cannot be undone.</p>
                        <form action="{{ route('seller.services.destroy', $service) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this service? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-danger-600 hover:bg-danger-700 text-white font-medium rounded-lg transition-colors">Delete Service</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
