<?php

namespace App\Filament\Pages\Settings;

class GoogleSettings extends BaseSettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-lock-closed';

    protected static ?string $navigationLabel = 'Google';

    protected static ?string $title = 'Google OAuth Settings';

    protected static ?int $navigationSort = 31;

    protected static ?string $slug = 'settings/social-login/google';

    protected static bool $shouldRegisterNavigation = false;

    protected function getProviders(): array
    {
        return [
            app(\App\Services\Settings\Providers\GoogleSettingsProvider::class),
        ];
    }
}
