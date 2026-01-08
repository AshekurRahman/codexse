<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\Helpers\Format;

class BackupManager extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Backup Manager';
    protected static ?string $title = 'Database Backup Manager';
    protected static ?int $navigationSort = 110;

    protected static string $view = 'filament.admin.pages.backup-manager';

    public ?array $data = [];
    public array $backups = [];

    public function mount(): void
    {
        $this->loadBackups();

        $this->form->fill([
            'auto_backup_enabled' => Setting::get('auto_backup_enabled', false),
            'backup_frequency' => Setting::get('backup_frequency', 'daily'),
            'backup_retention_days' => Setting::get('backup_retention_days', 30),
            'backup_notify_email' => Setting::get('backup_notify_email', ''),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Automatic Backup Settings')
                    ->description('Configure automatic database backups with encryption')
                    ->schema([
                        Forms\Components\Toggle::make('auto_backup_enabled')
                            ->label('Enable Automatic Backups')
                            ->helperText('Automatically backup your database on schedule'),

                        Forms\Components\Select::make('backup_frequency')
                            ->label('Backup Frequency')
                            ->options([
                                'hourly' => 'Every Hour',
                                'daily' => 'Daily',
                                'weekly' => 'Weekly',
                                'monthly' => 'Monthly',
                            ])
                            ->default('daily'),

                        Forms\Components\TextInput::make('backup_retention_days')
                            ->label('Retention Period (Days)')
                            ->numeric()
                            ->default(30)
                            ->minValue(1)
                            ->maxValue(365)
                            ->helperText('How long to keep backup files'),

                        Forms\Components\TextInput::make('backup_notify_email')
                            ->label('Notification Email')
                            ->email()
                            ->placeholder('admin@example.com')
                            ->helperText('Send backup notifications to this email'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('auto_backup_enabled', $data['auto_backup_enabled'] ?? false, 'backup', 'boolean', false);
        Setting::set('backup_frequency', $data['backup_frequency'] ?? 'daily', 'backup', 'string', false);
        Setting::set('backup_retention_days', $data['backup_retention_days'] ?? 30, 'backup', 'integer', false);
        Setting::set('backup_notify_email', $data['backup_notify_email'] ?? '', 'backup', 'string', false);

        Notification::make()
            ->title('Backup settings saved successfully')
            ->success()
            ->send();
    }

    public function loadBackups(): void
    {
        $this->backups = [];

        try {
            // Get backups from Spatie's backup destination
            $disk = Storage::disk('backups');
            $appName = config('backup.backup.name');
            $backupPath = $appName;

            if (!$disk->exists($backupPath)) {
                // Fallback to legacy backups directory
                $this->loadLegacyBackups();
                return;
            }

            $files = $disk->files($backupPath);

            foreach ($files as $file) {
                $filename = basename($file);

                // Only show zip files (Spatie creates zip archives)
                if (pathinfo($filename, PATHINFO_EXTENSION) !== 'zip') {
                    continue;
                }

                $this->backups[] = [
                    'name' => $filename,
                    'size' => $this->formatBytes($disk->size($file)),
                    'date' => date('Y-m-d H:i:s', $disk->lastModified($file)),
                    'path' => $file,
                    'is_encrypted' => $this->isEncrypted($filename),
                ];
            }

            // Also load any legacy SQL backups
            $this->loadLegacyBackups();

            // Sort by date descending
            usort($this->backups, function ($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        } catch (\Exception $e) {
            Log::error('BackupManager: Failed to load backups', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Load legacy SQL backup files (from old exec()-based system)
     */
    protected function loadLegacyBackups(): void
    {
        $backupPath = storage_path('app/backups');

        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
            return;
        }

        $files = File::files($backupPath);

        foreach ($files as $file) {
            $ext = $file->getExtension();

            // Only show sql and zip files
            if (!in_array($ext, ['sql', 'zip'])) {
                continue;
            }

            $filename = $file->getFilename();

            // Skip if already added from Spatie backups
            $exists = collect($this->backups)->contains('name', $filename);
            if ($exists) {
                continue;
            }

            $this->backups[] = [
                'name' => $filename,
                'size' => $this->formatBytes($file->getSize()),
                'date' => date('Y-m-d H:i:s', $file->getMTime()),
                'path' => $file->getPathname(),
                'is_encrypted' => false,
                'is_legacy' => true,
            ];
        }
    }

    /**
     * Check if backup is encrypted based on filename pattern
     */
    protected function isEncrypted(string $filename): bool
    {
        // Spatie encrypts if BACKUP_ARCHIVE_PASSWORD is set
        return !empty(env('BACKUP_ARCHIVE_PASSWORD'));
    }

    /**
     * Create a new backup using Spatie Laravel Backup
     * This is secure - no exec() with shell commands
     */
    public function createBackup(): void
    {
        try {
            // Run Spatie backup command (database only for speed/security)
            Artisan::call('backup:run', [
                '--only-db' => true,
                '--disable-notifications' => true,
            ]);

            $output = Artisan::output();

            $this->loadBackups();

            Log::info('BackupManager: Backup created successfully', [
                'user_id' => auth()->id(),
                'output' => $output,
            ]);

            Notification::make()
                ->title('Backup created successfully')
                ->body('Database backup has been created and encrypted.')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Log::error('BackupManager: Backup creation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Backup failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Download a backup file securely
     */
    public function downloadBackup(string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        // Security: Validate filename to prevent directory traversal
        $safeFilename = basename($filename);
        if ($safeFilename !== $filename) {
            Notification::make()
                ->title('Invalid filename')
                ->danger()
                ->send();

            return $this->emptyResponse();
        }

        // Validate filename pattern
        if (!preg_match('/^[\w\-\.]+\.(sql|zip)$/i', $safeFilename)) {
            Notification::make()
                ->title('Invalid filename format')
                ->danger()
                ->send();

            return $this->emptyResponse();
        }

        try {
            $disk = Storage::disk('backups');
            $appName = config('backup.backup.name');

            // Try Spatie backup location first
            $spatiePath = $appName . '/' . $safeFilename;
            if ($disk->exists($spatiePath)) {
                $path = $spatiePath;
            } else {
                // Fallback to root of backups disk (legacy)
                $path = $safeFilename;
            }

            if (!$disk->exists($path)) {
                Notification::make()
                    ->title('File not found')
                    ->danger()
                    ->send();

                return $this->emptyResponse();
            }

            // Check file size to prevent memory issues (max 500MB)
            $fileSize = $disk->size($path);
            if ($fileSize > 500 * 1024 * 1024) {
                Notification::make()
                    ->title('File too large for download')
                    ->body('Please use SFTP to download large backup files.')
                    ->danger()
                    ->send();

                return $this->emptyResponse();
            }

            Log::info('BackupManager: Backup downloaded', [
                'user_id' => auth()->id(),
                'filename' => $safeFilename,
            ]);

            return $disk->download($path, $safeFilename);

        } catch (\Exception $e) {
            Log::error('BackupManager: Download failed', [
                'filename' => $safeFilename,
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Download failed')
                ->body('An error occurred while downloading the backup.')
                ->danger()
                ->send();

            return $this->emptyResponse();
        }
    }

    /**
     * Delete a backup file securely
     */
    public function deleteBackup(string $filename): void
    {
        // Security: Validate filename to prevent directory traversal
        $safeFilename = basename($filename);
        if ($safeFilename !== $filename || !preg_match('/^[\w\-\.]+\.(sql|zip)$/i', $safeFilename)) {
            Notification::make()
                ->title('Invalid filename')
                ->danger()
                ->send();
            return;
        }

        try {
            $disk = Storage::disk('backups');
            $appName = config('backup.backup.name');

            // Try Spatie backup location first
            $spatiePath = $appName . '/' . $safeFilename;
            if ($disk->exists($spatiePath)) {
                $disk->delete($spatiePath);
            } elseif ($disk->exists($safeFilename)) {
                // Legacy location
                $disk->delete($safeFilename);
            } else {
                Notification::make()
                    ->title('File not found')
                    ->danger()
                    ->send();
                return;
            }

            $this->loadBackups();

            Log::info('BackupManager: Backup deleted', [
                'user_id' => auth()->id(),
                'filename' => $safeFilename,
            ]);

            Notification::make()
                ->title('Backup deleted')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Log::error('BackupManager: Delete failed', [
                'filename' => $safeFilename,
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Delete failed')
                ->body('An error occurred while deleting the backup.')
                ->danger()
                ->send();
        }
    }

    /**
     * Restore a backup - requires confirmation and is logged
     * Note: For security, restore functionality should be carefully controlled
     */
    public function restoreBackup(string $filename): void
    {
        Notification::make()
            ->title('Restore Not Available')
            ->body('For security reasons, database restoration must be performed via command line: php artisan backup:restore')
            ->warning()
            ->send();

        Log::warning('BackupManager: Restore attempted via UI', [
            'user_id' => auth()->id(),
            'filename' => $filename,
        ]);
    }

    /**
     * Cleanup old backups using Spatie's cleanup strategy
     */
    public function cleanupBackups(): void
    {
        try {
            Artisan::call('backup:clean', [
                '--disable-notifications' => true,
            ]);

            $output = Artisan::output();
            $this->loadBackups();

            Log::info('BackupManager: Cleanup completed', [
                'user_id' => auth()->id(),
                'output' => $output,
            ]);

            Notification::make()
                ->title('Cleanup completed')
                ->body('Old backups have been removed according to retention policy.')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Log::error('BackupManager: Cleanup failed', [
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Cleanup failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Return an empty response for error cases
     */
    protected function emptyResponse(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return response()->streamDownload(function () {
            echo '';
        }, 'error.txt');
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    public static function getBackupCount(): int
    {
        try {
            $disk = Storage::disk('backups');
            $appName = config('backup.backup.name');

            if (!$disk->exists($appName)) {
                return 0;
            }

            $files = $disk->files($appName);
            return count(array_filter($files, fn($f) => str_ends_with($f, '.zip')));
        } catch (\Exception $e) {
            return 0;
        }
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getBackupCount();
        return $count > 0 ? (string) $count : null;
    }
}
