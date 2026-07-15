<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->toggleable(true, true),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('product.title')
                    ->label('Product')
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('productPrice.title')
                    ->label('Price Plan')
                    ->searchable(),
                TextColumn::make('order.id')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('gateway')
                    ->searchable(),
                TextColumn::make('gateway_subscription_id')
                    ->searchable(),
                TextColumn::make('gateway_status')
                    ->searchable(),
                TextColumn::make('trial_ends_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('next_billing_date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('canceled_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at->format('M j, Y g:i A'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at->format('M j, Y g:i A'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(\App\Models\Subscription::getStatuses(), \App\Models\Subscription::getStatuses())),
                \Filament\Tables\Filters\SelectFilter::make('gateway')
                    ->options(['stripe' => 'Stripe', 'paypal' => 'PayPal']),
                \Filament\Tables\Filters\Filter::make('active')
                    ->label('Active Subscriptions')
                    ->query(fn ($query) => $query->where('status', 'active')),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
