<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Stats --}}
        @php $stats = $this->getSmsStats(); @endphp
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">
                        {{ number_format($stats['total']) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total SMS</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-info-600">
                        {{ number_format($stats['sent']) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Sent</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-success-600">
                        {{ number_format($stats['delivered']) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Delivered</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-danger-600">
                        {{ number_format($stats['failed']) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Failed</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-warning-600">
                        ${{ number_format($stats['cost'], 2) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Cost</div>
                </div>
            </x-filament::section>
        </div>

        {{-- Settings Form --}}
        <x-filament::section>
            <x-slot name="heading">SMS Configuration</x-slot>
            <x-slot name="description">Configure SMS providers and notification settings</x-slot>

            <form wire:submit="save">
                {{ $this->form }}

                <div class="mt-6 flex gap-3">
                    <x-filament::button type="submit">
                        Save Settings
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        {{-- Provider Comparison --}}
        <x-filament::section collapsed>
            <x-slot name="heading">Provider Comparison</x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Provider</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Pricing</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Coverage</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Features</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 dark:text-gray-400">
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">Twilio</td>
                            <td class="px-4 py-3">~$0.0075/SMS (US)</td>
                            <td class="px-4 py-3">180+ countries</td>
                            <td class="px-4 py-3">Delivery reports, webhooks, analytics</td>
                        </tr>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">Vonage (Nexmo)</td>
                            <td class="px-4 py-3">~$0.0065/SMS (US)</td>
                            <td class="px-4 py-3">200+ countries</td>
                            <td class="px-4 py-3">Two-way SMS, number insight</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">AWS SNS</td>
                            <td class="px-4 py-3">~$0.00645/SMS (US)</td>
                            <td class="px-4 py-3">200+ countries</td>
                            <td class="px-4 py-3">Scalable, integrates with AWS</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- SMS Templates Info --}}
        <x-filament::section collapsed>
            <x-slot name="heading">SMS Message Templates</x-slot>

            <div class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white">Order Confirmation</h4>
                    <p class="font-mono bg-gray-50 dark:bg-gray-800 p-2 rounded mt-1">Your order #{{'{order_number}'}} has been confirmed! Thank you for your purchase.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white">Order Shipped</h4>
                    <p class="font-mono bg-gray-50 dark:bg-gray-800 p-2 rounded mt-1">Your order #{{'{order_number}'}} has been shipped! Tracking: {{'{tracking_number}'}}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white">Order Delivered</h4>
                    <p class="font-mono bg-gray-50 dark:bg-gray-800 p-2 rounded mt-1">Your order #{{'{order_number}'}} has been delivered! Enjoy your purchase.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white">Video Call Reminder</h4>
                    <p class="font-mono bg-gray-50 dark:bg-gray-800 p-2 rounded mt-1">Reminder: You have a video call scheduled at {{'{time}'}}. Join: {{'{url}'}}</p>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
