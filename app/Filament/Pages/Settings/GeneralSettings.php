<?php

namespace App\Filament\Pages\Settings;

use App\Services\Settings\SettingsManager;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class GeneralSettings extends BaseSettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $title = 'General Settings';

    protected static ?int $navigationSort = 1;

    protected static \UnitEnum|string|null $navigationGroup = 'Settings';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $slug = 'settings/general';

    protected function getProviders(): array
    {
        return [app(\App\Services\Settings\Providers\AppSettingsProvider::class)];
    }

    public function regenerateApiKey(): void
    {
        $manager = app(SettingsManager::class);
        $newKey = 'secret_'.Str::random(40);

        $manager->set('app.api_key', $newKey, 'encrypted', false);
        $manager->clearCache();

        // Reload the form with new key
        $this->loadSettings();

        Notification::make()
            ->title('API Key regenerated successfully')
            ->body('Your new API key has been generated. Make sure to update all integrations.')
            ->success()
            ->send();
    }
}
