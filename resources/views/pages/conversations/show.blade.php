<x-layouts.app title="{{ $conversation->subject }} - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <a href="{{ route('conversations.index') }}" class="inline-flex items-center text-sm text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Messages
                </a>
                <div class="flex items-center gap-4">
                    <img src="{{ $conversation->seller->logo_url }}" alt="{{ $conversation->seller->store_name }}" class="h-12 w-12 rounded-full object-cover">
                    <div>
                        <h1 class="text-xl font-bold text-surface-900 dark:text-white">{{ $conversation->subject }}</h1>
                        <p class="text-surface-600 dark:text-surface-400">with {{ $conversation->seller->store_name }}</p>
                    </div>
                </div>
                @if($conversation->product)
                    <div class="mt-4 inline-flex items-center gap-2 rounded-lg bg-surface-100 dark:bg-surface-800 px-3 py-2">
                        <img src="{{ $conversation->product->thumbnail_url }}" alt="{{ $conversation->product->name }}" class="h-8 w-8 rounded object-cover">
                        <span class="text-sm text-surface-700 dark:text-surface-300">{{ $conversation->product->name }}</span>
                    </div>
                @endif
            </div>

            <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                <div class="p-6 space-y-4 max-h-[500px] overflow-y-auto">
                    @foreach($conversation->messages as $message)
                        <div class="flex gap-4 {{ $message->sender_id === auth()->id() ? 'flex-row-reverse' : '' }}">
                            <img src="{{ $message->sender->avatar_url ?? 'https://ui-avatars.com/api/?name=User' }}" alt="" class="h-10 w-10 rounded-full object-cover flex-shrink-0">
                            <div class="max-w-[70%] {{ $message->sender_id === auth()->id() ? 'text-right' : '' }}">
                                <div class="rounded-lg px-4 py-3 {{ $message->sender_id === auth()->id() ? 'bg-primary-600 text-white' : 'bg-surface-100 dark:bg-surface-700 text-surface-900 dark:text-white' }}">
                                    <p class="whitespace-pre-wrap">{{ $message->body }}</p>
                                </div>
                                <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">{{ $message->created_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-surface-200 dark:border-surface-700 p-4">
                    <form action="{{ route('conversations.reply', $conversation) }}" method="POST" class="flex gap-4">
                        @csrf
                        <input type="text" name="message" placeholder="Type your message..." required class="flex-1 rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                        <button type="submit" class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
