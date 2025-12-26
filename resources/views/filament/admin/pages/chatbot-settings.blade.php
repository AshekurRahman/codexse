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
        <x-slot name="heading">Quick Start Guide</x-slot>

        <div class="prose dark:prose-invert prose-sm max-w-none">
            <h4>FAQ Bot (Free)</h4>
            <ol class="list-decimal list-inside space-y-1">
                <li>Select "FAQ Bot (Free)" mode above</li>
                <li>Enable the chatbot</li>
                <li>Go to <strong>Support > Chatbot FAQs</strong> to add your Q&A pairs</li>
                <li>Save settings - the chatbot will appear on your site!</li>
            </ol>

            <h4 class="mt-4">AI Bot (Paid)</h4>
            <ol class="list-decimal list-inside space-y-1">
                <li>Get an API key from <a href="https://console.anthropic.com" target="_blank" class="text-primary-600 hover:underline">console.anthropic.com</a></li>
                <li>Select "AI Bot" mode and enter your API key</li>
                <li>Choose a model and customize the system prompt</li>
                <li>Enable the chatbot and save</li>
            </ol>

            <h4 class="mt-4">AI Pricing (Claude API)</h4>
            <table class="min-w-full text-sm">
                <thead>
                    <tr>
                        <th class="text-left">Model</th>
                        <th class="text-left">Cost per 1M tokens</th>
                        <th class="text-left">Best For</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Sonnet 4</td>
                        <td>$3 in / $15 out</td>
                        <td>Best balance</td>
                    </tr>
                    <tr>
                        <td>Haiku 3.5</td>
                        <td>$0.80 in / $4 out</td>
                        <td>Fast & cheap</td>
                    </tr>
                    <tr>
                        <td>Opus 4</td>
                        <td>$15 in / $75 out</td>
                        <td>Most capable</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
