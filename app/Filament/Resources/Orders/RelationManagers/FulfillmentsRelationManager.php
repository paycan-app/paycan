<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Models\Fulfillment;
use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FulfillmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'fulfillments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->label('Status')
                    ->options(fn () => array_combine(Fulfillment::getStatuses(), array_map('ucfirst', Fulfillment::getStatuses())))
                    ->required()
                    ->default('pending')
                    ->hintAction(
                        Action::make('viewId')
                            ->label(fn (Get $get): string => 'ID: '.($get('id') ?: 'No ID'))
                            ->color('gray')
                            ->icon('heroicon-m-key')
                            ->visible(fn (string $operation): bool => $operation === 'create')
                            ->action(function (Set $set) {
                                $set('id_visible', true);
                            })
                    )
                    ->suffixAction(
                        Action::make('showId')
                            ->label(fn (Get $get): string => 'ID: '.($get('id') ?: 'N/A'))
                            ->color('gray')
                            ->disabled()
                            ->visible(fn (string $operation): bool => $operation === 'edit')
                    ),
                TextInput::make('id')
                    ->label('Fulfillment ID')
                    ->maxLength(50)
                    ->placeholder('Optional: Leave empty to auto-generate')
                    ->helperText('Custom ID (ULID will be generated if empty)')
                    ->visible(fn (Get $get, string $operation): bool => $operation === 'create' && (bool) $get('id_visible'))
                    ->dehydrated(),
                TextInput::make('id_visible')
                    ->hidden()
                    ->dehydrated(false)
                    ->default(false),
                Select::make('type')
                    ->label('Type')
                    ->options([
                        'digital' => 'Digital',
                        'physical' => 'Physical',
                        'service' => 'Service',
                        'subscription_access' => 'Subscription Access',
                    ])
                    ->required()
                    ->default('physical'),
                TextInput::make('tracking_id')
                    ->label('Tracking ID')
                    ->maxLength(255),
                Select::make('provider')
                    ->label('Provider')
                    ->options(fn () => array_combine(Fulfillment::getProviders(), Fulfillment::getProviders()))
                    ->searchable(),
                Textarea::make('meta')
                    ->label('Meta (JSON)')
                    ->placeholder('{"key": "value"}')
                    ->columnSpanFull(),
                DateTimePicker::make('fulfilled_at')
                    ->label('Fulfilled At'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'processing' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('tracking_id')
                    ->label('Tracking ID')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('provider'),
                TextColumn::make('fulfilled_at')
                    ->dateTime(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
