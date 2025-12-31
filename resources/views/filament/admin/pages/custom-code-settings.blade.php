<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit">
                Save Settings
            </x-filament::button>
        </div>
    </form>

    <div class="mt-8 p-4 rounded-lg bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800">
        <div class="flex gap-3">
            <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-warning-600 dark:text-warning-400 shrink-0 mt-0.5" />
            <div>
                <h4 class="font-medium text-warning-800 dark:text-warning-200">Security Warning</h4>
                <p class="text-sm text-warning-700 dark:text-warning-300 mt-1">
                    Only add code from trusted sources. Malicious scripts can compromise your website's security and user data.
                    Always test changes in a staging environment first.
                </p>
            </div>
        </div>
    </div>
</x-filament-panels::page>
