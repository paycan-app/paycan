<?php

// namespace App\Services\Settings\Providers;

// use App\Contracts\SettingProvider;
// use App\Services\Settings\Concerns\HasStatusIndicator;
// use Filament\Forms\Components\TextInput;
// use Filament\Forms\Components\Toggle;
// use Filament\Schemas\Components\Section;
// use Filament\Schemas\Components\Utilities\Get;
// use Filament\Schemas\Schema;

// class SocialLoginSettingsProvider implements SettingProvider
// {
//     use HasStatusIndicator;

//     public function getGroup(): string
//     {
//         return 'social';
//     }

//     public function getLabel(): string
//     {
//         return 'Social Login';
//     }

//     public function getCategory(): string
//     {
//         return 'auth';
//     }

//     public function isEnabled(): bool
//     {
//         return true;
//     }

//     public function getSchema(): Schema
//     {
//         return Schema::make()
//             ->components([
//                 Section::make(fn (Get $get) => $this->sectionWithIndicator('Google OAuth', (bool) $get('social_google_enabled'))
//                 )
//                     ->description('Configure Google social login')
//                     ->schema([
//                         Toggle::make('social_google_enabled')
//                             ->label('Enable Google Login')
//                             ->helperText('Allow users to login with Google')
//                             ->default(false),

//                         TextInput::make('social_google_client_id')
//                             ->label('Client ID')
//                             ->placeholder('xxx.apps.googleusercontent.com')
//                             ->helperText('Your Google OAuth Client ID')
//                             ->columnSpanFull(),

//                         TextInput::make('social_google_client_secret')
//                             ->label('Client Secret')
//                             ->password()->revealable()
//                             ->placeholder('GOCSPX-xxx')
//                             ->helperText('Your Google OAuth Client Secret (encrypted)')
//                             ->columnSpanFull(),

//                         TextInput::make('social_google_redirect')
//                             ->label('Redirect URI')
//                             ->default('/auth/google/callback')
//                             ->helperText('OAuth callback URL')
//                             ->columnSpanFull(),
//                     ])
//                     ->columns(2)
//                     ->collapsed()
//                     ->collapsible(),

//                 Section::make(fn (Get $get) => $this->sectionWithIndicator('Facebook OAuth', (bool) $get('social_facebook_enabled'))
//                 )
//                     ->description('Configure Facebook social login')
//                     ->schema([
//                         Toggle::make('social_facebook_enabled')
//                             ->label('Enable Facebook Login')
//                             ->helperText('Allow users to login with Facebook')
//                             ->default(false),

//                         TextInput::make('social_facebook_client_id')
//                             ->label('App ID')
//                             ->placeholder('123456789')
//                             ->helperText('Your Facebook App ID')
//                             ->columnSpanFull(),

//                         TextInput::make('social_facebook_client_secret')
//                             ->label('App Secret')
//                             ->password()
//                             ->placeholder('xxx')
//                             ->helperText('Your Facebook App Secret (encrypted)')
//                             ->columnSpanFull(),

//                         TextInput::make('social_facebook_redirect')
//                             ->label('Redirect URI')
//                             ->default('/auth/facebook/callback')
//                             ->helperText('OAuth callback URL')
//                             ->columnSpanFull(),
//                     ])
//                     ->columns(2)
//                     ->collapsed()
//                     ->collapsible(),

//                 Section::make(fn (Get $get) => $this->sectionWithIndicator('GitHub OAuth', (bool) $get('social_github_enabled'))
//                 )
//                     ->description('Configure GitHub social login')
//                     ->schema([
//                         Toggle::make('social_github_enabled')
//                             ->label('Enable GitHub Login')
//                             ->helperText('Allow users to login with GitHub')
//                             ->default(false),

//                         TextInput::make('social_github_client_id')
//                             ->label('Client ID')
//                             ->placeholder('Iv1.xxx')
//                             ->helperText('Your GitHub OAuth Client ID')
//                             ->columnSpanFull(),

//                         TextInput::make('social_github_client_secret')
//                             ->label('Client Secret')
//                             ->password()
//                             ->placeholder('xxx')
//                             ->helperText('Your GitHub OAuth Client Secret (encrypted)')
//                             ->columnSpanFull(),

//                         TextInput::make('social_github_redirect')
//                             ->label('Redirect URI')
//                             ->default('/auth/github/callback')
//                             ->helperText('OAuth callback URL')
//                             ->columnSpanFull(),
//                     ])
//                     ->columns(2)
//                     ->collapsed()
//                     ->collapsible(),
//             ]);
//     }

//     public function getDefaults(): array
//     {
//         return [
//             'google_enabled' => false,
//             'google_client_id' => config('services.google.client_id', ''),
//             'google_client_secret' => config('services.google.client_secret', ''),
//             'google_redirect' => config('services.google.redirect', '/auth/google/callback'),

//             'facebook_enabled' => false,
//             'facebook_client_id' => config('services.facebook.client_id', ''),
//             'facebook_client_secret' => config('services.facebook.client_secret', ''),
//             'facebook_redirect' => config('services.facebook.redirect', '/auth/facebook/callback'),

//             'github_enabled' => false,
//             'github_client_id' => config('services.github.client_id', ''),
//             'github_client_secret' => config('services.github.client_secret', ''),
//             'github_redirect' => config('services.github.redirect', '/auth/github/callback'),
//         ];
//     }
// }
