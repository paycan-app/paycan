<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransactionsTable
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
                    ->searchable(),
                TextColumn::make('order.id')
                    ->searchable(),
                TextColumn::make('subscription.title')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('gateway')
                    ->searchable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('currency')
                    ->searchable(),
                TextColumn::make('gateway_transaction_id')
                    ->searchable(),
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
                    ->options(array_combine(\App\Models\Transaction::getStatuses(), \App\Models\Transaction::getStatuses())),
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->options(array_combine(\App\Models\Transaction::getTypes(), \App\Models\Transaction::getTypes())),
                \Filament\Tables\Filters\SelectFilter::make('gateway')
                    ->options(['stripe' => 'Stripe', 'paypal' => 'PayPal']),
                \Filament\Tables\Filters\Filter::make('completed')
                    ->query(fn ($query) => $query->where('status', 'completed')),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
