<?php

namespace App\Filament\Pages\Settings;

class MailgunSettings extends BaseSettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Mailgun';

    protected static ?string $title = 'Mailgun Email Settings';

    protected static ?int $navigationSort = 22;

    protected static ?string $slug = 'settings/mail/mailgun';

    protected static bool $shouldRegisterNavigation = false;

    protected function getProviders(): array
    {
        return [
            app(\App\Services\Settings\Providers\MailgunSettingsProvider::class),
        ];
    }
}
