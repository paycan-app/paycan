<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Filament\Components\JsonKeyValueViewer;


class TransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Transaction Details')
                    ->schema([
                        TextEntry::make('gateway_transaction_id')
                            ->label('Transaction ID')
                            ->weight('bold')
                            ->copyable(),
                        TextEntry::make('type')
                            ->label('Type')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                            ->color(fn (string $state): string => match ($state) {
                                'charge' => 'success',
                                'refund' => 'warning',
                                'subscription_create', 'subscription_renew' => 'info',
                                'subscription_cancel' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'completed' => 'success',
                                'failed' => 'danger',
                                'refunded' => 'gray',
                                default => 'gray',
                            }),
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->money('currency')
                            ->weight('bold'),
                        TextEntry::make('currency')
                            ->label('Currency'),
                        TextEntry::make('gateway')
                            ->label('Gateway')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                    ])
                    ->columns(3),

                Section::make('Customer & Relations')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Customer')
                            ->placeholder('-'),
                        TextEntry::make('user.email')
                            ->label('Customer Email')
                            ->placeholder('-'),
                        TextEntry::make('order.order_number')
                            ->label('Related Order')
                            ->placeholder('No related order'),
                        TextEntry::make('subscription.title')
                            ->label('Related Subscription')
                            ->placeholder('No related subscription'),
                    ])
                    ->columns(2),

                Section::make('Gateway Information')
                    ->schema([
                        TextEntry::make('gateway')->label('Payment Gateway')->badge(),
                        TextEntry::make('gateway_transaction_id')->label('Gateway Transaction ID')->placeholder('-')->copyable(),
                        // Use reusable viewer for JSON
                        JsonKeyValueViewer::make('gateway_data', 'Gateway Data'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Section::make('Metadata')
                    ->schema([
                        // Use reusable viewer for JSON
                        JsonKeyValueViewer::make('meta', 'Metadata'),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Updated')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }
}
