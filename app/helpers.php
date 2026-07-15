<?php

use App\Services\Settings\SettingsManager;

if (! function_exists('settings')) {
    /**
     * Get or set settings values
     */
    function settings(?string $key = null, mixed $default = null): mixed
    {
        $manager = app(SettingsManager::class);

        if ($key === null) {
            return $manager;
        }

        return $manager->get($key, $default);
    }
}
