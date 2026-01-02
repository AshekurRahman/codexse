<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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
                    ->description('Configure automatic database backups')
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
        $backupPath = storage_path('app/backups');

        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        $files = File::files($backupPath);

        foreach ($files as $file) {
            if ($file->getExtension() === 'sql' || $file->getExtension() === 'zip') {
                $this->backups[] = [
                    'name' => $file->getFilename(),
                    'size' => $this->formatBytes($file->getSize()),
                    'date' => date('Y-m-d H:i:s', $file->getMTime()),
                    'path' => $file->getPathname(),
                ];
            }
        }

        // Sort by date descending
        usort($this->backups, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
    }

    public function createBackup(): void
    {
        try {
            $backupPath = storage_path('app/backups');
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filePath = $backupPath . '/' . $filename;

            // Get database credentials
            $host = config('database.connections.mysql.host');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            // Create mysqldump command
            $command = sprintf(
                'mysqldump --host=%s --user=%s --password=%s %s > %s 2>&1',
                escapeshellarg($host),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($filePath)
            );

            exec($command, $output, $returnCode);

            if ($returnCode === 0 && File::exists($filePath)) {
                $this->loadBackups();

                Notification::make()
                    ->title('Backup created successfully')
                    ->body('File: ' . $filename)
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Backup failed')
                    ->body('Could not create database backup. Check server configuration.')
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Backup failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function downloadBackup(string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $path = storage_path('app/backups/' . $filename);

        if (File::exists($path)) {
            return response()->streamDownload(function () use ($path) {
                echo file_get_contents($path);
            }, $filename);
        }

        Notification::make()
            ->title('File not found')
            ->danger()
            ->send();

        return response()->streamDownload(function () {
            echo '';
        }, 'error.txt');
    }

    public function deleteBackup(string $filename): void
    {
        $path = storage_path('app/backups/' . $filename);

        if (File::exists($path)) {
            File::delete($path);
            $this->loadBackups();

            Notification::make()
                ->title('Backup deleted')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('File not found')
                ->danger()
                ->send();
        }
    }

    public function restoreBackup(string $filename): void
    {
        try {
            $path = storage_path('app/backups/' . $filename);

            if (!File::exists($path)) {
                Notification::make()
                    ->title('Backup file not found')
                    ->danger()
                    ->send();
                return;
            }

            // Get database credentials
            $host = config('database.connections.mysql.host');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            // Create mysql import command
            $command = sprintf(
                'mysql --host=%s --user=%s --password=%s %s < %s 2>&1',
                escapeshellarg($host),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($path)
            );

            exec($command, $output, $returnCode);

            if ($returnCode === 0) {
                Notification::make()
                    ->title('Database restored successfully')
                    ->body('Restored from: ' . $filename)
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Restore failed')
                    ->body('Could not restore database. Error: ' . implode("\n", $output))
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Restore failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
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
        $backupPath = storage_path('app/backups');

        if (!File::exists($backupPath)) {
            return 0;
        }

        return count(File::files($backupPath));
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getBackupCount();
        return $count > 0 ? (string) $count : null;
    }
}
