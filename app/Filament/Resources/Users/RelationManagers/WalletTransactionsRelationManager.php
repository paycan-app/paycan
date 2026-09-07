<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Models\WalletTransaction;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WalletTransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'walletTransactions';

    protected static ?string $title = 'Credit History & Usage Logs';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('description')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime('M j, Y g:i A')
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at?->format('M j, Y g:i:s A'))
                    ->sortable(),

                TextColumn::make('wallet.type')
                    ->label('Wallet')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'premium' => 'warning',
                        'basic' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => ucfirst($state ?? 'basic')),

                TextColumn::make('type')
                    ->label('Direction')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'credit' => 'success',
                        'debit' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => $state === 'credit' ? '+ Credit' : '- Debit'),

                TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'usage' => 'primary',
                        'subscription_grant', 'subscription_renewal', 'order_purchase' => 'success',
                        'manual_adjustment' => 'warning',
                        'subscription_reset' => 'gray',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn (WalletTransaction $record): string => ($record->type === 'credit' ? '+' : '-').number_format((float) $record->amount, 2))
                    ->weight('bold')
                    ->color(fn (WalletTransaction $record): string => $record->type === 'credit' ? 'success' : 'gray')
                    ->sortable(),

                TextColumn::make('balance_after')
                    ->label('Balance After')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('reference_id')
                    ->label('Ref / Task ID')
                    ->copyable()
                    ->searchable()
                    ->toggleable(true, true),

                TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->wrap()
                    ->limit(50),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('Filter Action')
                    ->options([
                        'usage' => 'Usage Log (AI Agent / Task)',
                        'subscription_grant' => 'Subscription Grant',
                        'subscription_renewal' => 'Subscription Renewal',
                        'order_purchase' => 'One-time Order Purchase',
                        'manual_adjustment' => 'Manual Admin Adjustment',
                        'subscription_reset' => 'Quota Reset',
                    ]),

                SelectFilter::make('type')
                    ->label('Direction')
                    ->options([
                        'credit' => 'Credits Added (+)',
                        'debit' => 'Credits Deducted (-)',
                    ]),

                SelectFilter::make('wallet_id')
                    ->label('Wallet')
                    ->relationship('wallet', 'type')
                    ->preload(),
            ])
            ->recordActions([
                Action::make('viewDetails')
                    ->label('Details')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Credit Transaction & Usage Details')
                    ->form([
                        TextInput::make('id')
                            ->label('Transaction ID')
                            ->disabled()
                            ->default(fn (WalletTransaction $record) => $record->id),

                        TextInput::make('wallet_type')
                            ->label('Wallet Type')
                            ->disabled()
                            ->default(fn (WalletTransaction $record) => ucfirst($record->wallet?->type ?? 'basic')),

                        TextInput::make('action_name')
                            ->label('Action')
                            ->disabled()
                            ->default(fn (WalletTransaction $record) => ucwords(str_replace('_', ' ', $record->action))),

                        TextInput::make('amount_formatted')
                            ->label('Amount')
                            ->disabled()
                            ->default(fn (WalletTransaction $record) => ($record->type === 'credit' ? '+' : '-').$record->amount.' (Balance after: '.$record->balance_after.')'),

                        TextInput::make('reference_id')
                            ->label('Reference ID / Run ID')
                            ->disabled()
                            ->default(fn (WalletTransaction $record) => $record->reference_id ?? 'N/A'),

                        TextInput::make('description')
                            ->label('Description')
                            ->disabled()
                            ->default(fn (WalletTransaction $record) => $record->description ?? 'N/A'),

                        Textarea::make('meta_json')
                            ->label('Usage Metadata (JSON)')
                            ->rows(6)
                            ->disabled()
                            ->default(fn (WalletTransaction $record) => $record->meta ? json_encode($record->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : 'No metadata attached'),
                    ]),
            ]);
    }
}
