<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Stats Overview --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-filament::section>
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($this->rateLimitStats['total_blocked'] ?? 0) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Blocked Requests</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-3xl font-bold text-warning-600">
                        {{ number_format($this->rateLimitStats['blocked_today'] ?? 0) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Blocked Today</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <x-filament::button
                        wire:click="clearRateLimitCache"
                        color="gray"
                        icon="heroicon-o-arrow-path"
                    >
                        Clear Rate Limit Cache
                    </x-filament::button>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">Reset all rate limits</div>
                </div>
            </x-filament::section>
        </div>

        {{-- Settings Form --}}
        <x-filament::section>
            <x-slot name="heading">Rate Limiting Configuration</x-slot>
            <x-slot name="description">Configure rate limits for different endpoints to protect against abuse</x-slot>

            <form wire:submit="save">
                {{ $this->form }}

                <div class="mt-6">
                    <x-filament::button type="submit">
                        Save Settings
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        {{-- Documentation --}}
        <x-filament::section collapsed>
            <x-slot name="heading">How Rate Limiting Works</x-slot>

            <div class="prose prose-sm dark:prose-invert max-w-none text-gray-600 dark:text-gray-400">
                <ul>
                    <li><strong>Rate Limit</strong> - Maximum number of requests allowed within the decay period.</li>
                    <li><strong>Decay Period</strong> - Time window (in minutes) before the counter resets.</li>
                    <li><strong>Global Limit</strong> - Applies to all requests not covered by specific limits.</li>
                    <li><strong>API Limit</strong> - Applies to all API endpoints.</li>
                    <li><strong>Login Limit</strong> - Failed login attempts before temporary lockout.</li>
                    <li><strong>Whitelist</strong> - IPs that bypass all rate limiting (e.g., office IPs, monitoring services).</li>
                </ul>

                <h4>Recommended Settings:</h4>
                <ul>
                    <li>Login: 5 attempts per 1 minute (prevents brute force)</li>
                    <li>Registration: 3 attempts per 1 minute (prevents spam accounts)</li>
                    <li>API: 100 requests per minute (standard API limit)</li>
                    <li>Search: 30 requests per minute (prevents scraping)</li>
                </ul>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
