<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Branding Settings --}}
        <x-filament::section>
            <x-slot name="heading">Email Branding</x-slot>
            <x-slot name="description">Configure the appearance of all transactional emails</x-slot>

            <form wire:submit="save">
                {{ $this->form }}

                <div class="mt-6">
                    <x-filament::button type="submit">
                        Save Branding Settings
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        {{-- Template Editor --}}
        <x-filament::section>
            <x-slot name="heading">Email Templates</x-slot>
            <x-slot name="description">Customize the content of each transactional email</x-slot>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Template List --}}
                <div class="space-y-2">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Select Template</h4>
                    @foreach($this->getTemplates() as $slug => $template)
                        <button
                            wire:click="selectTemplate('{{ $slug }}')"
                            class="w-full text-left px-4 py-3 rounded-lg border transition-colors {{ $selectedTemplate === $slug ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800' }}"
                        >
                            <div class="font-medium text-sm text-gray-900 dark:text-white">{{ $template['name'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($template['category']) }}</div>
                        </button>
                    @endforeach
                </div>

                {{-- Template Editor --}}
                <div class="lg:col-span-2">
                    @if($selectedTemplate)
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subject Line</label>
                                <input
                                    type="text"
                                    wire:model="templateData.subject"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Content (HTML)</label>
                                <textarea
                                    wire:model="templateData.html_content"
                                    rows="12"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 font-mono text-sm"
                                ></textarea>
                            </div>

                            @if(isset($this->getTemplates()[$selectedTemplate]['variables']))
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                                    <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Available Variables</h5>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($this->getTemplates()[$selectedTemplate]['variables'] as $var)
                                            <code class="px-2 py-1 bg-white dark:bg-gray-700 rounded text-xs border border-gray-200 dark:border-gray-600 dark:text-gray-300">@{{{{ $var }}}}</code>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="flex gap-3">
                                <x-filament::button wire:click="saveTemplate">
                                    Save Template
                                </x-filament::button>
                                <x-filament::button wire:click="resetTemplate" color="gray">
                                    Reset to Default
                                </x-filament::button>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center justify-center h-64 text-gray-500 dark:text-gray-400">
                            <div class="text-center">
                                <x-heroicon-o-envelope class="w-12 h-12 mx-auto mb-3 opacity-50" />
                                <p>Select a template to edit</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </x-filament::section>

        {{-- Preview --}}
        @if($selectedTemplate && isset($templateData['html_content']))
            <x-filament::section collapsed>
                <x-slot name="heading">Preview</x-slot>

                <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg">
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm max-w-2xl mx-auto">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700 text-center">
                            @if(\App\Models\Setting::get('email_header_logo'))
                                <img src="{{ \App\Models\Setting::get('email_header_logo') }}" alt="Logo" class="h-8 mx-auto">
                            @else
                                <h1 class="text-xl font-bold" style="color: {{ \App\Models\Setting::get('email_primary_color', '#7c3aed') }}">{{ config('app.name') }}</h1>
                            @endif
                        </div>
                        <div class="p-6 prose prose-sm dark:prose-invert max-w-none">
                            {!! $templateData['html_content'] !!}
                        </div>
                        <div class="p-6 border-t border-gray-200 dark:border-gray-700 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ \App\Models\Setting::get('email_footer_text', '© ' . date('Y') . ' ' . config('app.name')) }}
                        </div>
                    </div>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
