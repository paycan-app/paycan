<?php

namespace App\Services\Settings\Providers;

use App\Contracts\SettingProvider;
use App\Services\Settings\Concerns\HasStatusIndicator;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class GitHubSettingsProvider implements SettingProvider
{
    use HasStatusIndicator;

    public function getGroup(): string
    {
        return 'github';
    }

    public function getLabel(): string
    {
        return 'GitHub OAuth';
    }

    public function getCategory(): string
    {
        return 'auth';
    }

    public function isEnabled(): bool
    {
        return (bool) settings('github.enabled', false);
    }

    public function getSchema(): Schema
    {
        return Schema::make()
            ->components([
                Section::make(fn (Get $get) => $this->sectionWithIndicator('GitHub OAuth Configuration', (bool) $get('github__enabled')))
                    ->description('Configure GitHub social login')
                    ->schema([
                        Toggle::make('github__enabled')
                            ->label('Enable GitHub Login')
                            ->helperText('Allow users to login with GitHub')
                            ->default(false),
                        TextInput::make('github__client_id')
                            ->label('Client ID')
                            ->placeholder('Iv1.xxx')
                            ->helperText('Your GitHub OAuth Client ID')
                            ->columnSpanFull(),
                        TextInput::make('github__client_secret')
                            ->label('Client Secret')
                            ->password()->revealable()
                            ->placeholder('xxx')
                            ->helperText('Your GitHub OAuth Client Secret (encrypted)')
                            ->columnSpanFull(),
                        TextInput::make('github__redirect')
                            ->label('Redirect URI')
                            ->default('/auth/github/callback')
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
            'client_id' => config('services.github.client_id', ''),
            'client_secret' => config('services.github.client_secret', ''),
            'redirect' => config('services.github.redirect', '/auth/github/callback'),
        ];
    }
}
