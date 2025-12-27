<x-layouts.app title="My Product Requests - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">My Product Requests</h1>
                    <p class="mt-1 text-surface-600 dark:text-surface-400">Track the status of your product requests</p>
                </div>
                <a href="{{ route('product-request.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Request
                </a>
            </div>

            @if($requests->count() > 0)
                <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-surface-200 dark:divide-surface-700">
                            <thead class="bg-surface-50 dark:bg-surface-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider">Budget</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider">Urgency</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-200 dark:divide-surface-700">
                                @foreach($requests as $request)
                                    <tr class="hover:bg-surface-50 dark:hover:bg-surface-700/50">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-surface-900 dark:text-white">{{ $request->product_title }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm text-surface-600 dark:text-surface-400">
                                                {{ $request->category?->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm text-surface-600 dark:text-surface-400">
                                                {{ $request->budget_range ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($request->urgency === 'urgent') bg-danger-100 text-danger-800 dark:bg-danger-900/30 dark:text-danger-400
                                                @elseif($request->urgency === 'high') bg-warning-100 text-warning-800 dark:bg-warning-900/30 dark:text-warning-400
                                                @elseif($request->urgency === 'normal') bg-info-100 text-info-800 dark:bg-info-900/30 dark:text-info-400
                                                @else bg-surface-100 text-surface-800 dark:bg-surface-700 dark:text-surface-400
                                                @endif
                                            ">
                                                {{ ucfirst($request->urgency) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($request->status === 'fulfilled') bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-400
                                                @elseif($request->status === 'approved') bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400
                                                @elseif($request->status === 'reviewing') bg-info-100 text-info-800 dark:bg-info-900/30 dark:text-info-400
                                                @elseif($request->status === 'rejected') bg-danger-100 text-danger-800 dark:bg-danger-900/30 dark:text-danger-400
                                                @elseif($request->status === 'closed') bg-surface-100 text-surface-800 dark:bg-surface-700 dark:text-surface-400
                                                @else bg-warning-100 text-warning-800 dark:bg-warning-900/30 dark:text-warning-400
                                                @endif
                                            ">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm text-surface-600 dark:text-surface-400">
                                                {{ $request->created_at->format('M d, Y') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('product-request.show', $request) }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 text-sm font-medium">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6">
                    {{ $requests->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-surface-900 dark:text-white">No requests yet</h3>
                    <p class="mt-2 text-surface-600 dark:text-surface-400">You haven't submitted any product requests.</p>
                    <a href="{{ route('product-request.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                        Submit Your First Request
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
