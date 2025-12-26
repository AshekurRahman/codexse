<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Session Info --}}
        <x-filament::section>
            <x-slot name="heading">Session Information</x-slot>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">User:</span>
                    <p class="font-medium">{{ $record->display_name }}</p>
                    @if($record->user)
                        <p class="text-sm text-gray-500">{{ $record->user->email }}</p>
                    @elseif($record->guest_email)
                        <p class="text-sm text-gray-500">{{ $record->guest_email }}</p>
                    @else
                        <p class="text-sm text-gray-400 italic">No email provided</p>
                    @endif
                </div>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Status:</span>
                    <div class="mt-1">
                        <x-filament::badge :color="match($record->status) {
                            'active' => 'success',
                            'closed' => 'gray',
                            'archived' => 'secondary',
                            default => 'gray',
                        }">
                            {{ ucfirst($record->status) }}
                        </x-filament::badge>
                    </div>
                </div>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Statistics:</span>
                    <p class="text-sm">{{ $record->message_count }} messages</p>
                    <p class="text-sm">{{ number_format($record->total_tokens_used) }} tokens used</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Session ID:</span>
                    <p class="text-sm font-mono text-gray-600 dark:text-gray-300">{{ $record->session_id }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Started:</span>
                    <p class="text-sm">{{ $record->created_at->format('M j, Y g:i A') }}</p>
                    @if($record->last_message_at)
                        <span class="text-sm text-gray-500 dark:text-gray-400">Last Activity:</span>
                        <p class="text-sm">{{ $record->last_message_at->format('M j, Y g:i A') }} ({{ $record->last_message_at->diffForHumans() }})</p>
                    @endif
                </div>
            </div>
        </x-filament::section>

        {{-- Conversation --}}
        <x-filament::section>
            <x-slot name="heading">Conversation</x-slot>
            <div class="space-y-4 max-h-[600px] overflow-y-auto">
                @forelse($record->messages as $message)
                    <div class="flex gap-4 {{ $message->role === 'assistant' ? '' : 'flex-row-reverse' }}">
                        <div class="shrink-0">
                            @if($message->role === 'assistant')
                                <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-800 flex items-center justify-center">
                                    <x-heroicon-o-cpu-chip class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                                </div>
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                    <x-heroicon-o-user class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 max-w-[80%]">
                            <div class="p-4 rounded-lg {{ $message->role === 'assistant' ? 'bg-primary-50 dark:bg-primary-900/20' : 'bg-gray-50 dark:bg-gray-800' }}">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="font-medium text-sm">
                                        {{ $message->role === 'assistant' ? 'AI Assistant' : $record->display_name }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $message->created_at->format('M j, Y g:i A') }}</span>
                                </div>
                                <div class="prose dark:prose-invert prose-sm max-w-none">
                                    {!! nl2br(e($message->content)) !!}
                                </div>
                                @if($message->tokens_used)
                                    <div class="mt-2 text-xs text-gray-400">
                                        Tokens: {{ number_format($message->tokens_used) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <x-heroicon-o-chat-bubble-bottom-center-text class="w-12 h-12 mx-auto text-gray-400 mb-3" />
                        <p class="text-gray-500 dark:text-gray-400">No messages in this conversation.</p>
                    </div>
                @endforelse
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
