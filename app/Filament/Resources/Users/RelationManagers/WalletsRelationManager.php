<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Models\Wallet;
use App\Services\Wallet\WalletService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WalletsRelationManager extends RelationManager
{
    protected static string $relationship = 'wallets';

    protected static ?string $title = 'Credit Wallets';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->options([
                        'basic' => 'Basic Credit',
                        'premium' => 'Premium Credit',
                    ])
                    ->required(),
                TextInput::make('balance')
                    ->numeric()
                    ->default(0.0000)
                    ->step(0.0001)
                    ->required(),
                TextInput::make('currency')
                    ->default('credits')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                TextColumn::make('type')
                    ->label('Wallet Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'premium' => 'warning',
                        'basic' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state).' Credits')
                    ->sortable(),

                TextColumn::make('balance')
                    ->label('Balance')
                    ->weight('bold')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('currency')
                    ->label('Unit')
                    ->color('gray'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M j, Y g:i A')
                    ->since()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New Wallet')
                    ->icon('heroicon-o-plus'),
            ])
            ->recordActions([
                Action::make('viewHistory')
                    ->label('History')
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->modalHeading(fn (Wallet $record): string => ucfirst($record->type).' Credit Ledger & Usages')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->form(function (Wallet $record): array {
                        $transactions = $record->transactions()->latest()->take(10)->get();
                        if ($transactions->isEmpty()) {
                            return [
                                TextInput::make('empty_state')
                                    ->label('Transactions')
                                    ->disabled()
                                    ->default('No transactions recorded for this wallet yet.'),
                            ];
                        }

                        $fields = [];
                        foreach ($transactions as $idx => $tx) {
                            $sign = $tx->type === 'credit' ? '+' : '-';
                            $title = "[{$tx->created_at->format('M j, Y H:i')}] {$sign}{$tx->amount} ({$tx->action})";
                            $desc = $tx->description ? " - {$tx->description}" : '';
                            $fields[] = TextInput::make("tx_{$idx}")
                                ->label($title)
                                ->disabled()
                                ->default("Balance after: {$tx->balance_after}{$desc}".($tx->reference_id ? " [Ref: {$tx->reference_id}]" : ''));
                        }

                        return $fields;
                    }),
                Action::make('adjustBalance')
                    ->label('Adjust Balance')
                    ->icon('heroicon-o-scale')
                    ->color('primary')
                    ->form([
                        Select::make('adjustment_type')
                            ->label('Action')
                            ->options([
                                'add' => 'Add Credits',
                                'deduct' => 'Deduct Credits',
                                'set' => 'Set Absolute Balance',
                            ])
                            ->default('add')
                            ->required(),

                        TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->required()
                            ->minValue(0.0001)
                            ->step(0.0001),

                        TextInput::make('description')
                            ->label('Reason / Description')
                            ->placeholder('e.g., Manual bonus, Support compensation')
                            ->maxLength(255),
                    ])
                    ->action(function (Wallet $record, array $data): void {
                        $walletService = app(WalletService::class);
                        $amount = (float) $data['amount'];
                        $actionType = $data['adjustment_type'];
                        $description = $data['description'] ?: 'Manual admin adjustment';

                        try {
                            if ($actionType === 'add') {
                                $walletService->addCredits(
                                    target: $record,
                                    amount: $amount,
                                    type: $record->type,
                                    action: 'manual_adjustment',
                                    description: $description
                                );
                            } elseif ($actionType === 'deduct') {
                                $walletService->deductCredits(
                                    target: $record,
                                    amount: $amount,
                                    type: $record->type,
                                    description: $description
                                );
                            } elseif ($actionType === 'set') {
                                $walletService->resetCredits(
                                    target: $record,
                                    newBalance: $amount,
                                    type: $record->type,
                                    action: 'manual_adjustment',
                                    description: $description
                                );
                            }

                            Notification::make()
                                ->title('Wallet balance updated')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Failed to adjust balance')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ]);
    }
}
