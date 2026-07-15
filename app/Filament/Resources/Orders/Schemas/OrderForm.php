<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use App\Models\ProductPrice;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Details')
                    ->description('Basic order information')
                    ->schema([
                        Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')->required(),
                                TextInput::make('email')->email()->required(),
                            ])
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(fn (string $operation) => $operation !== 'edit'),
                        Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('product_price_id', null))
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(fn (string $operation) => $operation !== 'edit'),
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
                            })
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(fn (string $operation) => $operation !== 'edit'),
                        TextInput::make('order_number')
                            ->label('Order Number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'ORD-'.date('Y').'-'.str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT))
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(fn (string $operation) => $operation !== 'edit'),
                        Select::make('status')
                            ->label('Order Status')
                            ->options(fn () => array_combine(Order::getStatuses(), array_map('ucfirst', Order::getStatuses())))
                            ->required()
                            ->default('pending'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Pricing & Payment')
                    ->description('Order totals and payment information')
                    ->schema([
                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(fn (string $operation) => $operation !== 'edit'),
                        TextInput::make('total')
                            ->label('Total Amount')
                            ->required()
                            ->numeric()
                            ->step(0.01)
                            ->prefix('$')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(fn (string $operation) => $operation !== 'edit'),
                        TextInput::make('tax')
                            ->label('Tax Amount')
                            ->numeric()
                            ->step(0.01)
                            ->default(0)
                            ->prefix('$')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(fn (string $operation) => $operation !== 'edit'),
                        Select::make('currency')
                            ->label('Currency')
                            ->options([
                                'USD' => 'USD ($)',
                                'EUR' => 'EUR (€)',
                                'GBP' => 'GBP (£)',
                                'CAD' => 'CAD ($)',
                            ])
                            ->required()
                            ->default('USD')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(fn (string $operation) => $operation !== 'edit'),
                    ])
                    ->columns(4)
                    ->collapsible(),

                Section::make('Billing Information')
                    ->description('Customer billing details')
                    ->schema([
                        TextInput::make('billing_name')
                            ->label('Full Name')
                            ->required()
                            ->placeholder('Customer full name')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(fn (string $operation) => $operation !== 'edit'),
                        TextInput::make('billing_email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->placeholder('customer@example.com')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(fn (string $operation) => $operation !== 'edit'),
                        TextInput::make('billing_address')
                            ->label('Street Address')
                            ->placeholder('123 Main Street')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(fn (string $operation) => $operation !== 'edit'),
                        TextInput::make('billing_city')
                            ->label('City')
                            ->placeholder('City name')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(fn (string $operation) => $operation !== 'edit'),
                        TextInput::make('billing_state')
                            ->label('State/Province')
                            ->placeholder('State or Province')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(fn (string $operation) => $operation !== 'edit'),
                        TextInput::make('billing_zipcode')
                            ->label('ZIP/Postal Code')
                            ->placeholder('12345')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(fn (string $operation) => $operation !== 'edit'),
                        Select::make('billing_country')
                            ->label('Country')
                            ->options([
                                'US' => 'United States',
                                'CA' => 'Canada',
                                'GB' => 'United Kingdom',
                                'AU' => 'Australia',
                                'DE' => 'Germany',
                                'FR' => 'France',
                            ])
                            ->searchable()
                            ->default('US')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(fn (string $operation) => $operation !== 'edit'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Payment Gateway')
                    ->description('Gateway and transaction details')
                    ->schema([
                        Select::make('gateway')
                            ->label('Payment Gateway')
                            ->options(fn () => array_combine(Order::getGateways(), array_map('ucfirst', Order::getGateways())))
                            ->required()
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(fn (string $operation) => $operation !== 'edit'),
                        TextInput::make('gateway_order_id')
                            ->label('Gateway Order ID')
                            ->placeholder('Transaction ID from payment provider')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(fn (string $operation) => $operation !== 'edit'),
                        Textarea::make('gateway_data')
                            ->label('Gateway Data (JSON)')
                            ->placeholder('{"transaction_id": "...", "customer_id": "..."}')
                            ->helperText('Additional payment gateway data')
                            ->columnSpanFull()
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(fn (string $operation) => $operation !== 'edit'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Section::make('Fulfillment')
                    ->description('Order fulfillment and tracking')
                    ->schema([
                        Repeater::make('fulfillments')
                            ->relationship()
                            ->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'processing' => 'Processing',
                                        'shipped' => 'Shipped',
                                        'delivered' => 'Delivered',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->required()
                                    ->default('pending'),
                                TextInput::make('tracking_number')
                                    ->label('Tracking Number')
                                    ->placeholder('Enter tracking number'),
                                TextInput::make('carrier')
                                    ->label('Shipping Carrier')
                                    ->placeholder('UPS, FedEx, USPS, etc.'),
                                DateTimePicker::make('shipped_at')
                                    ->label('Shipped Date')
                                    ->displayFormat('M j, Y H:i'),
                                DateTimePicker::make('delivered_at')
                                    ->label('Delivered Date')
                                    ->displayFormat('M j, Y H:i'),
                                Textarea::make('notes')
                                    ->label('Fulfillment Notes')
                                    ->placeholder('Internal notes about fulfillment')
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->itemLabel(fn (array $state): ?string => $state['status'] ?? 'New Fulfillment')
                            ->addActionLabel('Add Fulfillment')
                            ->columnSpanFull()
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(fn (string $operation) => $operation !== 'edit'),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('Additional Information')
                    ->description('Notes and metadata')
                    ->schema([
                        Textarea::make('customer_note')
                            ->label('Customer Note')
                            ->placeholder('Note from customer')
                            ->helperText('Any special instructions from the customer')
                            ->columnSpanFull()
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(fn (string $operation) => $operation !== 'edit'),
                        KeyValue::make('meta')
                            ->label('Order Metadata')
                            ->keyLabel('Attribute Name')
                            ->valueLabel('Attribute Value')
                            ->reorderable()
                            ->addActionLabel('Add Custom Field')
                            ->helperText('Add custom order attributes and metadata')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
