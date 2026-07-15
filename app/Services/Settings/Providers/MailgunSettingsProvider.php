<?php

namespace App\Services\Settings\Providers;

use App\Contracts\SettingProvider;
use App\Services\Settings\Concerns\HasStatusIndicator;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MailgunSettingsProvider implements SettingProvider
{
    use HasStatusIndicator;

    public function getGroup(): string
    {
        return 'mailgun';
    }

    public function getLabel(): string
    {
        return 'Mailgun Settings';
    }

    public function getCategory(): string
    {
        return 'mail';
    }

    public function isEnabled(): bool
    {
        return settings('mail.mailer') === 'mailgun';
    }

    public function getSchema(): Schema
    {
        return Schema::make()
            ->components([
                Section::make(fn () => $this->sectionWithIndicator('Mailgun Configuration', $this->isEnabled()))
                    ->description('Configure Mailgun API settings for reliable email delivery')
                    ->schema([
                        TextInput::make('mailgun__domain')
                            ->label('Mailgun Domain')
                            ->placeholder('mg.example.com')
                            ->helperText('Your Mailgun sending domain')
                            ->columnSpanFull(),
                        TextInput::make('mailgun__secret')
                            ->label('Mailgun API Key')
                            ->password()->revealable()
                            ->placeholder('key-••••••••')
                            ->helperText('Your Mailgun API key (encrypted)')
                            ->columnSpanFull(),
                        TextInput::make('mailgun__endpoint')
                            ->label('Mailgun Endpoint')
                            ->placeholder('api.mailgun.net')
                            ->default('api.mailgun.net')
                            ->helperText('Mailgun API endpoint (use api.eu.mailgun.net for EU region)')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public function getDefaults(): array
    {
        return [
            'domain' => config('services.mailgun.domain', ''),
            'secret' => config('services.mailgun.secret', ''),
            'endpoint' => config('services.mailgun.endpoint', 'api.mailgun.net'),
        ];
    }
}
