<?php

namespace App\Support;

class Package
{
    /**
     * Get the currently active package slug (silver, gold, platinum).
     */
    public static function current(): string
    {
        $package = strtolower((string) config('packages.default', 'gold'));

        if (! array_key_exists($package, config('packages.packages', []))) {
            return 'gold';
        }

        return $package;
    }

    /**
     * Check if a given feature is enabled in the current package.
     */
    public static function has(string $feature): bool
    {
        $current = self::current();
        $features = config("packages.packages.{$current}.features", []);

        return in_array($feature, $features, true);
    }

    /**
     * Get quota limit for the current package.
     */
    public static function limit(string $quota): mixed
    {
        $current = self::current();

        return config("packages.packages.{$current}.quotas.{$quota}");
    }

    /**
     * Get all active features for the current package.
     *
     * @return array<string>
     */
    public static function features(): array
    {
        $current = self::current();

        return config("packages.packages.{$current}.features", []);
    }

    /**
     * Check if current package matches the given package name.
     */
    public static function is(string $package): bool
    {
        return self::current() === strtolower($package);
    }
}
