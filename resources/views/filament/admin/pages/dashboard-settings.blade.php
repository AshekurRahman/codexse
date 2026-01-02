<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Preview Section --}}
        <x-filament::section>
            <x-slot name="heading">Widget Preview</x-slot>
            <x-slot name="description">These widgets will appear on your dashboard</x-slot>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach(\App\Filament\Admin\Pages\DashboardSettings::$availableWidgets as $key => $widget)
                    <div class="p-4 rounded-lg border-2 transition-all {{ in_array($key, $this->data['enabled_widgets'] ?? []) ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 opacity-60' }}">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg {{ in_array($key, $this->data['enabled_widgets'] ?? []) ? 'bg-primary-100 dark:bg-primary-900/40 text-primary-600' : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                                @php
                                    $iconClass = str_replace('heroicon-o-', '', $widget['icon']);
                                @endphp
                                <x-dynamic-component :component="$widget['icon']" class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="font-medium text-sm text-gray-900 dark:text-white">{{ $widget['name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($widget['description'], 30) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        {{-- Settings Form --}}
        <x-filament::section>
            <x-slot name="heading">Configure Dashboard</x-slot>
            <x-slot name="description">Select which widgets to display and configure layout options</x-slot>

            <form wire:submit="save">
                {{ $this->form }}

                <div class="mt-6 flex gap-4">
                    <x-filament::button type="submit">
                        Save Settings
                    </x-filament::button>

                    <x-filament::button
                        wire:click="resetToDefaults"
                        color="gray"
                        type="button"
                    >
                        Reset to Defaults
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        {{-- Info Section --}}
        <x-filament::section collapsed>
            <x-slot name="heading">About Dashboard Widgets</x-slot>

            <div class="prose prose-sm dark:prose-invert max-w-none text-gray-600 dark:text-gray-400">
                <p>Customize your admin dashboard by selecting which widgets to display. Widgets provide quick insights into different aspects of your marketplace.</p>

                <h4>Available Widget Categories:</h4>
                <ul>
                    <li><strong>Analytics</strong> - Stats Overview, Revenue Chart, Orders Chart</li>
                    <li><strong>Products</strong> - Top Products, Low Stock Alert, Pending Reviews</li>
                    <li><strong>Users</strong> - Recent Users, Sellers Chart</li>
                    <li><strong>Operations</strong> - Latest Orders, Support Tickets, Activity Log</li>
                    <li><strong>System</strong> - System Health monitoring</li>
                </ul>

                <p><strong>Tip:</strong> Enable only the widgets you use regularly to keep your dashboard clean and fast-loading.</p>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
