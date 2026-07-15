<?php

namespace App\Filament\Pages\Settings;

class ResendSettings extends BaseSettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Resend';

    protected static ?string $title = 'Resend Email Settings';

    protected static ?int $navigationSort = 25;

    protected static ?string $slug = 'settings/mail/resend';

    protected static bool $shouldRegisterNavigation = false;

    protected function getProviders(): array
    {
        return [
            app(\App\Services\Settings\Providers\ResendSettingsProvider::class),
        ];
    }
}
