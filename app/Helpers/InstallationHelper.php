<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class InstallationHelper
{
    /**
     * Check if the application is installed
     */
    public static function isInstalled(): bool
    {
        return File::exists(storage_path('installed'));
    }

    /**
     * Mark the application as installed
     */
    public static function markAsInstalled(): void
    {
        File::put(storage_path('installed'), now()->toDateTimeString());
    }

    /**
     * Check if database is connected and has tables
     */
    public static function isDatabaseReady(): bool
    {
        try {
            DB::connection()->getPdo();

            return Schema::hasTable('users') && Schema::hasTable('admin_users');
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get installation status
     */
    public static function getInstallationStatus(): array
    {
        return [
            'installed' => self::isInstalled(),
            'database_ready' => self::isDatabaseReady(),
            'env_exists' => File::exists(base_path('.env')),
            'key_generated' => ! empty(config('app.key')),
        ];
    }

    /**
     * Check if admin user exists
     */
    public static function hasAdminUser(): bool
    {
        try {
            return \App\Models\AdminUser::exists();
        } catch (\Exception $e) {
            return false;
        }
    }
}
