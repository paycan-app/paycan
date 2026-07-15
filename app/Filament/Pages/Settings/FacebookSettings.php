<?php

namespace App\Filament\Pages\Settings;

class FacebookSettings extends BaseSettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-lock-closed';

    protected static ?string $navigationLabel = 'Facebook';

    protected static ?string $title = 'Facebook OAuth Settings';

    protected static ?int $navigationSort = 32;

    protected static ?string $slug = 'settings/social-login/facebook';

    protected static bool $shouldRegisterNavigation = false;

    protected function getProviders(): array
    {
        return [
            app(\App\Services\Settings\Providers\FacebookSettingsProvider::class),
        ];
    }
}
