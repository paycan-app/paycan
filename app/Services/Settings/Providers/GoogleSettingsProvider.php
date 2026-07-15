<?php

namespace App\Services\Settings\Providers;

use App\Contracts\SettingProvider;
use App\Services\Settings\Concerns\HasStatusIndicator;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class GoogleSettingsProvider implements SettingProvider
{
    use HasStatusIndicator;

    public function getGroup(): string
    {
        return 'google';
    }

    public function getLabel(): string
    {
        return 'Google OAuth';
    }

    public function getCategory(): string
    {
        return 'auth';
    }

    public function isEnabled(): bool
    {
        return (bool) settings('google.enabled', false);
    }

    public function getSchema(): Schema
    {
        return Schema::make()
            ->components([
                Section::make(fn (Get $get) => $this->sectionWithIndicator('Google OAuth Configuration', (bool) $get('google__enabled')))
                    ->description('Configure Google social login')
                    ->schema([
                        Toggle::make('google__enabled')
                            ->label('Enable Google Login')
                            ->helperText('Allow users to login with Google')
                            ->default(false),
                        TextInput::make('google__client_id')
                            ->label('Client ID')
                            ->placeholder('xxx.apps.googleusercontent.com')
                            ->helperText('Your Google OAuth Client ID')
                            ->columnSpanFull(),
                        TextInput::make('google__client_secret')
                            ->label('Client Secret')
                            ->password()->revealable()
                            ->placeholder('GOCSPX-xxx')
                            ->helperText('Your Google OAuth Client Secret (encrypted)')
                            ->columnSpanFull(),
                        TextInput::make('google__redirect')
                            ->label('Redirect URI')
                            ->default('/auth/google/callback')
                            ->helperText('OAuth callback URL')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public function getDefaults(): array
    {
        return [
            'enabled' => false,
            'client_id' => config('services.google.client_id', ''),
            'client_secret' => config('services.google.client_secret', ''),
            'redirect' => config('services.google.redirect', '/auth/google/callback'),
        ];
    }
}
