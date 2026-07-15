<?php

namespace App\Filament\Pages\Settings;

class LicenseGeneratorSettings extends BaseSettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'License Generator';

    protected static ?string $title = 'License Generator Settings';

    protected static ?int $navigationSort = 42;

    protected static ?string $slug = 'settings/fulfillment/license-generator';

    protected static bool $shouldRegisterNavigation = false;

    protected function getProviders(): array
    {
        return [
            app(\App\Services\Settings\Providers\LicenseGeneratorSettingsProvider::class),
        ];
    }
}
