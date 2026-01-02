<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-600">
                        {{ \App\Models\VideoCall::count() }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Calls</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-success-600">
                        {{ \App\Models\VideoCall::where('status', 'ended')->count() }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Completed</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-warning-600">
                        {{ \App\Models\VideoCall::where('status', 'active')->count() }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Active Now</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-info-600">
                        {{ \App\Models\VideoCall::whereIn('status', ['pending', 'scheduled'])->count() }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Scheduled</div>
                </div>
            </x-filament::section>
        </div>

        {{-- Settings Form --}}
        <x-filament::section>
            <x-slot name="heading">Video Call Configuration</x-slot>
            <x-slot name="description">Configure video calling providers and settings for service consultations</x-slot>

            <form wire:submit="save">
                {{ $this->form }}

                <div class="mt-6">
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
                            <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Best For</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Features</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 dark:text-gray-400">
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">Jitsi Meet</td>
                            <td class="px-4 py-3">Free (public) / Self-hosted</td>
                            <td class="px-4 py-3">Small to medium businesses</td>
                            <td class="px-4 py-3">No account required, easy setup</td>
                        </tr>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">Agora</td>
                            <td class="px-4 py-3">10,000 free min/month, then pay</td>
                            <td class="px-4 py-3">Enterprise, high quality needs</td>
                            <td class="px-4 py-3">HD video, global network, recording</td>
                        </tr>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">Twilio Video</td>
                            <td class="px-4 py-3">Pay-as-you-go ($0.004/min)</td>
                            <td class="px-4 py-3">Developers, custom solutions</td>
                            <td class="px-4 py-3">Programmable, webhooks, recording</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">Daily.co</td>
                            <td class="px-4 py-3">Free tier + paid plans</td>
                            <td class="px-4 py-3">Startups, fast integration</td>
                            <td class="px-4 py-3">Prebuilt UI, easy embed</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
