<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Customer Management')
                    ->tabs([
                        Tab::make('Account Information')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Basic Details')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Full Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Enter customer name')
                                            ->hintAction(
                                                Action::make('viewId')
                                                    ->label(fn (Get $get): string => $get('id') ?: 'No ID')
                                                    ->color('gray')
                                                    ->icon('heroicon-m-pencil-square')
                                                    ->visible(fn (string $operation): bool => $operation === 'create')
                                                    ->action(function (Set $set) {
                                                        $set('id_visible', true);
                                                    })
                                            )
                                            ->suffixAction(
                                                Action::make('showId')
                                                    ->label(fn (Get $get): string => 'ID: '.($get('id') ?: 'Auto-generated'))
                                                    ->color('gray')
                                                    ->disabled()
                                                    ->visible(fn (string $operation): bool => $operation === 'edit')
                                            ),
                                        TextInput::make('id')
                                            ->label('User ID')
                                            ->maxLength(50)
                                            ->placeholder('Optional: Leave empty to auto-generate')
                                            ->helperText('Custom ID (ULID will be generated if empty)')
                                            ->visible(fn (Get $get, string $operation): bool => $operation === 'create' && (bool) $get('id_visible'))
                                            ->dehydrated(),
                                        TextInput::make('id_visible')
                                            ->hidden()
                                            ->dehydrated(false)
                                            ->default(false),
                                        TextInput::make('email')
                                            ->label('Email Address')
                                            ->email()
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->placeholder('customer@example.com'),
                                    ])
                                    ->columns(2),
                            ]),

                        Tab::make('Security & Verification')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Section::make('Account Security')
                                    ->description('Password and verification settings')
                                    ->schema([
                                        TextInput::make('password')
                                            ->label('Password')
                                            ->password()->revealable()
                                            ->dehydrateStateUsing(fn (?string $state) => $state ? Hash::make($state) : null)
                                            ->dehydrated(fn (?string $state) => filled($state))
                                            ->required(fn (string $operation): bool => $operation === 'create')
                                            ->minLength(8)
                                            ->placeholder('Enter new password'),
                                        DateTimePicker::make('email_verified_at')
                                            ->label('Email Verified At')
                                            ->displayFormat('M j, Y H:i')
                                            ->helperText('When the customer verified their email'),
                                    ])
                                    ->columns(2),
                            ]),

                        Tab::make('Gateway Data')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Section::make('Payment Gateway Information')
                                    ->description('Customer information stored by payment gateways')
                                    ->schema([
                                        KeyValue::make('gateway_data')
                                            ->label('Gateway Data')
                                            ->keyLabel('Attribute Name')
                                            ->valueLabel('Attribute Value')
                                            ->reorderable()
                                            ->addActionLabel('Add Field')
                                            ->helperText('Payment gateway customer data as key/value pairs')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
