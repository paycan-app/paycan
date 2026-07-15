<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
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
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('email_verified_at')
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
                \Filament\Tables\Filters\Filter::make('verified')
                    ->label('Verified Only')
                    ->query(fn ($query) => $query->whereNotNull('email_verified_at')),
                \Filament\Tables\Filters\Filter::make('unverified')
                    ->label('Unverified Only')
                    ->query(fn ($query) => $query->whereNull('email_verified_at')),
                \Filament\Tables\Filters\Filter::make('has_orders')
                    ->label('Has Orders')
                    ->query(fn ($query) => $query->has('orders')),
                \Filament\Tables\Filters\Filter::make('has_subscriptions')
                    ->label('Has Subscriptions')
                    ->query(fn ($query) => $query->has('subscriptions')),
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
