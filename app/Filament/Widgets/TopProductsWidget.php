<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class TopProductsWidget extends TableWidget
{
    protected static ?int $sort = 4;

    protected static ?string $heading = 'Top Performing Products';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->withCount('prices as order_count')
                    ->withSum('prices as total_revenue', 'amount')
                    ->orderByDesc('total_revenue')
                    ->limit(5)
            )
            ->columns([
                
                TextColumn::make('title')
                    ->label('Product')
                    ->searchable()
                    ->weight('bold'),


                TextColumn::make('order_count')
                    ->label('Orders')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(function (Product $record) {
                        return Order::whereHas('productPrice', function ($query) use ($record) {
                            $query->where('product_id', $record->id);
                        })->count();
                    }),

                TextColumn::make('total_revenue')
                    ->label('Revenue')
                    ->getStateUsing(function (Product $record) {
                        return Order::whereHas('productPrice', function ($query) use ($record) {
                            $query->where('product_id', $record->id);
                        })->sum('total');
                    })
                    ->money('USD')
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn (Product $record) => $record->is_active ? 'Active' : 'Inactive')
                    ->color(fn (Product $record) => $record->is_active ? 'success' : 'gray'),
            ])
            ->defaultSort('total_revenue', 'desc')
            ->defaultPaginationPageOption(5);
    }
}
