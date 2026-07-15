<?php

namespace App\Filament\Resources\Products\Widgets;

use App\Models\ProductPrice;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class ProductPricesWidget extends TableWidget
{
    protected static ?string $heading = 'Prices';

    public ?Model $record = null;

    protected function getTableQuery(): Builder|Relation|null
    {
        if (! $this->record) {
            return null;
        }

        return ProductPrice::query()->where('product_id', $this->record->id);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('id')
                ->label('ID')
                ->searchable()
                ->sortable()
                ->copyable()->toggleable(true, true),

            TextColumn::make('title')
                ->label('Price Name')
                ->searchable()
                ->sortable()
                ->weight('medium'),

            IconColumn::make('is_active')
                ->label('Active')
                ->boolean()
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->trueColor('success')
                ->falseColor('gray')
                ->sortable()
                ->width('35px'),

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
                ->sortable()
                ->toggleable(true, true),

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
                ->toggleable(true, true),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            \Filament\Tables\Filters\SelectFilter::make('is_active')
                ->label('Status')
                ->options([
                    1 => 'Active',
                    0 => 'Inactive',
                ]),

            \Filament\Tables\Filters\SelectFilter::make('billing_period')
                ->options([
                    'once' => 'One-time',
                    'daily' => 'Daily',
                    'weekly' => 'Weekly',
                    'monthly' => 'Monthly',
                    'yearly' => 'Yearly',
                ]),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make()
                ->label('Add Price')
                ->icon('heroicon-o-plus')
                ->form($this->priceFormSchema())
                ->action(function (array $data): void {
                    ProductPrice::create([
                        'product_id' => $this->record->id,
                        'title' => $data['title'] ?? null,
                        'slug' => $data['slug'] ?? null,
                        'amount' => $data['amount'] ?? null,
                        'currency' => $data['currency'] ?? null,
                        'billing_period' => $data['billing_period'] ?? null,
                        'trial_days' => $data['trial_days'] ?? 0,
                        'is_active' => $data['is_active'] ?? true,
                        'description' => $data['description'] ?? null,
                    ]);
                })
                ->modalWidth('lg'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            \Filament\Actions\EditAction::make()
                ->label('Edit')
                ->icon('heroicon-o-pencil')
                ->form($this->priceFormSchema())
                ->fillForm(fn (ProductPrice $record) => [
                    'title' => $record->title,
                    'slug' => $record->slug,
                    'amount' => $record->amount,
                    'currency' => $record->currency,
                    'billing_period' => $record->billing_period,
                    'trial_days' => $record->trial_days,
                    'is_active' => $record->is_active,
                    'description' => $record->description,
                ])
                ->action(fn (ProductPrice $record, array $data) => $record->update($data))
                ->modalWidth('lg'),

            \Filament\Actions\DeleteAction::make()
                ->label('Delete')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->color('danger')
                ->action(fn (ProductPrice $record) => $record->delete()),
        ];
    }

    protected function getTableRecordAction(): ?string
    {
        return 'edit';
    }

    protected function getTableBulkActions(): array
    {
        return [
            \Filament\Actions\BulkActionGroup::make([
                \Filament\Actions\DeleteBulkAction::make(),
            ]),
        ];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No prices yet';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Create your first pricing option for this product.';
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-currency-dollar';
    }

    private function priceFormSchema(): array
    {
        return [
            \Filament\Forms\Components\TextInput::make('title')
                ->label('Price Name')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(function (?string $state, \Filament\Schemas\Components\Utilities\Set $set, \Filament\Schemas\Components\Utilities\Get $get) {
                    if ($state && ! $get('slug_manually_edited') && ! $get('slug')) {
                        $baseSlug = \Illuminate\Support\Str::slug(\Illuminate\Support\Str::ascii($state));
                        $slug = $baseSlug;
                        $counter = 1;

                        // Ensure unique slug by checking database
                        while (ProductPrice::where('slug', $slug)->exists()) {
                            $slug = $baseSlug.'-'.$counter;
                            $counter++;
                        }

                        $set('slug', $slug);
                        $set('slug_visible', true); // Make slug visible when auto-generated
                    }
                })
                ->hintActions([
                    \Filament\Actions\Action::make('viewSlug')
                        ->label(fn (\Filament\Schemas\Components\Utilities\Get $get): string => $get('slug') ?: 'No slug')
                        ->color('gray')
                        ->icon('heroicon-m-pencil-square')
                        ->action(function (\Filament\Schemas\Components\Utilities\Set $set) {
                            $set('slug_visible', true);
                            $set('slug_manually_edited', true);
                        }),
                ])
                ->placeholder('e.g., Regular, Premium, Student')
                ->columnSpanFull(),

            \Filament\Forms\Components\TextInput::make('slug')
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
                ->afterStateUpdated(function (?string $state, \Filament\Schemas\Components\Utilities\Set $set) {
                    if ($state) {
                        $cleanSlug = \Illuminate\Support\Str::slug(\Illuminate\Support\Str::ascii($state));
                        if ($cleanSlug !== $state) {
                            $set('slug', $cleanSlug);
                        }
                    }
                })
                ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => (bool) $get('slug_visible'))
                ->columnSpanFull(),

            \Filament\Forms\Components\TextInput::make('slug_visible')
                ->hidden()
                ->dehydrated(false)
                ->default(false),

            \Filament\Forms\Components\TextInput::make('slug_manually_edited')
                ->hidden()
                ->dehydrated(false)
                ->default(false),

            \Filament\Forms\Components\TextInput::make('amount')
                ->label('Price')
                ->required()
                ->numeric()
                ->prefix('$')
                ->step(0.01)
                ->default(0),

            \Filament\Forms\Components\Select::make('currency')
                ->label('Currency')
                ->options([
                    'USD' => 'USD ($)',
                    'EUR' => 'EUR (€)',
                    'GBP' => 'GBP (£)',
                    'CAD' => 'CAD ($)',
                ])
                ->required()
                ->default('USD'),

            \Filament\Forms\Components\Select::make('billing_period')
                ->label('Billing Period')
                ->options([
                    'once' => 'One-time',
                    'daily' => 'Daily',
                    'weekly' => 'Weekly',
                    'monthly' => 'Monthly',
                    'yearly' => 'Yearly',
                ])
                ->required()
                ->default('once')
                ->rules([
                    function () {
                        return function (string $attribute, $value, \Closure $fail) {
                            if ($value === 'once' && $this->record->type === 'subscription') {
                                $fail('Subscription products cannot have a one-time billing period. Please select a recurring period (daily, weekly, monthly, or yearly).');
                            }
                        };
                    },
                ])
                ->helperText(fn () => $this->record->type === 'subscription'
                    ? 'Subscription products must have a recurring billing period.'
                    : 'Choose how often this product should be billed.'),

            \Filament\Forms\Components\TextInput::make('trial_days')
                ->label('Trial Days')
                ->numeric()
                ->default(0)
                ->minValue(0)
                ->helperText('Number of trial days (0 for no trial)'),

            \Filament\Forms\Components\Toggle::make('is_active')
                ->label('Active')
                ->default(true)
                ->helperText('Whether this price is available for purchase'),

            \Filament\Forms\Components\Textarea::make('description')
                ->label('Description')
                ->placeholder('Optional description for this pricing option')
                ->columnSpanFull()
                ->rows(3),
        ];
    }
}
