<?php

namespace App\Support;

class Roles
{
    /**
     * The current demo-session role, or null if nobody has "logged in" yet.
     */
    public static function current(): ?string
    {
        return session('role');
    }

    /**
     * The display name typed in at the login screen.
     */
    public static function currentName(): ?string
    {
        return session('user_name');
    }

    public static function label(?string $role = null): string
    {
        $role = $role ?? static::current();

        return config("roles.role_labels.$role", 'Guest');
    }

    /**
     * "Name(Role)" formatted string used to attribute log entries and
     * requests to the real logged-in user — e.g. "Jane(Manager)".
     */
    public static function currentNameWithRole(): string
    {
        $name = static::currentName() ?? 'Guest';

        return "{$name}(" . static::label() . ")";
    }

    /**
     * Whether the current session role is allowed to perform $permission.
     * Permissions not present in config/roles.php are treated as open to
     * any logged-in role.
     */
    public static function can(string $permission): bool
    {
        $role = static::current();

        if (! $role) {
            return false;
        }

        $allowed = config("roles.permissions.$permission");

        // Unlisted permission => any logged-in role may do it.
        if ($allowed === null) {
            return true;
        }

        return in_array($role, $allowed, true);
    }

    public static function isLoggedIn(): bool
    {
        return static::current() !== null;
    }
}
