<?php

namespace App\Contracts;

use Filament\Schemas\Schema;

interface SettingProvider
{
    /**
     * Get the setting group identifier
     */
    public function getGroup(): string;

    /**
     * Get the display label for this provider
     */
    public function getLabel(): string;

    /**
     * Get the category this provider belongs to
     */
    public function getCategory(): string;

    /**
     * Get the Filament form schema for settings
     */
    public function getSchema(): Schema;

    /**
     * Check if this provider is enabled
     */
    public function isEnabled(): bool;

    /**
     * Get default values for settings
     */
    public function getDefaults(): array;
}
