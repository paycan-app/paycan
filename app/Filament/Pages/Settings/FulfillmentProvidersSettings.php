<?php

namespace App\Filament\Pages\Settings;

class FulfillmentProvidersSettings extends BaseSettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Fulfillment Providers';

    protected static ?string $title = 'Fulfillment Providers';

    protected static ?int $navigationSort = 999;

    protected static ?string $slug = 'settings/fulfillment-providers';

    protected static bool $shouldRegisterNavigation = false;

    protected function getProviders(): array
    {
        return [app(\App\Services\Settings\Providers\FulfillmentProvidersSettingsProvider::class)];
    }
}
