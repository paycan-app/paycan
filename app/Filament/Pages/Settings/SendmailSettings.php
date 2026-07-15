<?php

namespace App\Filament\Pages\Settings;

class SendmailSettings extends BaseSettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Sendmail';

    protected static ?string $title = 'Sendmail Settings';

    protected static ?int $navigationSort = 26;

    protected static ?string $slug = 'settings/mail/sendmail';

    protected static bool $shouldRegisterNavigation = false;

    protected function getProviders(): array
    {
        return [
            app(\App\Services\Settings\Providers\SendmailSettingsProvider::class),
        ];
    }
}
