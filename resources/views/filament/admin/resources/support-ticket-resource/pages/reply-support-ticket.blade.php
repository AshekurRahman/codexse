<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Ticket Info --}}
        <x-filament::section>
            <x-slot name="heading">Ticket Information</x-slot>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Customer:</span>
                    <p class="font-medium">{{ $record->user->name }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Category:</span>
                    <p class="font-medium capitalize">{{ $record->category }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Priority:</span>
                    <x-filament::badge :color="match($record->priority) {
                        'low' => 'gray',
                        'medium' => 'info',
                        'high' => 'warning',
                        'urgent' => 'danger',
                    }">
                        {{ ucfirst($record->priority) }}
                    </x-filament::badge>
                </div>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Status:</span>
                    <x-filament::badge :color="match($record->status) {
                        'open' => 'warning',
                        'in_progress' => 'info',
                        'waiting' => 'gray',
                        'resolved' => 'success',
                        'closed' => 'secondary',
                    }">
                        {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                    </x-filament::badge>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-gray-500 dark:text-gray-400">Subject:</span>
                <p class="font-medium">{{ $record->subject }}</p>
            </div>
            <div class="mt-4">
                <span class="text-sm text-gray-500 dark:text-gray-400">Description:</span>
                <div class="mt-1 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    {!! nl2br(e($record->description)) !!}
                </div>
            </div>
        </x-filament::section>

        {{-- Conversation --}}
        <x-filament::section>
            <x-slot name="heading">Conversation</x-slot>
            <div class="space-y-4">
                @forelse($record->replies as $reply)
                    <div class="flex gap-4 {{ $reply->is_staff_reply ? 'flex-row-reverse' : '' }}">
                        <div class="shrink-0">
                            <div class="w-10 h-10 rounded-full bg-{{ $reply->is_staff_reply ? 'primary' : 'gray' }}-100 dark:bg-{{ $reply->is_staff_reply ? 'primary' : 'gray' }}-800 flex items-center justify-center">
                                <span class="text-sm font-medium text-{{ $reply->is_staff_reply ? 'primary' : 'gray' }}-700 dark:text-{{ $reply->is_staff_reply ? 'primary' : 'gray' }}-300">
                                    {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-1 max-w-[80%]">
                            <div class="p-4 rounded-lg {{ $reply->is_staff_reply ? 'bg-primary-50 dark:bg-primary-900/20' : 'bg-gray-50 dark:bg-gray-800' }}">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="font-medium text-sm">{{ $reply->user->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="prose dark:prose-invert prose-sm max-w-none">
                                    {!! clean($reply->message) !!}
                                </div>
                                @if($reply->attachment)
                                    <div class="mt-2">
                                        <a href="{{ Storage::url($reply->attachment) }}" target="_blank" class="inline-flex items-center text-sm text-primary-600 hover:underline">
                                            <x-heroicon-o-paper-clip class="w-4 h-4 mr-1" />
                                            View Attachment
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">No replies yet.</p>
                @endforelse
            </div>
        </x-filament::section>

        {{-- Reply Form --}}
        <x-filament::section>
            <x-slot name="heading">Send Reply</x-slot>
            <form wire:submit="submit">
                {{ $this->form }}
                <div class="mt-4">
                    <x-filament::button type="submit">
                        Send Reply
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>
    </div>
</x-filament-panels::page>
