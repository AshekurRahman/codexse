<?php

namespace App\Filament\Admin\Pages;

use App\Models\MessageAttachment;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class FileShareSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-paper-clip';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'File Sharing';
    protected static ?string $title = 'File Sharing Settings';
    protected static ?int $navigationSort = 116;

    protected static string $view = 'filament.admin.pages.file-share-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'file_sharing_enabled' => Setting::get('file_sharing_enabled', true),
            'max_file_size' => Setting::get('max_file_size', 10),
            'max_files_per_message' => Setting::get('max_files_per_message', 5),
            'allowed_file_types' => Setting::get('allowed_file_types', $this->getDefaultAllowedTypes()),
            'allow_images' => Setting::get('file_allow_images', true),
            'allow_documents' => Setting::get('file_allow_documents', true),
            'allow_videos' => Setting::get('file_allow_videos', true),
            'allow_audio' => Setting::get('file_allow_audio', true),
            'allow_archives' => Setting::get('file_allow_archives', true),
            'virus_scan_enabled' => Setting::get('virus_scan_enabled', false),
            'storage_driver' => Setting::get('file_storage_driver', 'local'),
            'cdn_url' => Setting::get('file_cdn_url', ''),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('File Sharing')
                    ->schema([
                        Forms\Components\Toggle::make('file_sharing_enabled')
                            ->label('Enable File Sharing')
                            ->helperText('Allow users to attach files in messages')
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('max_file_size')
                                    ->label('Max File Size')
                                    ->numeric()
                                    ->suffix('MB')
                                    ->default(10)
                                    ->minValue(1)
                                    ->maxValue(100)
                                    ->helperText('Maximum size per file'),

                                Forms\Components\TextInput::make('max_files_per_message')
                                    ->label('Max Files Per Message')
                                    ->numeric()
                                    ->default(5)
                                    ->minValue(1)
                                    ->maxValue(20)
                                    ->helperText('Maximum attachments per message'),
                            ]),
                    ]),

                Forms\Components\Section::make('Allowed File Types')
                    ->description('Select which file types users can upload')
                    ->schema([
                        Forms\Components\Grid::make(5)
                            ->schema([
                                Forms\Components\Toggle::make('allow_images')
                                    ->label('Images')
                                    ->helperText('JPG, PNG, GIF, WebP'),

                                Forms\Components\Toggle::make('allow_documents')
                                    ->label('Documents')
                                    ->helperText('PDF, DOC, XLS, PPT'),

                                Forms\Components\Toggle::make('allow_videos')
                                    ->label('Videos')
                                    ->helperText('MP4, MOV, AVI'),

                                Forms\Components\Toggle::make('allow_audio')
                                    ->label('Audio')
                                    ->helperText('MP3, WAV, OGG'),

                                Forms\Components\Toggle::make('allow_archives')
                                    ->label('Archives')
                                    ->helperText('ZIP, RAR, 7Z'),
                            ]),

                        Forms\Components\TagsInput::make('allowed_file_types')
                            ->label('Allowed Extensions')
                            ->placeholder('Add custom extension')
                            ->helperText('Add specific file extensions (e.g., psd, ai, sketch)')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Security & Storage')
                    ->schema([
                        Forms\Components\Toggle::make('virus_scan_enabled')
                            ->label('Enable Virus Scanning')
                            ->helperText('Scan uploaded files for malware (requires ClamAV)'),

                        Forms\Components\Select::make('storage_driver')
                            ->label('Storage Driver')
                            ->options([
                                'local' => 'Local Storage',
                                's3' => 'Amazon S3',
                                'spaces' => 'DigitalOcean Spaces',
                                'gcs' => 'Google Cloud Storage',
                            ])
                            ->default('local'),

                        Forms\Components\TextInput::make('cdn_url')
                            ->label('CDN URL (Optional)')
                            ->url()
                            ->placeholder('https://cdn.example.com')
                            ->helperText('Serve files through a CDN for faster delivery'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('file_sharing_enabled', $data['file_sharing_enabled'] ?? true, 'files', 'boolean', false);
        Setting::set('max_file_size', $data['max_file_size'] ?? 10, 'files', 'integer', false);
        Setting::set('max_files_per_message', $data['max_files_per_message'] ?? 5, 'files', 'integer', false);
        Setting::set('allowed_file_types', $data['allowed_file_types'] ?? [], 'files', 'json', false);
        Setting::set('file_allow_images', $data['allow_images'] ?? true, 'files', 'boolean', false);
        Setting::set('file_allow_documents', $data['allow_documents'] ?? true, 'files', 'boolean', false);
        Setting::set('file_allow_videos', $data['allow_videos'] ?? true, 'files', 'boolean', false);
        Setting::set('file_allow_audio', $data['allow_audio'] ?? true, 'files', 'boolean', false);
        Setting::set('file_allow_archives', $data['allow_archives'] ?? true, 'files', 'boolean', false);
        Setting::set('virus_scan_enabled', $data['virus_scan_enabled'] ?? false, 'files', 'boolean', false);
        Setting::set('file_storage_driver', $data['storage_driver'] ?? 'local', 'files', 'string', false);
        Setting::set('file_cdn_url', $data['cdn_url'] ?? '', 'files', 'string', false);

        Notification::make()
            ->title('File sharing settings saved')
            ->success()
            ->send();
    }

    protected function getDefaultAllowedTypes(): array
    {
        return [
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf', 'csv',
            'mp4', 'mov', 'avi', 'webm',
            'mp3', 'wav', 'ogg',
            'zip', 'rar', '7z',
        ];
    }

    public function getStorageStats(): array
    {
        $totalFiles = MessageAttachment::count();
        $totalSize = MessageAttachment::sum('file_size');

        return [
            'total_files' => $totalFiles,
            'total_size' => $this->formatBytes($totalSize),
            'avg_size' => $totalFiles > 0 ? $this->formatBytes($totalSize / $totalFiles) : '0 B',
        ];
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
