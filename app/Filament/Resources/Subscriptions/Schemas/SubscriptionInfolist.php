<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use App\Models\Subscription;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Filament\Components\JsonKeyValueViewer;

class SubscriptionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->components([
                // Row 1: 2-2 (50/50)
                Section::make('Subscription Overview')
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('title')
                            ->label('Subscription Title')
                            ->weight('bold'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'trialing' => 'info',
                                'past_due' => 'warning',
                                'canceled' => 'danger',
                                'incomplete', 'incomplete_expired' => 'gray',
                                default => 'gray',
                            }),
                        IconEntry::make('is_on_trial')
                            ->label('On Trial')
                            ->boolean()
                            ->getStateUsing(fn (\App\Models\Subscription $record): bool => $record->isOnTrial()),
                        IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean()
                            ->getStateUsing(fn (\App\Models\Subscription $record): bool => $record->isActive()),
                    ])
                    ->columns(4),

                

                // Row 2: Full width
                Section::make('Billing & Trial Information')
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('trial_ends_at')
                            ->label('Trial Ends')
                            ->dateTime()
                            ->placeholder('No trial')
                            ->visible(fn ($record) => $record->trial_ends_at),
                        TextEntry::make('next_billing_date')
                            ->label('Next Billing')
                            ->dateTime()
                            ->placeholder('Not scheduled')
                            ->weight('bold'),
                        TextEntry::make('ends_at')
                            ->label('Expires')
                            ->dateTime()
                            ->placeholder('No expiration'),
                        TextEntry::make('canceled_at')
                            ->label('Canceled')
                            ->dateTime()
                            ->placeholder('Not canceled')
                            ->visible(fn ($record) => $record->canceled_at),
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ])
                    ->columns(2)
                    ->visible(fn (\App\Models\Subscription $record) => $record->trial_ends_at || $record->next_billing_date || $record->ends_at || $record->canceled_at),

                    Section::make('Customer & Product')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Customer'),
                        TextEntry::make('user.email')
                            ->label('Customer Email'),
                        TextEntry::make('product.title')
                            ->label('Product')
                            ->weight('bold'),
                        TextEntry::make('productPrice.title')
                            ->label('Price Plan'),
                        TextEntry::make('productPrice.amount')
                            ->label('Price')
                            ->money('productPrice.currency'),
                        TextEntry::make('order.order_number')
                            ->label('Initial Order')
                            ->placeholder('No order'),
                    ])
                    ->columns(3),

                // Row 3: 2-2 (50/50) collapsed by default
                Section::make('Payment Gateway')
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('gateway')
                            ->label('Gateway')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                        TextEntry::make('gateway_subscription_id')
                            ->label('Gateway Subscription ID')
                            ->copyable()
                            ->placeholder('Not set'),
                        TextEntry::make('gateway_status')
                            ->label('Gateway Status')
                            ->placeholder('Not set'),
                            // Replace inline gateway_data renderer with reusable viewer (same format as meta)
                            JsonKeyValueViewer::make('gateway_data', 'Gateway Data'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn (\App\Models\Subscription $record) => ($record->gateway ?? null)
                        || ($record->gateway_subscription_id ?? null)
                        || ($record->gateway_status ?? null)
                        || (is_array($record->gateway_data) ? ! empty($record->gateway_data) : (is_string($record->gateway_data) ? trim($record->gateway_data) !== '' : false))),

                Section::make('Additional Information')
                    ->columnSpan(2)
                    ->schema([
                        // Replace meta renderer with reusable viewer
                        JsonKeyValueViewer::make('meta', 'Metadata'),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn (\App\Models\Subscription $record) => ($record->gateway ?? null)
                        || ($record->gateway_subscription_id ?? null)
                        || ($record->gateway_status ?? null)
                        || (is_array($record->gateway_data) ? ! empty($record->gateway_data) : (is_string($record->gateway_data) ? trim($record->gateway_data) !== '' : false))),

                Section::make('Additional Information')
                    ->columnSpan(2)
                    ->schema([
                        RepeatableEntry::make('meta')
                            ->label('Metadata')
                            ->getStateUsing(function (\App\Models\Subscription $record) {
                                $state = $record->meta ?? [];

                                if (is_string($state)) {
                                    $decoded = json_decode($state, true);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                        $state = $decoded;
                                    }
                                }

                                if (! is_array($state) || empty($state)) {
                                    return [];
                                }

                                return collect($state)->map(function ($value, $key) {
                                    $formatted = (is_scalar($value) || $value === null)
                                        ? (string) ($value ?? '')
                                        : json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                                    return [
                                        'key' => (string) $key,
                                        'value' => $formatted,
                                    ];
                                })->values()->all();
                            })
                            ->schema([
                                TextEntry::make('key')
                                    ->weight('medium')
                                    ->columnSpan(3),
                                TextEntry::make('value')
                                    ->copyable()
                                    ->columnSpan(9),
                            ])
                            ->columns(12)
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
