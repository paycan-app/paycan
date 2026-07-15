<?php

namespace App\Services\Settings\Providers;

use App\Contracts\SettingProvider;
use App\Services\Settings\Concerns\HasStatusIndicator;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SendmailSettingsProvider implements SettingProvider
{
    use HasStatusIndicator;

    public function getGroup(): string
    {
        return 'sendmail';
    }

    public function getLabel(): string
    {
        return 'Sendmail Settings';
    }

    public function getCategory(): string
    {
        return 'mail';
    }

    public function isEnabled(): bool
    {
        return settings('mail.mailer') === 'sendmail';
    }

    public function getSchema(): Schema
    {
        return Schema::make()
            ->components([
                Section::make(fn () => $this->sectionWithIndicator('Sendmail Configuration', $this->isEnabled()))
                    ->description('Configure Sendmail binary path for local email delivery')
                    ->schema([
                        TextInput::make('sendmail__path')
                            ->label('Sendmail Path')
                            ->placeholder('/usr/sbin/sendmail -bs -i')
                            ->default('/usr/sbin/sendmail -bs -i')
                            ->helperText('Path to sendmail binary with arguments')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public function getDefaults(): array
    {
        return [
            'path' => config('mail.mailers.sendmail.path', '/usr/sbin/sendmail -bs -i'),
        ];
    }
}
