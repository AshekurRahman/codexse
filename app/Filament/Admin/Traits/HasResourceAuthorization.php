<?php

namespace App\Filament\Admin\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Trait for adding authorization checks to Filament resources.
 *
 * Resources can override specific methods or define permissions in
 * the $authorizedRoles and $permissions properties.
 *
 * Authorization hierarchy:
 * 1. Must be an admin (is_admin = true)
 * 2. Super admins (super_admin role) have full access
 * 3. Users with specific permissions have access to those actions
 * 4. If no roles/permissions are configured, admins have full access (fallback)
 */
trait HasResourceAuthorization
{
    /**
     * Get the permission name for this resource.
     * Override in the resource class to customize.
     */
    public static function getPermissionName(): string
    {
        return static::$permissionName ?? strtolower(class_basename(static::getModel()));
    }

    /**
     * Check if the user can view any records.
     */
    public static function canViewAny(): bool
    {
        return static::checkPermission('view_any');
    }

    /**
     * Check if the user can view a specific record.
     */
    public static function canView(Model $record): bool
    {
        return static::checkPermission('view');
    }

    /**
     * Check if the user can create records.
     */
    public static function canCreate(): bool
    {
        return static::checkPermission('create');
    }

    /**
     * Check if the user can edit a record.
     */
    public static function canEdit(Model $record): bool
    {
        return static::checkPermission('edit');
    }

    /**
     * Check if the user can delete a record.
     */
    public static function canDelete(Model $record): bool
    {
        return static::checkPermission('delete');
    }

    /**
     * Check if the user can bulk delete records.
     */
    public static function canDeleteAny(): bool
    {
        return static::checkPermission('delete_any');
    }

    /**
     * Central permission check method.
     */
    protected static function checkPermission(string $action): bool
    {
        $user = auth()->user();

        // Must be logged in and be an admin
        if (!$user || !$user->is_admin) {
            return false;
        }

        // Super admins have all permissions
        if (static::isSuperAdmin($user)) {
            return true;
        }

        // Check if user has specific permission
        $permissionName = $action . '_' . static::getPermissionName();
        $hasPermission = static::userHasPermission($user, $permissionName);

        if ($hasPermission !== null) {
            return $hasPermission;
        }

        // Fallback: If no permission system is configured, allow all admins
        // This ensures the admin panel works even without seeded permissions
        return true;
    }

    /**
     * Check if user has a specific permission.
     * Returns null if permission system is not configured.
     */
    protected static function userHasPermission($user, string $permission): ?bool
    {
        try {
            // Check if the permission exists in the database
            $permissionExists = \Spatie\Permission\Models\Permission::where('name', $permission)
                ->where('guard_name', 'web')
                ->exists();

            if (!$permissionExists) {
                // Permission not configured, return null to trigger fallback
                return null;
            }

            return $user->hasPermissionTo($permission);
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            // Permission doesn't exist, return null to trigger fallback
            return null;
        } catch (\Exception $e) {
            // Any other error (e.g., table doesn't exist), return null to trigger fallback
            return null;
        }
    }

    /**
     * Check if user is a super admin (has all permissions).
     */
    protected static function isSuperAdmin($user): bool
    {
        try {
            return $user->hasRole('super_admin');
        } catch (\Exception $e) {
            // If roles aren't set up, fall back to false
            return false;
        }
    }
}
