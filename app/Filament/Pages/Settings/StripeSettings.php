<?php

namespace App\Filament\Pages\Settings;

class StripeSettings extends BaseSettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Stripe';

    protected static ?string $title = 'Stripe Payment Gateway';

    protected static ?int $navigationSort = 11;

    protected static ?string $slug = 'settings/payment-providers/stripe';

    protected static bool $shouldRegisterNavigation = false;

    protected function getProviders(): array
    {
        return [
            app(\App\Services\Payment\Gateways\StripeSettingsProvider::class),
        ];
    }
}
