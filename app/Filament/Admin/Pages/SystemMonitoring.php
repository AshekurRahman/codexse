<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SystemMonitoring extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-server';

    protected static string $view = 'filament.admin.pages.system-monitoring';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'System Monitoring';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clear_cache')
                ->label('Clear Cache')
                ->icon('heroicon-o-trash')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    Artisan::call('cache:clear');
                    Artisan::call('config:clear');
                    Artisan::call('view:clear');

                    Notification::make()
                        ->title('Cache Cleared')
                        ->success()
                        ->send();
                }),

            Action::make('optimize')
                ->label('Optimize')
                ->icon('heroicon-o-bolt')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    Artisan::call('optimize');

                    Notification::make()
                        ->title('Application Optimized')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getSystemInfo(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'server_os' => php_uname('s') . ' ' . php_uname('r'),
            'server_time' => now()->format('Y-m-d H:i:s T'),
            'timezone' => config('app.timezone'),
        ];
    }

    public function getPhpConfig(): array
    {
        return [
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time') . 's',
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_input_vars' => ini_get('max_input_vars'),
            'opcache_enabled' => function_exists('opcache_get_status') && opcache_get_status() ? 'Yes' : 'No',
        ];
    }

    public function getDatabaseInfo(): array
    {
        try {
            $pdo = DB::connection()->getPdo();
            $version = DB::select('SELECT VERSION() as version')[0]->version ?? 'Unknown';

            // Get database size
            $dbName = config('database.connections.mysql.database');
            $sizeQuery = DB::select("
                SELECT
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                FROM information_schema.tables
                WHERE table_schema = ?
            ", [$dbName]);

            $size = $sizeQuery[0]->size_mb ?? 0;

            // Get table count
            $tableCount = DB::select("
                SELECT COUNT(*) as count
                FROM information_schema.tables
                WHERE table_schema = ?
            ", [$dbName])[0]->count;

            return [
                'driver' => 'MySQL',
                'version' => explode('-', $version)[0],
                'database' => $dbName,
                'size' => $size . ' MB',
                'tables' => $tableCount,
                'status' => 'Connected',
            ];
        } catch (\Exception $e) {
            return [
                'driver' => 'MySQL',
                'status' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    public function getCacheInfo(): array
    {
        $driver = config('cache.default');

        try {
            Cache::put('monitoring_test', true, 10);
            $working = Cache::get('monitoring_test') === true;
            Cache::forget('monitoring_test');

            return [
                'driver' => ucfirst($driver),
                'status' => $working ? 'Working' : 'Error',
            ];
        } catch (\Exception $e) {
            return [
                'driver' => ucfirst($driver),
                'status' => 'Error',
            ];
        }
    }

    public function getQueueInfo(): array
    {
        $driver = config('queue.default');

        try {
            $pendingJobs = 0;
            $failedJobs = DB::table('failed_jobs')->count();

            if ($driver === 'database') {
                $pendingJobs = DB::table('jobs')->count();
            }

            return [
                'driver' => ucfirst($driver),
                'pending_jobs' => $pendingJobs,
                'failed_jobs' => $failedJobs,
            ];
        } catch (\Exception $e) {
            return [
                'driver' => ucfirst($driver),
                'status' => 'Error',
            ];
        }
    }

    public function getStorageInfo(): array
    {
        $storagePath = storage_path();

        $totalSpace = disk_total_space($storagePath);
        $freeSpace = disk_free_space($storagePath);
        $usedSpace = $totalSpace - $freeSpace;
        $usedPercent = round(($usedSpace / $totalSpace) * 100, 1);

        return [
            'total' => $this->formatBytes($totalSpace),
            'used' => $this->formatBytes($usedSpace),
            'free' => $this->formatBytes($freeSpace),
            'used_percent' => $usedPercent,
            'writable' => is_writable($storagePath) ? 'Yes' : 'No',
        ];
    }

    public function getSessionInfo(): array
    {
        return [
            'driver' => config('session.driver'),
            'lifetime' => config('session.lifetime') . ' minutes',
            'encrypt' => config('session.encrypt') ? 'Yes' : 'No',
            'secure' => config('session.secure') ? 'Yes' : 'No',
        ];
    }

    public function getMailInfo(): array
    {
        return [
            'driver' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host', 'N/A'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ];
    }

    public function getRecentLogs(): array
    {
        $logFile = storage_path('logs/laravel.log');

        if (!file_exists($logFile)) {
            return [];
        }

        $logs = [];
        $handle = fopen($logFile, 'r');

        if ($handle) {
            fseek($handle, max(0, filesize($logFile) - 50000));
            $content = fread($handle, 50000);
            fclose($handle);

            $lines = explode("\n", $content);
            $lines = array_slice($lines, -20);

            foreach ($lines as $line) {
                if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*?(ERROR|WARNING|INFO|DEBUG)/', $line, $matches)) {
                    $logs[] = [
                        'time' => $matches[1],
                        'level' => $matches[2],
                        'message' => substr($line, 0, 200),
                    ];
                }
            }
        }

        return array_reverse($logs);
    }

    protected function formatBytes($bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
