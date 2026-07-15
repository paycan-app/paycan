<?php

namespace App\Services\Settings\Providers;

use App\Contracts\SettingProvider;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AppSettingsProvider implements SettingProvider
{
    public function getGroup(): string
    {
        return 'app';
    }

    public function getLabel(): string
    {
        return 'Application Settings';
    }

    public function getCategory(): string
    {
        return 'app';
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function getSchema(): Schema
    {
        return Schema::make()
            ->components([
                Section::make('General')
                    ->description('Basic application settings')
                    ->schema([
                        TextInput::make('app__name')
                            ->label('Application Name')
                            ->required()
                            ->default(config('app.name'))
                            ->helperText('The name of your application')
                            ->columnSpanFull(),

                        TextInput::make('app__url')
                            ->label('Application URL')
                            ->url()
                            ->required()
                            ->default(config('app.url'))
                            ->helperText('The base URL for this application')
                            ->columnSpanFull(),

                        TextInput::make('app__client_url')
                            ->label('Client URL')
                            ->url()
                            ->default('')
                            ->helperText('The base URL for your application that connects to this instance.')
                            ->columnSpanFull(),

                        TextInput::make('app__timezone')
                            ->label('Timezone')
                            ->required()
                            ->default(config('app.timezone'))
                            ->helperText('Application timezone (e.g., UTC, America/New_York)')
                            ->columnSpanFull(),

                        Select::make('app__locale')
                            ->label('Default Language')
                            ->required()
                            ->options([
                                'en' => 'English',
                                'ar' => 'العربية (Arabic)',
                            ])
                            ->default(config('app.locale'))
                            ->helperText('Default language for all users. Users can override this with their own preference.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('API Secret Key')
                    ->description('Manage API secret key for external integrations.')
                    ->schema([
                        TextInput::make('app__api_key')
                            ->label('API Secret Key')
                            ->default(fn () => $this->generateApiKey())
                            ->readOnly()
                            ->copyable()
                            ->columnSpanFull(),

                        ViewField::make('regenerate_button')
                            ->view('filament.forms.components.regenerate-api-key-button')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }

    protected function generateApiKey(): string
    {
        return 'secret_'.Str::random(40);
    }

    public function getDefaults(): array
    {
        return [
            'name' => config('app.name', 'Paycan'),
            'client_url' => '',   
            'url' => config('app.url', 'http://localhost'),
            'timezone' => config('app.timezone', 'UTC'),
            'locale' => config('app.locale', 'en'),
            'api_key' => $this->generateApiKey(),
        ];
    }
}
