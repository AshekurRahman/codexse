<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Stats --}}
        @php $stats = $this->getStorageStats(); @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-600">
                        {{ number_format($stats['total_files']) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Files Shared</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-success-600">
                        {{ $stats['total_size'] }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Storage Used</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-info-600">
                        {{ $stats['avg_size'] }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Average File Size</div>
                </div>
            </x-filament::section>
        </div>

        {{-- Settings Form --}}
        <x-filament::section>
            <x-slot name="heading">File Sharing Configuration</x-slot>
            <x-slot name="description">Configure file upload settings for messages and conversations</x-slot>

            <form wire:submit="save">
                {{ $this->form }}

                <div class="mt-6">
                    <x-filament::button type="submit">
                        Save Settings
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        {{-- File Type Reference --}}
        <x-filament::section collapsed>
            <x-slot name="heading">Supported File Types Reference</x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Images</h4>
                    <p class="text-gray-600 dark:text-gray-400">jpg, jpeg, png, gif, webp, svg, bmp, ico</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Documents</h4>
                    <p class="text-gray-600 dark:text-gray-400">pdf, doc, docx, xls, xlsx, ppt, pptx, txt, rtf, csv, odt</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Videos</h4>
                    <p class="text-gray-600 dark:text-gray-400">mp4, mov, avi, webm, mkv, flv</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Audio</h4>
                    <p class="text-gray-600 dark:text-gray-400">mp3, wav, ogg, flac, aac, m4a</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Archives</h4>
                    <p class="text-gray-600 dark:text-gray-400">zip, rar, 7z, tar, gz</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Design Files</h4>
                    <p class="text-gray-600 dark:text-gray-400">psd, ai, sketch, fig, xd</p>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
