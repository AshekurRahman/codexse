<x-layouts.app title="Messages - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Messages</h1>
                    <p class="mt-1 text-surface-600 dark:text-surface-400">Your conversations with sellers</p>
                </div>
                <a href="{{ route('conversations.create') }}" class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Message
                </a>
            </div>

            <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                @if($conversations->count() > 0)
                    <div class="divide-y divide-surface-200 dark:divide-surface-700">
                        @foreach($conversations as $conversation)
                            <a href="{{ route('conversations.show', $conversation) }}" class="block px-6 py-4 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                <div class="flex items-start gap-4">
                                    <img src="{{ $conversation->seller->user->avatar_url }}" alt="{{ $conversation->seller->store_name }}" class="h-12 w-12 rounded-full object-cover">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <h3 class="font-medium text-surface-900 dark:text-white truncate">{{ $conversation->seller->store_name }}</h3>
                                            <span class="text-sm text-surface-500 dark:text-surface-400">{{ $conversation->last_message_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm font-medium text-surface-700 dark:text-surface-300">{{ $conversation->subject }}</p>
                                        @if($conversation->latestMessage)
                                            <p class="text-sm text-surface-500 dark:text-surface-400 truncate">{{ $conversation->latestMessage->body }}</p>
                                        @endif
                                        @if($conversation->product)
                                            <span class="inline-flex items-center mt-2 rounded-full bg-surface-100 dark:bg-surface-700 px-2 py-0.5 text-xs text-surface-600 dark:text-surface-400">
                                                {{ $conversation->product->name }}
                                            </span>
                                        @endif
                                    </div>
                                    @php $unread = $conversation->unreadMessagesFor(auth()->user()); @endphp
                                    @if($unread > 0)
                                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-primary-600 text-xs font-medium text-white">{{ $unread }}</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="px-6 py-4 border-t border-surface-200 dark:border-surface-700">
                        {{ $conversations->links() }}
                    </div>
                @else
                    <div class="px-6 py-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p class="text-surface-600 dark:text-surface-400 mb-4">No conversations yet</p>
                        <a href="{{ route('conversations.create') }}" class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                            Start a Conversation
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
