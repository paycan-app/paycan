<?php

namespace App\Services\Settings\Providers;

use App\Contracts\SettingProvider;
use App\Services\Settings\Concerns\HasStatusIndicator;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ResendSettingsProvider implements SettingProvider
{
    use HasStatusIndicator;

    public function getGroup(): string
    {
        return 'resend';
    }

    public function getLabel(): string
    {
        return 'Resend Settings';
    }

    public function getCategory(): string
    {
        return 'mail';
    }

    public function isEnabled(): bool
    {
        return settings('mail.mailer') === 'resend';
    }

    public function getSchema(): Schema
    {
        return Schema::make()
            ->components([
                Section::make(fn () => $this->sectionWithIndicator('Resend Configuration', $this->isEnabled()))
                    ->description('Configure Resend email API settings')
                    ->schema([
                        TextInput::make('resend__key')
                            ->label('API Key')
                            ->password()->revealable()
                            ->placeholder('re_••••••••')
                            ->helperText('Your Resend API key (encrypted)')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public function getDefaults(): array
    {
        return [
            'key' => config('services.resend.key', ''),
        ];
    }
}
