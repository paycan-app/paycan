<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Filament\Components\JsonKeyValueViewer;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->components([
                // Row 1: Billing (3) + Note (1)
                Section::make('Customer & Product')
                    ->columnSpan(3)
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Customer')
                            ->weight('bold')
                            ->placeholder('-')
                            ->columnSpan(1),
                        TextEntry::make('user.email')
                            ->label('Customer Email')
                            ->copyable()
                            ->placeholder('-')
                            ->columnSpan(1),
                        TextEntry::make('product.title')
                            ->label('Product')
                            ->weight('bold')
                            ->placeholder('-')
                            ->columnSpan(1),
                        TextEntry::make('productPrice.title')
                            ->label('Price Option')
                            ->placeholder('-')
                            ->columnSpan(1),
                        TextEntry::make('created_at')->label('Created')->dateTime()->columnSpan(1),
                        TextEntry::make('updated_at')->label('Updated')->dateTime()->columnSpan(1),
                        TextEntry::make('deleted_at')
                            ->label('Deleted')
                            ->dateTime()
                            ->visible(fn (Order $record): bool => $record->trashed())
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Section::make('Note')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('id')
                            ->label('Order ID')
                            ->copyable()
                            ->columnSpanFull(),
                        TextEntry::make('customer_note')
                            ->label('Customer Note')
                            ->placeholder('-')
                            ->weight('medium')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                // Row 2: Customer & Product (full width)
                Section::make('Billing Information')
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('billing_name')->label('Billing Name')->columnSpan(1),
                        TextEntry::make('billing_email')->label('Billing Email')->columnSpan(1),
                        TextEntry::make('billing_address')->label('Address')->placeholder('-')->columnSpan(1),
                        TextEntry::make('billing_city')->label('City')->placeholder('-')->columnSpan(1),
                        TextEntry::make('billing_state')->label('State')->placeholder('-')->columnSpan(1),
                        TextEntry::make('billing_zipcode')->label('Zip Code')->placeholder('-')->columnSpan(1),
                        TextEntry::make('billing_country')->label('Country')->placeholder('-')->columnSpan(1),
                    ])
                    ->columns(2),

                // Row 3: Order Details (2) + Payment / Additional (2+2 split)

                Section::make('Order Details')
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('id')->label('Order ID')->copyable()->columnSpan(1),
                        TextEntry::make('order_number')->label('Order Number')->weight('bold')->columnSpan(1),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'processing' => 'info',
                                'completed' => 'success',
                                'failed' => 'danger',
                                'cancelled' => 'gray',
                                'refunded' => 'warning',
                                default => 'gray',
                            })
                            ->columnSpan(1),
                        TextEntry::make('total')->label('Total Amount')->money('currency')->weight('bold')->columnSpan(1),
                        TextEntry::make('currency')->label('Currency')->columnSpan(1),
                        TextEntry::make('tax')->label('Tax')->money('currency')->columnSpan(1),
                        TextEntry::make('quantity')->label('Quantity')->numeric()->columnSpan(1),
                    ])
                    ->columns(2),

                // Payment Information: 50% width, collapsed

                Section::make('Payment Information')
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('gateway')->label('Payment Gateway')->badge(),
                        TextEntry::make('gateway_order_id')->label('Gateway Order ID')->placeholder('-')->copyable(),
                        JsonKeyValueViewer::make('gateway_data', 'Gateway Data'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                // Additional Information: 50% width, collapsed

                Section::make('Additional Information')
                    ->columnSpan(2)
                    ->schema([
                        JsonKeyValueViewer::make('meta', 'Metadata'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
