<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ImageColumn::make('image')
                //     ->label('Image')
                //     ->circular()
                //     ->defaultImageUrl('/images/product-placeholder.png'),
                 TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()->toggleable(true, true),
            
                TextColumn::make('title')
                    ->label('Product Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                // TextColumn::make('type')
                //     ->label('Type')
                //     ->badge()
                //     ->color(fn (string $state): string => match ($state) {
                //         'physical' => 'success',
                //         'digital' => 'info',
                //         'service' => 'warning',
                //         'subscription' => 'primary',
                //         default => 'secondary',
                //     })
                //     ->searchable()
                //     ->sortable(),
                TextColumn::make('prices_count')
                    ->label('Prices')
                    ->counts('prices')
                    ->badge()
                    ->color(fn (int $state): string => $state === 0 ? 'danger' : 'gray'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
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
                Filter::make('active')
                    ->label('Active Products Only')
                    ->query(fn ($query) => $query->where('is_active', true)),
                TrashedFilter::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordAction('edit')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
