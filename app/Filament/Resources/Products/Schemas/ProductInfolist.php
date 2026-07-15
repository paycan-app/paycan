<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('Product ID')
                    ->copyable()
                    ->badge()
                    ->color('gray'),
                TextEntry::make('title'),
                TextEntry::make('slug')
                    ->copyable()
                    ->badge()
                    ->color('primary'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('type'),
                ImageEntry::make('image')
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('meta')
                    ->placeholder('-')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state) && ! empty($state)) {
                            return collect($state)->map(fn ($value, $key) => "{$key}: {$value}")->join(', ');
                        }

                        return $state ?: '-';
                    })
                    ->columnSpanFull(),
                TextEntry::make('file')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Product $record): bool => $record->trashed()),

                // Prices list, similar to comments under a post
                RepeatableEntry::make('prices')
                    ->label('Prices')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Name')
                            ->badge(),
                        TextEntry::make('slug')
                            ->label('Slug')
                            ->badge()
                            ->color('gray'),
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->formatStateUsing(fn ($state) => $state !== null ? number_format((float) $state, 2) : '-'),
                        TextEntry::make('currency')
                            ->label('Currency'),
                        TextEntry::make('billing_period')
                            ->label('Period')
                            ->badge()
                            ->color('primary'),
                        TextEntry::make('trial_days')
                            ->label('Trial')
                            ->formatStateUsing(fn ($state) => $state > 0 ? "{$state} days" : 'No trial')
                            ->color(fn ($state) => $state > 0 ? 'info' : 'gray'),
                        IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean(),
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
