<?php

namespace App\Services\Settings\Providers;

use App\Contracts\SettingProvider;
use App\Services\Settings\Concerns\HasStatusIndicator;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class FulfillmentProvidersSettingsProvider implements SettingProvider
{
    // NOTE: Aggregator pages cannot save multi-group keys.
    // Converting this provider to target only the downloader group for consistency.
    use HasStatusIndicator;

    public function getGroup(): string
    {
        return 'downloader';
    }

    public function getLabel(): string
    {
        return 'Fulfillment Providers';
    }

    public function getCategory(): string
    {
        return 'fulfillment';
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function getSchema(): Schema
    {
        return Schema::make()
            ->components([
                Section::make(fn (Get $get) => $this->sectionWithConditionalIndicator('Digital Downloader', true, (bool) $get('downloader__enabled')))
                    ->description('Configure download link generation for digital products')
                    ->schema([
                        Toggle::make('downloader__enabled')
                            ->label('Enable Digital Downloader')
                            ->helperText('Toggle to enable or disable automatic download link generation')
                            ->default(true)
                            ->columnSpanFull(),
                        TextInput::make('downloader__link_expiry')
                            ->label('Download Link Expiry (hours)')
                            ->numeric()
                            ->default(48)
                            ->minValue(1)
                            ->maxValue(720)
                            ->helperText('Number of hours before download link expires'),
                        TextInput::make('downloader__max_downloads')
                            ->label('Maximum Downloads')
                            ->numeric()
                            ->default(5)
                            ->minValue(1)
                            ->maxValue(100)
                            ->helperText('Maximum number of downloads per order'),
                    ])
                    ->columns(2)
                    ->collapsed()
                    ->collapsible(),
            ]);
    }

    public function getDefaults(): array
    {
        return [
            'enabled' => true,
            'link_expiry' => 48,
            'max_downloads' => 5,
        ];
    }
}
