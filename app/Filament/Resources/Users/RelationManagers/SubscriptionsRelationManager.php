<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;

class SubscriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'subscriptions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->copyable()
                    ->toggleable(true, true)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Subscription')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.title')
                    ->label('Product')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('productPrice.title')
                    ->label('Price')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'active' => 'success',
                        'trialing' => 'info',
                        'past_due' => 'warning',
                        'canceled' => 'danger',
                        'incomplete', 'incomplete_expired' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('gateway')
                    ->sortable(),
                TextColumn::make('gateway_subscription_id')
                    ->label('Gateway Sub ID')
                    ->copyable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('next_billing_date')
                    ->label('Next Billing')
                    ->dateTime('M j, Y')
                    ->since()
                    ->tooltip(fn ($record) => $record->next_billing_date?->format('M j, Y g:i A'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('trial_ends_at')
                    ->label('Trial Ends')
                    ->dateTime('M j, Y')
                    ->since()
                    ->tooltip(fn ($record) => $record->trial_ends_at?->format('M j, Y g:i A'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at?->format('M j, Y g:i A'))
                    ->sortable(),
                TextColumn::make('canceled_at')
                    ->label('Canceled')
                    ->dateTime('M j, Y')
                    ->since()
                    ->tooltip(fn ($record) => $record->canceled_at?->format('M j, Y g:i A'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(\App\Models\Subscription::getStatuses(), \App\Models\Subscription::getStatuses())),
                \Filament\Tables\Filters\SelectFilter::make('gateway')
                    ->options(['stripe' => 'Stripe', 'paypal' => 'PayPal']),
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
