<x-layouts.app title="Support Tickets - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Support Tickets</h1>
                    <p class="mt-1 text-surface-600 dark:text-surface-400">Get help with your purchases</p>
                </div>
                <a href="{{ route('support.create') }}" class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Ticket
                </a>
            </div>

            <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                @if($tickets->count() > 0)
                    <div class="divide-y divide-surface-200 dark:divide-surface-700">
                        @foreach($tickets as $ticket)
                            <a href="{{ route('support.show', $ticket) }}" class="block px-6 py-4 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3">
                                            <span class="text-sm font-mono text-surface-500 dark:text-surface-400">{{ $ticket->ticket_number }}</span>
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                @if($ticket->status === 'open') bg-info-100 dark:bg-info-900/30 text-info-700 dark:text-info-400
                                                @elseif($ticket->status === 'in_progress') bg-warning-100 dark:bg-warning-900/30 text-warning-700 dark:text-warning-400
                                                @elseif($ticket->status === 'waiting') bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400
                                                @elseif($ticket->status === 'resolved') bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-400
                                                @else bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-400
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                            </span>
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                @if($ticket->priority === 'high') bg-danger-100 dark:bg-danger-900/30 text-danger-700 dark:text-danger-400
                                                @elseif($ticket->priority === 'medium') bg-warning-100 dark:bg-warning-900/30 text-warning-700 dark:text-warning-400
                                                @else bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-400
                                                @endif">
                                                {{ ucfirst($ticket->priority) }}
                                            </span>
                                        </div>
                                        <h3 class="mt-2 font-medium text-surface-900 dark:text-white">{{ $ticket->subject }}</h3>
                                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400 line-clamp-2">{{ $ticket->description }}</p>
                                        @if($ticket->product)
                                            <span class="inline-flex items-center mt-2 rounded-full bg-surface-100 dark:bg-surface-700 px-2 py-0.5 text-xs text-surface-600 dark:text-surface-400">
                                                {{ $ticket->product->name }}
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-sm text-surface-500 dark:text-surface-400">{{ $ticket->created_at->diffForHumans() }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="px-6 py-4 border-t border-surface-200 dark:border-surface-700">
                        {{ $tickets->links() }}
                    </div>
                @else
                    <div class="px-6 py-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <p class="text-surface-600 dark:text-surface-400 mb-4">No support tickets yet</p>
                        <a href="{{ route('support.create') }}" class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                            Create a Ticket
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
