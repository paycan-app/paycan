<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use App\Models\ProductPrice;
use App\Models\Subscription;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Subscription Management')
                    ->tabs([
                        Tab::make('Subscription Details')
                            ->icon('heroicon-o-arrow-path')
                            ->schema([
                                Section::make('Basic Information')
                                    ->schema([
                                        Select::make('user_id')
                                            ->label('Customer')
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Select::make('product_id')
                                            ->label('Product')
                                            ->relationship('product', 'title')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(fn (callable $set) => $set('product_price_id', null)),
                                        Select::make('product_price_id')
                                            ->label('Product Price')
                                            ->relationship('productPrice', 'title')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->options(function (callable $get) {
                                                $productId = $get('product_id');
                                                if (! $productId) {
                                                    return [];
                                                }

                                                return ProductPrice::where('product_id', $productId)
                                                    ->where('is_active', true)
                                                    ->pluck('title', 'id');
                                            }),
                                        Select::make('order_id')
                                            ->label('Initial Order')
                                            ->relationship('order', 'order_number')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Select the order that created this subscription'),
                                    ])
                                    ->columns(3),

                                Section::make('Subscription Settings')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Subscription Title')
                                            ->required()
                                            ->placeholder('e.g., Monthly Premium Plan'),
                                        Select::make('status')
                                            ->label('Status')
                                            ->options(fn () => array_combine(Subscription::getStatuses(), array_map('ucfirst', Subscription::getStatuses())))
                                            ->required()
                                            ->default('active'),
                                    ])
                                    ->columns(2),
                            ]),

                        Tab::make('Billing & Dates')
                            ->icon('heroicon-o-calendar-days')
                            ->schema([
                                Section::make('Trial Period')
                                    ->description('Trial and activation dates')
                                    ->schema([
                                        DateTimePicker::make('trial_ends_at')
                                            ->label('Trial Ends At')
                                            ->displayFormat('M j, Y H:i')
                                            ->helperText('When the trial period ends'),
                                    ]),

                                Section::make('Billing Dates')
                                    ->description('Subscription billing and expiration')
                                    ->schema([
                                        DateTimePicker::make('next_billing_date')
                                            ->label('Next Billing Date')
                                            ->displayFormat('M j, Y H:i')
                                            ->helperText('When the next payment is due'),
                                        DateTimePicker::make('ends_at')
                                            ->label('Ends At')
                                            ->displayFormat('M j, Y H:i')
                                            ->helperText('When the subscription expires'),
                                        DateTimePicker::make('canceled_at')
                                            ->label('Canceled At')
                                            ->displayFormat('M j, Y H:i')
                                            ->helperText('When the subscription was canceled'),
                                    ])
                                    ->columns(3),
                            ]),

                        Tab::make('Gateway Integration')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Section::make('Payment Gateway')
                                    ->description('Gateway-specific information')
                                    ->schema([
                                        Select::make('gateway')
                                            ->label('Payment Gateway')
                                            ->options([
                                                'stripe' => 'Stripe',
                                                'paypal' => 'PayPal',
                                                'square' => 'Square',
                                            ])
                                            ->required(),
                                        TextInput::make('gateway_subscription_id')
                                            ->label('Gateway Subscription ID')
                                            ->placeholder('ID from payment gateway'),
                                        TextInput::make('gateway_status')
                                            ->label('Gateway Status')
                                            ->placeholder('Status from payment gateway'),
                                    ])
                                    ->columns(3),

                                Section::make('Gateway Data')
                                    ->schema([
                                        Textarea::make('gateway_data')
                                            ->label('Gateway Data (JSON)')
                                            ->placeholder('{"subscription_id": "...", "customer_id": "...", "plan_id": "..."}')
                                            ->helperText('Additional data from payment gateway')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
