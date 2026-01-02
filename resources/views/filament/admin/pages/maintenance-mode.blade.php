<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Current Status --}}
        <x-filament::section>
            <x-slot name="heading">Current Status</x-slot>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    @if($this->isMaintenanceMode)
                        <div class="flex items-center gap-2 text-danger-600">
                            <x-heroicon-o-exclamation-triangle class="w-8 h-8" />
                            <div>
                                <p class="font-semibold text-lg">Maintenance Mode is ACTIVE</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Your site is currently offline for visitors</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-2 text-success-600">
                            <x-heroicon-o-check-circle class="w-8 h-8" />
                            <div>
                                <p class="font-semibold text-lg">Site is Online</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Your site is accessible to all visitors</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div>
                    @if($this->isMaintenanceMode)
                        <x-filament::button
                            wire:click="disableMaintenance"
                            color="success"
                            icon="heroicon-o-arrow-up-circle"
                            size="lg"
                        >
                            Bring Site Online
                        </x-filament::button>
                    @else
                        <x-filament::button
                            wire:click="enableMaintenance"
                            wire:confirm="Are you sure you want to put the site in maintenance mode? Visitors will not be able to access the site."
                            color="danger"
                            icon="heroicon-o-arrow-down-circle"
                            size="lg"
                        >
                            Enable Maintenance Mode
                        </x-filament::button>
                    @endif
                </div>
            </div>
        </x-filament::section>

        {{-- Settings Form --}}
        <x-filament::section>
            <x-slot name="heading">Maintenance Settings</x-slot>
            <x-slot name="description">Configure the maintenance page and access options</x-slot>

            <form wire:submit="save">
                {{ $this->form }}

                <div class="mt-6">
                    <x-filament::button type="submit">
                        Save Settings
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        {{-- Help Section --}}
        <x-filament::section collapsed>
            <x-slot name="heading">How It Works</x-slot>

            <div class="prose prose-sm dark:prose-invert max-w-none text-gray-600 dark:text-gray-400">
                <ul>
                    <li><strong>Maintenance Mode</strong> - When enabled, visitors will see a maintenance page instead of your site.</li>
                    <li><strong>Admin Access</strong> - Administrators can still access the admin panel at <code class="dark:bg-gray-700 dark:text-gray-300">/admin</code>.</li>
                    <li><strong>Secret Bypass</strong> - Set a secret token to create a bypass URL. Share this URL with team members who need access.</li>
                    <li><strong>Allowed IPs</strong> - Add IP addresses that should always have access, even during maintenance.</li>
                    <li><strong>Scheduled End</strong> - Set an expected end time to display to visitors.</li>
                </ul>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
