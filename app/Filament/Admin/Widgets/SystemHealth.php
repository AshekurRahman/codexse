<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Pages\DashboardSettings;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SystemHealth extends Widget
{
    protected static ?int $sort = 11;
    protected int|string|array $columnSpan = 1;

    protected static string $view = 'filament.admin.widgets.system-health';

    public static function canView(): bool
    {
        return DashboardSettings::isWidgetEnabled('system_health');
    }

    public function getHealthChecks(): array
    {
        return [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'queue' => $this->checkQueue(),
        ];
    }

    protected function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            $version = DB::select('SELECT VERSION() as version')[0]->version ?? 'Unknown';

            return [
                'status' => 'healthy',
                'message' => 'MySQL ' . explode('-', $version)[0],
                'icon' => 'heroicon-o-circle-stack',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Connection failed',
                'icon' => 'heroicon-o-circle-stack',
            ];
        }
    }

    protected function checkCache(): array
    {
        try {
            Cache::put('health_check', true, 5);
            $result = Cache::get('health_check');
            Cache::forget('health_check');

            return [
                'status' => $result ? 'healthy' : 'unhealthy',
                'message' => $result ? config('cache.default') : 'Not working',
                'icon' => 'heroicon-o-bolt',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Cache error',
                'icon' => 'heroicon-o-bolt',
            ];
        }
    }

    protected function checkStorage(): array
    {
        $storagePath = storage_path();

        if (!is_writable($storagePath)) {
            return [
                'status' => 'unhealthy',
                'message' => 'Not writable',
                'icon' => 'heroicon-o-folder',
            ];
        }

        $freeSpace = disk_free_space($storagePath);
        $freeSpaceGB = round($freeSpace / 1024 / 1024 / 1024, 1);

        return [
            'status' => $freeSpaceGB > 1 ? 'healthy' : 'warning',
            'message' => $freeSpaceGB . ' GB free',
            'icon' => 'heroicon-o-folder',
        ];
    }

    protected function checkQueue(): array
    {
        try {
            $failedJobs = DB::table('failed_jobs')->count();

            if ($failedJobs > 0) {
                return [
                    'status' => 'warning',
                    'message' => $failedJobs . ' failed jobs',
                    'icon' => 'heroicon-o-queue-list',
                ];
            }

            return [
                'status' => 'healthy',
                'message' => 'No failed jobs',
                'icon' => 'heroicon-o-queue-list',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'healthy',
                'message' => 'Queue ready',
                'icon' => 'heroicon-o-queue-list',
            ];
        }
    }

    public function getPhpInfo(): array
    {
        return [
            'version' => PHP_VERSION,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time') . 's',
            'upload_max_filesize' => ini_get('upload_max_filesize'),
        ];
    }
}
