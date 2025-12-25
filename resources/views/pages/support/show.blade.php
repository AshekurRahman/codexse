<x-layouts.app title="Ticket #{{ $ticket->ticket_number }} - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <a href="{{ route('support.index') }}" class="inline-flex items-center text-sm text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Tickets
                </a>

                <div class="flex items-start justify-between">
                    <div>
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
                        </div>
                        <h1 class="mt-2 text-xl font-bold text-surface-900 dark:text-white">{{ $ticket->subject }}</h1>
                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Created {{ $ticket->created_at->format('M d, Y g:i A') }}</p>
                    </div>

                    @if($ticket->isOpen())
                        <form action="{{ route('support.close', $ticket) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center rounded-lg border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 px-4 py-2 text-sm font-medium text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                Close Ticket
                            </button>
                        </form>
                    @endif
                </div>

                @if($ticket->product)
                    <div class="mt-4 inline-flex items-center gap-2 rounded-lg bg-surface-100 dark:bg-surface-800 px-3 py-2">
                        <img src="{{ $ticket->product->thumbnail_url }}" alt="{{ $ticket->product->name }}" class="h-8 w-8 rounded object-cover">
                        <span class="text-sm text-surface-700 dark:text-surface-300">{{ $ticket->product->name }}</span>
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <!-- Original Message -->
                <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                    <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900">
                        <div class="flex items-center gap-3">
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="h-10 w-10 rounded-full object-cover">
                            <div>
                                <p class="font-medium text-surface-900 dark:text-white">{{ auth()->user()->name }}</p>
                                <p class="text-sm text-surface-500 dark:text-surface-400">{{ $ticket->created_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-surface-700 dark:text-surface-300 whitespace-pre-wrap">{{ $ticket->description }}</p>
                    </div>
                </div>

                <!-- Replies -->
                @foreach($ticket->replies as $reply)
                    <div class="rounded-xl border {{ $reply->is_staff ? 'border-primary-200 dark:border-primary-800' : 'border-surface-200 dark:border-surface-700' }} bg-white dark:bg-surface-800 overflow-hidden">
                        <div class="px-6 py-4 border-b {{ $reply->is_staff ? 'border-primary-200 dark:border-primary-800 bg-primary-50 dark:bg-primary-900/20' : 'border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900' }}">
                            <div class="flex items-center gap-3">
                                <img src="{{ $reply->user->avatar_url }}" alt="{{ $reply->user->name }}" class="h-10 w-10 rounded-full object-cover">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="font-medium text-surface-900 dark:text-white">{{ $reply->user->name }}</p>
                                        @if($reply->is_staff)
                                            <span class="inline-flex items-center rounded-full bg-primary-100 dark:bg-primary-900/30 px-2 py-0.5 text-xs font-medium text-primary-700 dark:text-primary-400">Staff</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">{{ $reply->created_at->format('M d, Y g:i A') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4">
                            <p class="text-surface-700 dark:text-surface-300 whitespace-pre-wrap">{{ $reply->message }}</p>
                        </div>
                    </div>
                @endforeach

                <!-- Reply Form -->
                @if($ticket->isOpen())
                    <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                        <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700">
                            <h3 class="font-medium text-surface-900 dark:text-white">Add a Reply</h3>
                        </div>
                        <form action="{{ route('support.reply', $ticket) }}" method="POST" class="p-6">
                            @csrf
                            <textarea name="message" rows="4" required class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-primary-500" placeholder="Write your reply...">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                            <div class="mt-4 flex justify-end">
                                <button type="submit" class="inline-flex items-center rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                                    Send Reply
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800 p-6 text-center">
                        <p class="text-surface-600 dark:text-surface-400">This ticket is closed. If you need further assistance, please create a new ticket.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
