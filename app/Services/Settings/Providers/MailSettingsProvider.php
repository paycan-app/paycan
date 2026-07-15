<?php

namespace App\Services\Settings\Providers;

use App\Contracts\SettingProvider;
use App\Services\Settings\Concerns\HasStatusIndicator;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class MailSettingsProvider implements SettingProvider
{
    use HasStatusIndicator;

    public function getGroup(): string
    {
        return 'mail';
    }

    public function getLabel(): string
    {
        return 'Email Settings';
    }

    public function getCategory(): string
    {
        return 'mail';
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function getSchema(): \Filament\Schemas\Schema
    {
        return \Filament\Schemas\Schema::make()
            ->components([
                Section::make('Admin Notifications')
                    ->description('Configure admin notification settings')
                    ->schema([

                        Select::make('mail__mailer')
                            ->label('Mail Driver')
                            ->options([
                                'smtp' => 'SMTP',
                                'sendmail' => 'Sendmail',
                                'mailgun' => 'Mailgun',
                                'ses' => 'Amazon SES',
                                'postmark' => 'Postmark',
                                'resend' => 'Resend',
                                'log' => 'Log (Testing)',
                            ])
                            ->default('log')
                            ->required()
                            ->helperText('Controls which mailer sends admin notifications.')
                            ->columnSpanFull(),


                        TextInput::make('mail__from_address')
                            ->label('From Email Address')
                            ->email()
                            ->required()
                            ->placeholder('noreply@example.com')
                            ->helperText('Email address that emails will be sent from')
                            ->columnSpanFull(),

                        TextInput::make('mail__from_name')
                            ->label('From Name')
                            ->required()
                            ->placeholder('My Application')
                            ->helperText('Name that will appear as the sender')
                            ->columnSpanFull(),

                    ])
                    ->columns(2)
                    ->collapsible(),

                
            ]);
    }

    public function getDefaults(): array
    {
        return [
            'admin_email' => config('mail.from.address'),
            'mailer' => config('mail.mailer', 'log'),
            'from_address' => config('mail.from.address', 'noreply@paycan'),
            'from_name' => config('mail.from.name', 'Paycan'),
        ];
    }

}
