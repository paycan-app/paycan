<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\Transaction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Transaction Management')
                    ->tabs([
                        Tab::make('Transaction Details')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Section::make('Basic Information')
                                    ->schema([
                                        Select::make('user_id')
                                            ->label('Customer')
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Select::make('type')
                                            ->label('Transaction Type')
                                            ->options(fn () => array_combine(Transaction::getTypes(), array_map(function ($type) {
                                                return ucwords(str_replace('_', ' ', $type));
                                            }, Transaction::getTypes())))
                                            ->required(),
                                        Select::make('status')
                                            ->label('Status')
                                            ->options(fn () => array_combine(Transaction::getStatuses(), array_map('ucfirst', Transaction::getStatuses())))
                                            ->required()
                                            ->default('pending'),
                                    ])
                                    ->columns(3),

                                Section::make('Amount & Currency')
                                    ->schema([
                                        TextInput::make('amount')
                                            ->label('Amount')
                                            ->required()
                                            ->numeric()
                                            ->step(0.01)
                                            ->prefix('$'),
                                        Select::make('currency')
                                            ->label('Currency')
                                            ->options([
                                                'USD' => 'USD ($)',
                                                'EUR' => 'EUR (€)',
                                                'GBP' => 'GBP (£)',
                                                'CAD' => 'CAD ($)',
                                            ])
                                            ->required()
                                            ->default('USD'),
                                    ])
                                    ->columns(2),
                            ]),

                        Tab::make('Relations')
                            ->icon('heroicon-o-link')
                            ->schema([
                                Section::make('Related Records')
                                    ->description('Link this transaction to orders or subscriptions')
                                    ->schema([
                                        Select::make('order_id')
                                            ->label('Related Order')
                                            ->relationship('order', 'order_number')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Select an order (optional)'),
                                        Select::make('subscription_id')
                                            ->label('Related Subscription')
                                            ->relationship('subscription', 'title')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Select a subscription (optional)'),
                                    ])
                                    ->columns(2),
                            ]),

                        Tab::make('Gateway Information')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Section::make('Payment Gateway')
                                    ->description('Manual transaction details')
                                    ->schema([
                                        TextInput::make('gateway')
                                            ->label('Payment Gateway')
                                            ->default('manual')
                                            ->disabled()
                                            ->dehydrated(true)
                                            ->required(),
                                        TextInput::make('gateway_transaction_id')
                                            ->label('Reference ID')
                                            ->required()
                                            ->placeholder('Enter a reference ID for this manual transaction'),
                                    ])
                                    ->columns(2),
                            ]),

                        Tab::make('Metadata')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Additional Information')
                                    ->description('Custom metadata and notes')
                                    ->schema([
                                        KeyValue::make('meta')
                                            ->label('Transaction Metadata')
                                            ->keyLabel('Attribute Name')
                                            ->valueLabel('Attribute Value')
                                            ->reorderable()
                                            ->addActionLabel('Add Custom Field')
                                            ->helperText('Add custom transaction attributes')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
