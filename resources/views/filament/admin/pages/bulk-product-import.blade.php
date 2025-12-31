<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Download Template --}}
        <x-filament::section>
            <x-slot name="heading">CSV Template</x-slot>
            <x-slot name="description">Download the template to see the required format for importing products.</x-slot>

            <div class="flex items-center gap-4">
                <x-filament::button
                    wire:click="downloadTemplate"
                    icon="heroicon-o-arrow-down-tray"
                    color="gray"
                >
                    Download Template
                </x-filament::button>

                <div class="text-sm text-surface-500 dark:text-surface-400">
                    Required columns: <code class="bg-surface-100 dark:bg-surface-800 px-1 rounded">name</code>, <code class="bg-surface-100 dark:bg-surface-800 px-1 rounded">price</code>
                </div>
            </div>
        </x-filament::section>

        {{-- Upload Form --}}
        <form wire:submit="preview">
            {{ $this->form }}

            <div class="mt-6 flex gap-3">
                <x-filament::button type="submit" icon="heroicon-o-eye">
                    Preview Import
                </x-filament::button>
            </div>
        </form>

        {{-- Preview Data --}}
        @if($previewData)
            <x-filament::section>
                <x-slot name="heading">Preview (First 10 Rows)</x-slot>
                <x-slot name="description">
                    Total: {{ $totalRows }} rows | Valid: {{ $validRows }} | Errors: {{ $errorRows }}
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b dark:border-surface-700">
                                <th class="px-3 py-2 text-left font-medium text-surface-500 dark:text-surface-400">#</th>
                                @foreach($previewData['headers'] as $header)
                                    <th class="px-3 py-2 text-left font-medium text-surface-500 dark:text-surface-400">
                                        {{ $header }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($previewData['rows'] as $index => $row)
                                @php $rowNum = $index + 2; @endphp
                                <tr class="border-b dark:border-surface-700 {{ isset($validationErrors[$rowNum]) ? 'bg-danger-50 dark:bg-danger-900/20' : '' }}">
                                    <td class="px-3 py-2 text-surface-600 dark:text-surface-300">{{ $rowNum }}</td>
                                    @foreach($previewData['headers'] as $header)
                                        <td class="px-3 py-2 text-surface-900 dark:text-surface-100 max-w-xs truncate">
                                            {{ $row[$header] ?? '' }}
                                        </td>
                                    @endforeach
                                </tr>
                                @if(isset($validationErrors[$rowNum]))
                                    <tr class="bg-danger-50 dark:bg-danger-900/20">
                                        <td colspan="{{ count($previewData['headers']) + 1 }}" class="px-3 py-2">
                                            <div class="text-danger-600 dark:text-danger-400 text-xs">
                                                @foreach($validationErrors[$rowNum] as $error)
                                                    <span class="inline-block mr-2">{{ $error }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($validRows > 0)
                    <div class="mt-6 flex gap-3">
                        <x-filament::button
                            wire:click="import"
                            icon="heroicon-o-arrow-up-tray"
                            color="success"
                        >
                            Import {{ $validRows }} Products
                        </x-filament::button>
                    </div>
                @endif
            </x-filament::section>
        @endif

        {{-- Validation Errors Summary --}}
        @if($validationErrors && count($validationErrors) > 10)
            <x-filament::section collapsible collapsed>
                <x-slot name="heading">All Validation Errors ({{ count($validationErrors) }})</x-slot>

                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($validationErrors as $row => $errors)
                        <div class="text-sm">
                            <span class="font-medium text-danger-600 dark:text-danger-400">Row {{ str_replace('row_', '', $row) }}:</span>
                            <span class="text-surface-600 dark:text-surface-400">{{ implode(', ', $errors) }}</span>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endif

        {{-- Recent Imports --}}
        <x-filament::section collapsible>
            <x-slot name="heading">Recent Imports</x-slot>

            @php $recentImports = $this->getRecentImports(); @endphp

            @if($recentImports->isEmpty())
                <div class="text-center py-6 text-surface-500 dark:text-surface-400">
                    No imports yet.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b dark:border-surface-700">
                                <th class="px-3 py-2 text-left font-medium text-surface-500 dark:text-surface-400">Date</th>
                                <th class="px-3 py-2 text-left font-medium text-surface-500 dark:text-surface-400">User</th>
                                <th class="px-3 py-2 text-left font-medium text-surface-500 dark:text-surface-400">Status</th>
                                <th class="px-3 py-2 text-left font-medium text-surface-500 dark:text-surface-400">Progress</th>
                                <th class="px-3 py-2 text-left font-medium text-surface-500 dark:text-surface-400">Success</th>
                                <th class="px-3 py-2 text-left font-medium text-surface-500 dark:text-surface-400">Failed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentImports as $import)
                                <tr class="border-b dark:border-surface-700">
                                    <td class="px-3 py-2 text-surface-600 dark:text-surface-300">
                                        {{ $import->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-3 py-2 text-surface-900 dark:text-surface-100">
                                        {{ $import->user->name ?? 'Unknown' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'processing' => 'info',
                                                'completed' => 'success',
                                                'failed' => 'danger',
                                            ];
                                        @endphp
                                        <x-filament::badge :color="$statusColors[$import->status] ?? 'gray'">
                                            {{ ucfirst($import->status) }}
                                        </x-filament::badge>
                                    </td>
                                    <td class="px-3 py-2 text-surface-600 dark:text-surface-300">
                                        {{ $import->processed_rows }} / {{ $import->total_rows }}
                                    </td>
                                    <td class="px-3 py-2 text-success-600 dark:text-success-400">
                                        {{ $import->success_rows }}
                                    </td>
                                    <td class="px-3 py-2 text-danger-600 dark:text-danger-400">
                                        {{ $import->failed_rows }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
