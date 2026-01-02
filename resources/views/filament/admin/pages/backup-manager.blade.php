<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Backup Actions --}}
        <x-filament::section>
            <x-slot name="heading">Quick Actions</x-slot>
            <x-slot name="description">Create and manage database backups</x-slot>

            <div class="flex gap-4">
                <x-filament::button
                    wire:click="createBackup"
                    icon="heroicon-o-arrow-down-tray"
                    color="primary"
                >
                    Create Backup Now
                </x-filament::button>
            </div>
        </x-filament::section>

        {{-- Backup List --}}
        <x-filament::section>
            <x-slot name="heading">Available Backups</x-slot>
            <x-slot name="description">Download, restore, or delete existing backups</x-slot>

            @if(count($this->backups) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Filename</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Size</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Date</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->backups as $backup)
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-4 py-3 font-mono text-sm">{{ $backup['name'] }}</td>
                                    <td class="px-4 py-3">{{ $backup['size'] }}</td>
                                    <td class="px-4 py-3">{{ $backup['date'] }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            <x-filament::button
                                                wire:click="downloadBackup('{{ $backup['name'] }}')"
                                                size="sm"
                                                color="gray"
                                                icon="heroicon-o-arrow-down-tray"
                                            >
                                                Download
                                            </x-filament::button>
                                            <x-filament::button
                                                wire:click="restoreBackup('{{ $backup['name'] }}')"
                                                wire:confirm="Are you sure you want to restore this backup? This will overwrite your current database!"
                                                size="sm"
                                                color="warning"
                                                icon="heroicon-o-arrow-path"
                                            >
                                                Restore
                                            </x-filament::button>
                                            <x-filament::button
                                                wire:click="deleteBackup('{{ $backup['name'] }}')"
                                                wire:confirm="Are you sure you want to delete this backup?"
                                                size="sm"
                                                color="danger"
                                                icon="heroicon-o-trash"
                                            >
                                                Delete
                                            </x-filament::button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <x-heroicon-o-circle-stack class="w-12 h-12 mx-auto mb-4 opacity-50" />
                    <p>No backups available yet.</p>
                    <p class="text-sm">Click "Create Backup Now" to create your first backup.</p>
                </div>
            @endif
        </x-filament::section>

        {{-- Settings Form --}}
        <x-filament::section>
            <x-slot name="heading">Automatic Backup Settings</x-slot>
            <x-slot name="description">Configure scheduled database backups</x-slot>

            <form wire:submit="save">
                {{ $this->form }}

                <div class="mt-6">
                    <x-filament::button type="submit">
                        Save Settings
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>
    </div>
</x-filament-panels::page>
