<x-layouts.app title="Support Tickets">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden sticky top-24">
                        <div class="p-4 border-b border-surface-200 dark:border-surface-700">
                            <h2 class="font-semibold text-surface-900 dark:text-white">Navigation</h2>
                        </div>
                        <nav class="p-2">
                            <div class="space-y-1">
                                <p class="px-3 py-2 text-xs font-semibold text-surface-400 dark:text-surface-500 uppercase tracking-wider">Overview</p>
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    Dashboard
                                </a>
                            </div>

                            <div class="space-y-1 mt-4">
                                <p class="px-3 py-2 text-xs font-semibold text-surface-400 dark:text-surface-500 uppercase tracking-wider">Support</p>
                                <a href="{{ route('support.index') }}" class="flex items-center gap-3 px-3 py-2 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 rounded-lg font-medium">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    All Tickets
                                </a>
                                <a href="{{ route('support.create') }}" class="flex items-center gap-3 px-3 py-2 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    New Ticket
                                </a>
                            </div>

                            <div class="space-y-1 mt-4">
                                <p class="px-3 py-2 text-xs font-semibold text-surface-400 dark:text-surface-500 uppercase tracking-wider">Orders</p>
                                <a href="{{ route('purchases') }}" class="flex items-center gap-3 px-3 py-2 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    Product Orders
                                </a>
                                <a href="{{ route('service-orders.index') }}" class="flex items-center gap-3 px-3 py-2 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    Service Orders
                                </a>
                                <a href="{{ route('contracts.index') }}" class="flex items-center gap-3 px-3 py-2 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Contracts
                                </a>
                            </div>

                            <div class="space-y-1 mt-4">
                                <p class="px-3 py-2 text-xs font-semibold text-surface-400 dark:text-surface-500 uppercase tracking-wider">Communication</p>
                                <a href="{{ route('conversations.index') }}" class="flex items-center gap-3 px-3 py-2 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    Messages
                                </a>
                            </div>
                        </nav>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Support Tickets</h1>
                            <p class="text-surface-600 dark:text-surface-400 mt-1">Get help with your orders and issues</p>
                        </div>
                        <a href="{{ route('support.create') }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            New Ticket
                        </a>
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-wrap gap-2 mb-6">
                        <a href="{{ route('support.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !request('status') ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }}">
                            All
                        </a>
                        <a href="{{ route('support.index', ['status' => 'open']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'open' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }}">
                            Open
                        </a>
                        <a href="{{ route('support.index', ['status' => 'in_progress']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'in_progress' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }}">
                            In Progress
                        </a>
                        <a href="{{ route('support.index', ['status' => 'resolved']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'resolved' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }}">
                            Resolved
                        </a>
                    </div>

                    <!-- Tickets List -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                        @if($tickets->count() > 0)
                            <div class="divide-y divide-surface-200 dark:divide-surface-700">
                                @foreach($tickets as $ticket)
                                    <a href="{{ route('support.show', $ticket) }}" class="block px-6 py-4 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <span class="text-sm font-mono text-surface-500 dark:text-surface-400">{{ $ticket->ticket_number }}</span>
                                                    <x-status-badge :status="$ticket->status" size="sm" />
                                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                        @if($ticket->priority === 'high') bg-danger-100 dark:bg-danger-900/30 text-danger-700 dark:text-danger-400
                                                        @elseif($ticket->priority === 'medium') bg-warning-100 dark:bg-warning-900/30 text-warning-700 dark:text-warning-400
                                                        @else bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-400
                                                        @endif">
                                                        {{ ucfirst($ticket->priority) }}
                                                    </span>
                                                </div>
                                                <h3 class="font-medium text-surface-900 dark:text-white">{{ $ticket->subject }}</h3>
                                                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400 line-clamp-2">{{ $ticket->description }}</p>
                                                <div class="flex items-center gap-2 mt-2">
                                                    @if($ticket->product)
                                                        <span class="inline-flex items-center px-2 py-0.5 text-xs bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-400 rounded">
                                                            {{ $ticket->product->name }}
                                                        </span>
                                                    @endif
                                                    @if($ticket->serviceOrder)
                                                        <span class="inline-flex items-center px-2 py-0.5 text-xs bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 rounded">
                                                            Service Order #{{ $ticket->serviceOrder->order_number }}
                                                        </span>
                                                    @endif
                                                    @if($ticket->contract)
                                                        <span class="inline-flex items-center px-2 py-0.5 text-xs bg-info-100 dark:bg-info-900/30 text-info-700 dark:text-info-300 rounded">
                                                            Contract #{{ $ticket->contract->contract_number }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <span class="text-sm text-surface-500 dark:text-surface-400 shrink-0 ml-4">{{ $ticket->created_at->diffForHumans() }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            @if($tickets->hasPages())
                                <div class="px-6 py-4 border-t border-surface-200 dark:border-surface-700">
                                    {{ $tickets->links() }}
                                </div>
                            @endif
                        @else
                            <div class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">No support tickets</h3>
                                <p class="text-surface-600 dark:text-surface-400 mb-6">Need help? Create a support ticket.</p>
                                <a href="{{ route('support.create') }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                                    Create a Ticket
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
