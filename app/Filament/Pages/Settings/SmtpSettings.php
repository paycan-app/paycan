<?php

namespace App\Filament\Pages\Settings;

class SmtpSettings extends BaseSettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'SMTP';

    protected static ?string $title = 'SMTP Email Settings';

    protected static ?int $navigationSort = 21;

    protected static ?string $slug = 'settings/mail/smtp';

    protected static bool $shouldRegisterNavigation = false;

    protected function getProviders(): array
    {
        return [
            app(\App\Services\Settings\Providers\SmtpSettingsProvider::class),
        ];
    }
}
