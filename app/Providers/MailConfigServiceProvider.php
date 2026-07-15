<?php

namespace App\Providers;

use App\Services\Mail\DynamicMailConfigService;
use App\Services\Settings\DynamicConfigService;
use Illuminate\Support\ServiceProvider;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configure all application settings from database when the application boots
        try {
            // Configure mail settings
            $this->app->make(DynamicMailConfigService::class)->configure();

            // Configure payment gateways, social login, and other services
            $this->app->make(DynamicConfigService::class)->configure();
        } catch (\Exception $e) {
            // Silently fail if settings table doesn't exist (e.g., during migrations)
            logger()->debug('Failed to configure settings from database: '.$e->getMessage());
        }
    }
}
