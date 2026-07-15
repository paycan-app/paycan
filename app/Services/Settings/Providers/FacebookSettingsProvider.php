<?php

namespace App\Services\Settings\Providers;

use App\Contracts\SettingProvider;
use App\Services\Settings\Concerns\HasStatusIndicator;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class FacebookSettingsProvider implements SettingProvider
{
    use HasStatusIndicator;

    public function getGroup(): string
    {
        return 'facebook';
    }

    public function getLabel(): string
    {
        return 'Facebook OAuth';
    }

    public function getCategory(): string
    {
        return 'auth';
    }

    public function isEnabled(): bool
    {
        return (bool) settings('facebook.enabled', false);
    }

    public function getSchema(): Schema
    {
        return Schema::make()
            ->components([
                Section::make(fn (Get $get) => $this->sectionWithIndicator('Facebook OAuth Configuration', (bool) $get('facebook__enabled')))
                    ->description('Configure Facebook social login')
                    ->schema([
                        Toggle::make('facebook__enabled')
                            ->label('Enable Facebook Login')
                            ->helperText('Allow users to login with Facebook')
                            ->default(false),
                        TextInput::make('facebook__client_id')
                            ->label('App ID')
                            ->placeholder('123456789')
                            ->helperText('Your Facebook App ID')
                            ->columnSpanFull(),
                        TextInput::make('facebook__client_secret')
                            ->label('App Secret')
                            ->password()->revealable()
                            ->placeholder('xxx')
                            ->helperText('Your Facebook App Secret (encrypted)')
                            ->columnSpanFull(),
                        TextInput::make('facebook__redirect')
                            ->label('Redirect URI')
                            ->default('/auth/facebook/callback')
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
            'client_id' => config('services.facebook.client_id', ''),
            'client_secret' => config('services.facebook.client_secret', ''),
            'redirect' => config('services.facebook.redirect', '/auth/facebook/callback'),
        ];
    }
}
