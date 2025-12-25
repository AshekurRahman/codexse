<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <div class="flex justify-end gap-x-3">
            <x-filament::button type="submit">
                Save Settings
            </x-filament::button>
        </div>
    </x-filament-panels::form>

    <x-filament::section class="mt-6">
        <x-slot name="heading">Callback URLs</x-slot>
        <x-slot name="description">Use these URLs when configuring your OAuth applications</x-slot>

        <div class="space-y-4">
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Google Callback URL:</p>
                <code class="block mt-1 p-2 bg-gray-100 dark:bg-gray-800 rounded text-sm break-all">{{ route('social.callback', 'google') }}</code>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Facebook Callback URL:</p>
                <code class="block mt-1 p-2 bg-gray-100 dark:bg-gray-800 rounded text-sm break-all">{{ route('social.callback', 'facebook') }}</code>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">GitHub Callback URL:</p>
                <code class="block mt-1 p-2 bg-gray-100 dark:bg-gray-800 rounded text-sm break-all">{{ route('social.callback', 'github') }}</code>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Twitter/X Callback URL:</p>
                <code class="block mt-1 p-2 bg-gray-100 dark:bg-gray-800 rounded text-sm break-all">{{ route('social.callback', 'twitter') }}</code>
            </div>
        </div>
    </x-filament::section>
</x-filament-panels::page>
