<?php

namespace App\Filament\Admin\Traits;

use App\Services\ActivityLogService;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait for logging Filament resource actions.
 *
 * Add this trait to Filament resources to automatically log
 * create, update, and delete operations.
 */
trait LogsResourceActions
{
    /**
     * Get the log category for this resource.
     * Override to customize.
     */
    protected static function getLogCategory(): string
    {
        return 'admin';
    }

    /**
     * Get the resource name for logging.
     */
    protected static function getLogResourceName(): string
    {
        return static::$logResourceName ?? strtolower(class_basename(static::getModel()));
    }

    /**
     * Log record creation.
     */
    public static function logCreated(Model $record): void
    {
        $resourceName = static::getLogResourceName();

        ActivityLogService::log(
            action: 'admin_create',
            category: static::getLogCategory(),
            description: "Created {$resourceName} #{$record->getKey()}",
            subject: $record,
            properties: [
                'resource' => static::class,
                'resource_name' => $resourceName,
                'record_id' => $record->getKey(),
                'record_data' => static::getLogDataFromRecord($record),
            ]
        );
    }

    /**
     * Log record update.
     */
    public static function logUpdated(Model $record, array $changes = []): void
    {
        $resourceName = static::getLogResourceName();

        ActivityLogService::log(
            action: 'admin_update',
            category: static::getLogCategory(),
            description: "Updated {$resourceName} #{$record->getKey()}",
            subject: $record,
            properties: [
                'resource' => static::class,
                'resource_name' => $resourceName,
                'record_id' => $record->getKey(),
            ],
            oldValues: $changes['old'] ?? null,
            newValues: $changes['new'] ?? null
        );
    }

    /**
     * Log record deletion.
     */
    public static function logDeleted(Model $record): void
    {
        $resourceName = static::getLogResourceName();

        ActivityLogService::log(
            action: 'admin_delete',
            category: static::getLogCategory(),
            description: "Deleted {$resourceName} #{$record->getKey()}",
            subject: $record,
            properties: [
                'resource' => static::class,
                'resource_name' => $resourceName,
                'record_id' => $record->getKey(),
            ]
        );
    }

    /**
     * Get loggable data from a record.
     * Override to customize what data is logged.
     */
    protected static function getLogDataFromRecord(Model $record): array
    {
        // Get only non-sensitive attributes
        $hidden = ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'];
        $data = $record->toArray();

        foreach ($hidden as $key) {
            unset($data[$key]);
        }

        return $data;
    }
}
