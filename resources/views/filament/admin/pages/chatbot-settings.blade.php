<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit">
                Save Settings
            </x-filament::button>
        </div>
    </form>

    <x-filament::section class="mt-6">
        <x-slot name="heading">API Documentation</x-slot>
        <x-slot name="description">Reference information for the Anthropic Claude API</x-slot>

        <div class="prose dark:prose-invert prose-sm max-w-none">
            <h4>Getting Started</h4>
            <ol class="list-decimal list-inside space-y-1">
                <li>Create an account at <a href="https://console.anthropic.com" target="_blank" class="text-primary-600 hover:underline">console.anthropic.com</a></li>
                <li>Navigate to API Keys section</li>
                <li>Create a new API key and copy it</li>
                <li>Paste the key in the API Key field above</li>
            </ol>

            <h4 class="mt-4">Pricing Reference (as of 2024)</h4>
            <table class="min-w-full text-sm">
                <thead>
                    <tr>
                        <th class="text-left">Model</th>
                        <th class="text-left">Input</th>
                        <th class="text-left">Output</th>
                        <th class="text-left">Best For</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Claude Sonnet 4</td>
                        <td>$3 / 1M tokens</td>
                        <td>$15 / 1M tokens</td>
                        <td>Best balance of quality & cost</td>
                    </tr>
                    <tr>
                        <td>Claude 3.5 Haiku</td>
                        <td>$0.80 / 1M tokens</td>
                        <td>$4 / 1M tokens</td>
                        <td>Fast responses, lower cost</td>
                    </tr>
                    <tr>
                        <td>Claude Opus 4</td>
                        <td>$15 / 1M tokens</td>
                        <td>$75 / 1M tokens</td>
                        <td>Complex reasoning, highest quality</td>
                    </tr>
                </tbody>
            </table>

            <h4 class="mt-4">Token Estimation</h4>
            <p>Approximately 1 token = 4 characters of English text. A typical chat message uses 50-200 tokens.</p>

            <h4 class="mt-4">Rate Limiting</h4>
            <p>Rate limits prevent abuse and help control costs. Limits are applied per user (authenticated) or per IP address (guests).</p>
        </div>
    </x-filament::section>
</x-filament-panels::page>
