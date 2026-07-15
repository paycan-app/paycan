<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Filament\Components\JsonKeyValueViewer;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Information')
                    ->schema([
                        TextEntry::make('id')
                            ->label('User ID')
                            ->copyable()
                            ->badge()
                            ->color('gray'),
                        TextEntry::make('name')
                            ->label('Full Name')
                            ->weight('bold'),
                        TextEntry::make('email')
                            ->label('Email Address')
                            ->copyable(),
                        IconEntry::make('email_verified_at')
                            ->label('Email Verified')
                            ->boolean()
                            ->getStateUsing(fn ($record) => $record->email_verified_at !== null),
                        TextEntry::make('email_verified_at')
                            ->label('Verified At')
                            ->dateTime()
                            ->placeholder('Not verified')
                            ->visible(fn ($record) => $record->email_verified_at !== null),
                    ])
                    ->columns(2),

                Section::make('Account Statistics')
                    ->schema([
                        TextEntry::make('orders_count')
                            ->label('Total Orders')
                            ->getStateUsing(fn ($record) => $record->orders()->count())
                            ->badge()
                            ->color('info'),
                        TextEntry::make('subscriptions_count')
                            ->label('Active Subscriptions')
                            ->getStateUsing(fn ($record) => $record->subscriptions()->where('status', 'active')->count())
                            ->badge()
                            ->color('success'),
                        TextEntry::make('total_spent')
                            ->label('Total Spent')
                            ->getStateUsing(fn ($record) => $record->orders()->sum('total'))
                            ->money('USD')
                            ->weight('bold'),
                        TextEntry::make('last_order_date')
                            ->label('Last Order')
                            ->getStateUsing(fn ($record) => $record->orders()->latest()->first()?->created_at)
                            ->dateTime()
                            ->placeholder('No orders'),

                        TextEntry::make('created_at')
                            ->label('Joined')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ])
                    ->columns(3),

                Section::make('Gateway Information')
                    ->schema([
                        JsonKeyValueViewer::make('gateway_data', 'Gateway Data'),
                    ])->columnSpanFull()->collapsed(),

            
            ]);
    }
}
