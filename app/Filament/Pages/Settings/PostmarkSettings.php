<?php

namespace App\Filament\Pages\Settings;

class PostmarkSettings extends BaseSettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Postmark';

    protected static ?string $title = 'Postmark Email Settings';

    protected static ?int $navigationSort = 23;

    protected static ?string $slug = 'settings/mail/postmark';

    protected static bool $shouldRegisterNavigation = false;

    protected function getProviders(): array
    {
        return [
            app(\App\Services\Settings\Providers\PostmarkSettingsProvider::class),
        ];
    }
}
