<?php

namespace App\Filament\Pages\Settings;

class GitHubSettings extends BaseSettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-lock-closed';

    protected static ?string $navigationLabel = 'GitHub';

    protected static ?string $title = 'GitHub OAuth Settings';

    protected static ?int $navigationSort = 33;

    protected static ?string $slug = 'settings/social-login/github';

    protected static bool $shouldRegisterNavigation = false;

    protected function getProviders(): array
    {
        return [
            app(\App\Services\Settings\Providers\GitHubSettingsProvider::class),
        ];
    }
}
