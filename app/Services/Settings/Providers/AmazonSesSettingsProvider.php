<?php

namespace App\Services\Settings\Providers;

use App\Contracts\SettingProvider;
use App\Services\Settings\Concerns\HasStatusIndicator;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AmazonSesSettingsProvider implements SettingProvider
{
    use HasStatusIndicator;

    public function getGroup(): string
    {
        return 'ses';
    }

    public function getLabel(): string
    {
        return 'Amazon SES Settings';
    }

    public function getCategory(): string
    {
        return 'mail';
    }

    public function isEnabled(): bool
    {
        return settings('mail.mailer') === 'ses';
    }

    public function getSchema(): Schema
    {
        return Schema::make()
            ->components([
                Section::make(fn () => $this->sectionWithIndicator('Amazon SES Configuration', $this->isEnabled()))
                    ->description('Configure Amazon Simple Email Service settings')
                    ->schema([
                        TextInput::make('ses__key')
                            ->label('AWS Access Key ID')
                            ->placeholder('AKIAIOSFODNN7EXAMPLE')
                            ->helperText('Your AWS access key ID')
                            ->columnSpanFull(),
                        TextInput::make('ses__secret')
                            ->label('AWS Secret Access Key')
                            ->password()->revealable()
                            ->placeholder('wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY')
                            ->helperText('Your AWS secret access key (encrypted)')
                            ->columnSpanFull(),
                        TextInput::make('ses__region')
                            ->label('AWS Region')
                            ->placeholder('us-east-1')
                            ->default('us-east-1')
                            ->helperText('AWS region where your SES is configured'),
                        TextInput::make('ses__configuration_set')
                            ->label('Configuration Set')
                            ->placeholder('my-configuration-set')
                            ->helperText('Optional SES configuration set name'),
                    ])
                    ->columns(2),
            ]);
    }

    public function getDefaults(): array
    {
        return [
            'key' => config('services.ses.key', ''),
            'secret' => config('services.ses.secret', ''),
            'region' => config('services.ses.region', 'us-east-1'),
            'configuration_set' => config('services.ses.configuration_set', ''),
        ];
    }
}
