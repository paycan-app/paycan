<?php

namespace App\Filament\Pages\Settings;

class PayPalSettings extends BaseSettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'PayPal';

    protected static ?string $title = 'PayPal Payment Gateway';

    protected static ?int $navigationSort = 12;

    protected static ?string $slug = 'settings/payment-providers/paypal';

    protected static bool $shouldRegisterNavigation = false;

    protected function getProviders(): array
    {
        return [
            app(\App\Services\Payment\Gateways\PayPalSettingsProvider::class),
        ];
    }
}
