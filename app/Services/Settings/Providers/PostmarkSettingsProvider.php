<?php

namespace App\Services\Settings\Providers;

use App\Contracts\SettingProvider;
use App\Services\Settings\Concerns\HasStatusIndicator;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostmarkSettingsProvider implements SettingProvider
{
    use HasStatusIndicator;

    public function getGroup(): string
    {
        return 'postmark';
    }

    public function getLabel(): string
    {
        return 'Postmark Settings';
    }

    public function getCategory(): string
    {
        return 'mail';
    }

    public function isEnabled(): bool
    {
        return settings('mail.mailer') === 'postmark';
    }

    public function getSchema(): Schema
    {
        return Schema::make()
            ->components([
                Section::make(fn () => $this->sectionWithIndicator('Postmark Configuration', $this->isEnabled()))
                    ->description('Configure Postmark email service settings')
                    ->schema([
                        TextInput::make('postmark__token')
                            ->label('Server API Token')
                            ->password()->revealable()
                            ->placeholder('xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx')
                            ->helperText('Your Postmark server API token (encrypted)')
                            ->columnSpanFull(),
                        TextInput::make('postmark__message_stream_id')
                            ->label('Message Stream ID')
                            ->placeholder('outbound')
                            ->default('outbound')
                            ->helperText('Postmark message stream ID for organizing your email')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public function getDefaults(): array
    {
        return [
            'token' => config('services.postmark.token', ''),
            'message_stream_id' => config('services.postmark.message_stream_id', 'outbound'),
        ];
    }
}
