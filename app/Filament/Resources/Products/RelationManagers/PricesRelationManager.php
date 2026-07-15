<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Price Name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, Set $set, Get $get) {
                        // Only auto-generate slug if slug is empty and hasn't been manually edited
                        if ($state && ! $get('slug_manually_edited') && ! $get('slug')) {
                            $slug = Str::slug(Str::ascii($state));
                            $set('slug', $slug);
                        }
                    })
                    ->hintActions([
                        Action::make('viewSlug')
                            ->label(fn (Get $get): string => $get('slug') ?: 'No slug')
                            ->color('gray')
                            ->icon('heroicon-m-pencil-square')
                            ->action(function (Set $set) {
                                $set('slug_visible', true);
                                $set('slug_manually_edited', true);
                            }),
                    ])
                    ->placeholder('e.g., Regular, Premium, Student')
                    ->columnSpanFull(),

                TextInput::make('slug')
                    ->label('Price Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ProductPrice::class, 'slug', ignoreRecord: true)
                    ->rules(['alpha_dash'])
                    ->validationMessages([
                        'unique' => 'A price with this URL slug already exists. Please choose a different slug.',
                    ])
                    ->helperText('Latin characters only. Must be unique across all prices.')
                    ->placeholder('price-slug')
                    ->afterStateUpdated(function (?string $state, Set $set) {
                        if ($state) {
                            $cleanSlug = Str::slug(Str::ascii($state));
                            if ($cleanSlug !== $state) {
                                $set('slug', $cleanSlug);
                            }
                        }
                    })
                    ->visible(fn (Get $get): bool => (bool) $get('slug_visible')),

                TextInput::make('slug_visible')
                    ->hidden()
                    ->dehydrated(false)
                    ->default(false),

                TextInput::make('slug_manually_edited')
                    ->hidden()
                    ->dehydrated(false)
                    ->default(false),

                TextInput::make('amount')
                    ->label('Price')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->step(0.01)
                    ->default(0),

                Select::make('currency')
                    ->label('Currency')
                    ->options([
                        'USD' => 'USD ($)',
                        'EUR' => 'EUR (€)',
                        'GBP' => 'GBP (£)',
                        'CAD' => 'CAD ($)',
                    ])
                    ->required()
                    ->default('USD'),

                Select::make('billing_period')
                    ->label('Billing Period')
                    ->options([
                        'once' => 'One-time',
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                        'yearly' => 'Yearly',
                    ])
                    ->required()
                    ->default('once'),

                TextInput::make('trial_days')
                    ->label('Trial Days')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->helperText('Number of trial days (0 for no trial)'),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Whether this price is available for purchase'),

                Textarea::make('description')
                    ->label('Description')
                    ->placeholder('Optional description for this pricing option')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                IconColumn::make('is_active')
                    ->label('')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable()
                    ->width('40px'),

                TextColumn::make('title')
                    ->label('Price Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('amount')
                    ->label('Price')
                    ->money(fn ($record) => $record->currency ?? 'USD')
                    ->sortable(),

                TextColumn::make('billing_period')
                    ->label('Billing Period')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'once' => 'gray',
                        'monthly' => 'success',
                        'yearly' => 'warning',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('trial_days')
                    ->label('Trial')
                    ->formatStateUsing(fn ($state) => $state > 0 ? $state.' days' : 'No trial')
                    ->color(fn ($state) => $state > 0 ? 'info' : 'gray')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ]),
                SelectFilter::make('billing_period')
                    ->options([
                        'once' => 'One-time',
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                        'yearly' => 'Yearly',
                    ]),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No prices yet')
            ->emptyStateDescription('Create your first pricing option for this product.')
            ->emptyStateIcon('heroicon-o-currency-dollar');
    }
}
