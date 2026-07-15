<?php

namespace App\Services\Settings\Providers;

use App\Contracts\SettingProvider;
use App\Services\Settings\Concerns\HasStatusIndicator;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SmtpSettingsProvider implements SettingProvider
{
    use HasStatusIndicator;

    public function getGroup(): string
    {
        return 'smtp';
    }

    public function getLabel(): string
    {
        return 'SMTP Settings';
    }

    public function getCategory(): string
    {
        return 'mail';
    }

    public function isEnabled(): bool
    {
        return settings('mail.mailer') === 'smtp';
    }

    public function getSchema(): Schema
    {
        return Schema::make()
            ->components([
                Section::make(fn () => $this->sectionWithIndicator('SMTP Configuration', $this->isEnabled()))
                    ->description('Configure SMTP server settings for direct email delivery')
                    ->schema([
                        TextInput::make('smtp__host')
                            ->label('SMTP Host')
                            ->placeholder('smtp.gmail.com')
                            ->helperText('Your SMTP server hostname')
                            ->columnSpanFull(),
                        TextInput::make('smtp__port')
                            ->label('SMTP Port')
                            ->numeric()
                            ->placeholder('587')
                            ->helperText('SMTP server port (usually 587 for TLS or 465 for SSL)'),
                        Select::make('smtp__encryption')
                            ->label('Encryption')
                            ->options([
                                'tls' => 'TLS',
                                'ssl' => 'SSL',
                                '' => 'None',
                            ])
                            ->default('tls')
                            ->helperText('Encryption method'),
                        TextInput::make('smtp__username')
                            ->label('SMTP Username')
                            ->placeholder('your-email@gmail.com')
                            ->helperText('SMTP authentication username')
                            ->columnSpanFull(),
                        TextInput::make('smtp__password')
                            ->label('SMTP Password')
                            ->password()->revealable()
                            ->placeholder('••••••••')
                            ->helperText('SMTP authentication password (encrypted)')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public function getDefaults(): array
    {
        return [
            'host' => config('mail.mailers.smtp.host', 'smtp.gmail.com'),
            'port' => config('mail.mailers.smtp.port', 587),
            'encryption' => config('mail.mailers.smtp.encryption', 'tls'),
            'username' => config('mail.mailers.smtp.username', ''),
            'password' => config('mail.mailers.smtp.password', ''),
        ];
    }
}
