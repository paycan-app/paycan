<?php

namespace App\Filament\Pages\Settings;

class PaymentProvidersSettings extends BaseSettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Payment Providers';

    protected static ?string $title = 'Payment Providers';

    protected static ?int $navigationSort = 999;

    protected static ?string $slug = 'settings/payment-providers';

    protected static bool $shouldRegisterNavigation = false;

    protected function getProviders(): array
    {
        return [
            app(\App\Services\Payment\Gateways\StripeSettingsProvider::class),
            app(\App\Services\Payment\Gateways\PayPalSettingsProvider::class),
        ];
    }
}
