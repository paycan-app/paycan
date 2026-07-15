<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentSettingsProvider;
use App\Services\Settings\Concerns\HasStatusIndicator;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StripeSettingsProvider implements PaymentSettingsProvider
{
    use HasStatusIndicator;

    public function getGroup(): string
    {
        return 'stripe';
    }

    public function getLabel(): string
    {
        return 'Stripe Payment Gateway';
    }

    public function getCategory(): string
    {
        return 'payment';
    }

    public function supportsSubscriptions(): bool
    {
        return settings('stripe.enable_subscriptions', true);
    }

    public function getGatewayClass(): string
    {
        return \App\Services\Payment\StripeGateway::class;
    }

    public function isEnabled(): bool
    {
        return settings('stripe.enabled', false);
    }

    public function getSchema(): Schema
    {
        return Schema::make()
            ->components([
                Section::make(fn () => $this->sectionWithIndicator('Stripe Configuration', (bool) settings('stripe.enabled', false)))
                    ->description('Configure your Stripe payment gateway settings')
                    ->schema([
                        Toggle::make('stripe__enabled')
                            ->label('Enable Stripe')
                            ->helperText('Toggle to enable or disable Stripe payments')
                            ->default(false),
                        TextInput::make('stripe__api_key')
                            ->label('Secret Key')
                            ->password()->revealable()
                            ->required()
                            ->placeholder('sk_test_...')
                            ->helperText('Your Stripe secret key (encrypted and never shown publicly)')
                            ->columnSpanFull(),
                        TextInput::make('stripe__publishable_key')
                            ->label('Publishable Key')
                            ->required()
                            ->placeholder('pk_test_...')
                            ->helperText('Your Stripe publishable key')
                            ->columnSpanFull(),
                        Toggle::make('stripe__enable_subscriptions')
                            ->label('Enable Subscription Support')
                            ->helperText('Allow this gateway to process recurring subscription payments')
                            ->default(true),
                        Toggle::make('stripe__automatic_tax')
                            ->label('Automatic Tax (Stripe Tax)')
                            ->helperText('Let Stripe calculate and collect tax at checkout. Requires Stripe Tax to be set up in your Stripe dashboard.')
                            ->default(false),
                        TextInput::make('stripe__webhook_secret')
                            ->label('Webhook Secret')
                            ->password()->revealable()
                            ->placeholder('whsec_...')
                            ->helperText('Webhook signing secret for verifying Stripe events')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public function getDefaults(): array
    {
        return [
            'enabled' => false,
            'enable_subscriptions' => true,
            'automatic_tax' => false,
            'api_key' => '',
            'publishable_key' => '',
            'webhook_secret' => '',
        ];
    }
}
