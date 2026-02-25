<?php

namespace App\Helpers;

use Spatie\Permission\Models\Role;

/**
 * Centralized helper for dynamic role ID lookups.
 * Caches results per-request so the DB is hit only once per role name.
 */
class RoleHelper
{
    /**
     * In-memory cache of role name => role id.
     */
    protected static array $cache = [];

    /**
     * Get a role's ID by its name. Returns null if the role doesn't exist.
     *
     * @param  string  $name  e.g. 'admin', 'vendor', 'sales', 'user', 'student'
     */
    public static function id(string $name): ?int
    {
        if (! isset(static::$cache[$name])) {
            $role = Role::where('name', $name)->where('guard_name', 'web')->first();
            static::$cache[$name] = $role ? $role->id : null;
        }

        return static::$cache[$name];
    }

    /**
     * Convenience shortcuts for common roles.
     */
    public static function adminId(): ?int
    {
        return static::id('admin');
    }

    public static function vendorId(): ?int
    {
        return static::id('vendor');
    }

    public static function salesId(): ?int
    {
        return static::id('sales');
    }

    public static function userId(): ?int
    {
        return static::id('user');
    }

    public static function studentId(): ?int
    {
        return static::id('student');
    }

    /**
     * Clear the in-memory cache (useful in tests or after seeding).
     */
    public static function clearCache(): void
    {
        static::$cache = [];
    }
}
