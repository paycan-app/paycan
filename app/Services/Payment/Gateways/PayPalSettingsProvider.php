<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentSettingsProvider;
use App\Services\Settings\Concerns\HasStatusIndicator;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PayPalSettingsProvider implements PaymentSettingsProvider
{
    use HasStatusIndicator;

    public function getGroup(): string
    {
        return 'paypal';
    }

    public function getLabel(): string
    {
        return 'PayPal Payment Gateway';
    }

    public function getCategory(): string
    {
        return 'payment';
    }

    public function supportsSubscriptions(): bool
    {
        return settings('paypal.enable_subscriptions', true);
    }

    public function getGatewayClass(): string
    {
        return \App\Services\Payment\PayPalGateway::class;
    }

    public function isEnabled(): bool
    {
        return settings('paypal.enabled', false);
    }

    public function getSchema(): Schema
    {
        return Schema::make()
            ->components([
                Section::make(fn () => $this->sectionWithIndicator('PayPal Configuration', (bool) settings('paypal.enabled', false)))
                    ->description('Configure your PayPal payment gateway settings')
                    ->schema([
                        Toggle::make('paypal__enabled')
                            ->label('Enable PayPal')
                            ->helperText('Toggle to enable or disable PayPal payments')
                            ->default(false),

                        Select::make('paypal__mode')
                            ->label('Mode')
                            ->options([
                                'sandbox' => 'Sandbox (Testing)',
                                'live' => 'Live (Production)',
                            ])
                            ->default('sandbox')
                            ->helperText('Select sandbox for testing or live for production')
                            ->required(),

                        TextInput::make('paypal__client_id')
                            ->label('Client ID')
                            ->required()
                            ->placeholder('AXxxx...')
                            ->helperText('Your PayPal REST API Client ID')
                            ->columnSpanFull(),

                        TextInput::make('paypal__client_secret')
                            ->label('Client Secret')
                            ->password()->revealable()
                            ->required()
                            ->placeholder('EXxxx...')
                            ->helperText('Your PayPal REST API Client Secret (encrypted and never shown publicly)')
                            ->columnSpanFull(),

                        Toggle::make('paypal__enable_subscriptions')
                            ->label('Enable Subscription Support')
                            ->helperText('Allow this gateway to process recurring subscription payments')
                            ->default(true),

                        TextInput::make('paypal__webhook_id')
                            ->label('Webhook ID')
                            ->placeholder('WH-xxx...')
                            ->helperText('Webhook ID for verifying PayPal events')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public function getDefaults(): array
    {
        return [
            'enabled' => false,
            'mode' => 'sandbox',
            'enable_subscriptions' => true,
            'client_id' => '',
            'client_secret' => '',
            'webhook_id' => '',
        ];
    }
}
