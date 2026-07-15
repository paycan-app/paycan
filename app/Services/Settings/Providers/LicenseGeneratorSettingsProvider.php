<?php

namespace App\Services\Settings\Providers;

use App\Contracts\SettingProvider;
use App\Services\Settings\Concerns\HasStatusIndicator;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class LicenseGeneratorSettingsProvider implements SettingProvider
{
    use HasStatusIndicator;

    public function getGroup(): string
    {
        return 'license';
    }

    public function getLabel(): string
    {
        return 'License Generator';
    }

    public function getCategory(): string
    {
        return 'fulfillment';
    }

    public function isEnabled(): bool
    {
        return (bool) settings('license.enabled', true);
    }

    public function getSchema(): Schema
    {
        return Schema::make()
            ->components([
                Section::make(fn (Get $get) => $this->sectionWithConditionalIndicator('License Generator', true, (bool) $get('license__enabled')))
                    ->description('Configure automatic license key generation for digital products')
                    ->schema([
                        Toggle::make('license__enabled')
                            ->label('Enable License Generator')
                            ->helperText('Toggle to enable or disable automatic license generation')
                            ->default(true)
                            ->columnSpanFull(),
                        TextInput::make('license__key_length')
                            ->label('License Key Length')
                            ->numeric()
                            ->default(16)
                            ->minValue(8)
                            ->maxValue(64)
                            ->helperText('Number of characters in generated license keys'),
                        TextInput::make('license__prefix')
                            ->label('License Key Prefix')
                            ->maxLength(10)
                            ->placeholder('e.g., PROD-')
                            ->helperText('Optional prefix for all license keys'),
                    ])
                    ->columns(2),
            ]);
    }

    public function getDefaults(): array
    {
        return [
            'enabled' => true,
            'key_length' => 16,
            'prefix' => '',
        ];
    }
}
