<x-layouts.app title="My Services">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">My Services</h1>
                    <p class="text-surface-600 dark:text-surface-400 mt-1">Manage your gig-based services</p>
                </div>
                <a href="{{ route('seller.services.create') }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Service
                </a>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-success-50 dark:bg-success-900/30 border border-success-200 dark:border-success-800 rounded-lg text-success-700 dark:text-success-300">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <p class="text-sm text-surface-500 dark:text-surface-400">Total Services</p>
                    <p class="text-2xl font-bold text-surface-900 dark:text-white mt-1">{{ $services->total() }}</p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <p class="text-sm text-surface-500 dark:text-surface-400">Published</p>
                    <p class="text-2xl font-bold text-success-600 dark:text-success-400 mt-1">{{ $publishedCount ?? 0 }}</p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <p class="text-sm text-surface-500 dark:text-surface-400">Pending Review</p>
                    <p class="text-2xl font-bold text-warning-600 dark:text-warning-400 mt-1">{{ $pendingCount ?? 0 }}</p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <p class="text-sm text-surface-500 dark:text-surface-400">Active Orders</p>
                    <p class="text-2xl font-bold text-primary-600 dark:text-primary-400 mt-1">{{ $activeOrdersCount ?? 0 }}</p>
                </div>
            </div>

            <!-- Services Table -->
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                @if($services->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-surface-50 dark:bg-surface-900/50">
                                <tr>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Service</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Category</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Starting Price</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Status</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Orders</th>
                                    <th class="text-right text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-200 dark:divide-surface-700">
                                @foreach($services as $service)
                                    <tr class="hover:bg-surface-50 dark:hover:bg-surface-700/50">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                <div class="w-16 h-12 rounded-lg bg-surface-100 dark:bg-surface-700 overflow-hidden shrink-0">
                                                    @if($service->thumbnail)
                                                        <img src="{{ Storage::url($service->thumbnail) }}" alt="" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center">
                                                            <svg class="w-6 h-6 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <a href="{{ route('seller.services.show', $service) }}" class="font-medium text-surface-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 truncate max-w-[200px] block">{{ $service->name }}</a>
                                                    <p class="text-sm text-surface-500 dark:text-surface-400">{{ $service->packages->count() }} packages</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-surface-600 dark:text-surface-400">
                                            {{ $service->category->name ?? 'Uncategorized' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $startingPrice = $service->packages->min('price');
                                            @endphp
                                            <span class="font-semibold text-surface-900 dark:text-white">
                                                ${{ number_format($startingPrice ?? 0, 2) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <x-status-badge :status="$service->status" />
                                        </td>
                                        <td class="px-6 py-4 text-surface-600 dark:text-surface-400">
                                            {{ $service->orders_count ?? 0 }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('seller.services.show', $service) }}" class="p-2 text-surface-500 hover:text-primary-600 dark:text-surface-400 dark:hover:text-primary-400 transition-colors" title="View">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('seller.services.edit', $service) }}" class="p-2 text-surface-500 hover:text-primary-600 dark:text-surface-400 dark:hover:text-primary-400 transition-colors" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('seller.services.destroy', $service) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this service?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 text-surface-500 hover:text-danger-600 dark:text-surface-400 dark:hover:text-danger-400 transition-colors" title="Delete">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($services->hasPages())
                        <div class="px-6 py-4 border-t border-surface-200 dark:border-surface-700">
                            {{ $services->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">No services yet</h3>
                        <p class="text-surface-600 dark:text-surface-400 mb-6">Create your first service to start getting orders</p>
                        <a href="{{ route('seller.services.create') }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Service
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
