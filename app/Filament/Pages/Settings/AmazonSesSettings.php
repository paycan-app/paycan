<?php

namespace App\Filament\Pages\Settings;

class AmazonSesSettings extends BaseSettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Amazon SES';

    protected static ?string $title = 'Amazon SES Email Settings';

    protected static ?int $navigationSort = 24;

    protected static ?string $slug = 'settings/mail/amazon-ses';

    protected static bool $shouldRegisterNavigation = false;

    protected function getProviders(): array
    {
        return [
            app(\App\Services\Settings\Providers\AmazonSesSettingsProvider::class),
        ];
    }
}
