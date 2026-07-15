<?php

namespace App\Filament\Pages\Settings;

use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Page;

class SettingsNavigation
{
    public static function getItems(Page $page): array
    {
        return [
            static::getCoreSettingsGroup($page),
            static::getPaymentProviderGroup($page),
            static::getMailGroup($page),
            static::getSocialLoginGroup($page),
            static::getFulfillmentGroup($page),
        ];
    }

    protected static function getCoreSettingsGroup(Page $page): NavigationGroup
    {
        return NavigationGroup::make()
            ->label('General')
            ->icon('heroicon-o-cog-6-tooth')
            ->collapsed()
            ->items([
                NavigationItem::make('General')
                    ->url(GeneralSettings::getUrl())
                    ->isActiveWhen(fn () => $page instanceof GeneralSettings)
                    ->sort(1),

                // NavigationItem::make('Notifications')
                //     ->url(NotificationSettings::getUrl())
                //     ->isActiveWhen(fn () => $page instanceof NotificationSettings)
                //     ->sort(2),
            ]);
    }

    protected static function getPaymentProviderGroup(Page $page): NavigationGroup
    {
        return NavigationGroup::make()
            ->label('Payment Providers')
            ->icon('heroicon-o-credit-card')
            ->collapsed()
            ->items([
                NavigationItem::make('Stripe')
                    ->url(StripeSettings::getUrl())
                    ->isActiveWhen(fn () => $page instanceof StripeSettings)
                    ->badge(settings('stripe.enabled', false) ? '✓' : null)
                    ->sort(11),

                NavigationItem::make('PayPal')
                    ->url(PayPalSettings::getUrl())
                    ->isActiveWhen(fn () => $page instanceof PayPalSettings)
                    ->badge(settings('paypal.enabled', false) ? '✓' : null)
                    ->sort(12),
            ]);
    }

    protected static function getMailGroup(Page $page): NavigationGroup
    {
        $currentMailer = settings('mail.mailer', 'log');

        return NavigationGroup::make()
            ->label('Email')
            ->icon('heroicon-o-envelope')
            ->collapsed()
            ->items([
                NavigationItem::make('Mail')
                    ->url(MailSettings::getUrl())
                    ->isActiveWhen(fn () => $page instanceof MailSettings)
                    ->sort(19),

                NavigationItem::make('Notifications')
                    ->url(NotificationSettings::getUrl())
                    ->isActiveWhen(fn () => $page instanceof NotificationSettings)
                    ->sort(20),

                NavigationItem::make('SMTP')
                    ->url(SmtpSettings::getUrl())
                    ->isActiveWhen(fn () => $page instanceof SmtpSettings)
                    ->badge($currentMailer === 'smtp' ? '✓' : null)
                    ->sort(21),

                NavigationItem::make('Mailgun')
                    ->url(MailgunSettings::getUrl())
                    ->isActiveWhen(fn () => $page instanceof MailgunSettings)
                    ->badge($currentMailer === 'mailgun' ? '✓' : null)
                    ->sort(22),

                NavigationItem::make('Postmark')
                    ->url(PostmarkSettings::getUrl())
                    ->isActiveWhen(fn () => $page instanceof PostmarkSettings)
                    ->badge($currentMailer === 'postmark' ? '✓' : null)
                    ->sort(23),

                NavigationItem::make('Amazon SES')
                    ->url(AmazonSesSettings::getUrl())
                    ->isActiveWhen(fn () => $page instanceof AmazonSesSettings)
                    ->badge($currentMailer === 'ses' ? '✓' : null)
                    ->sort(24),

                NavigationItem::make('Resend')
                    ->url(ResendSettings::getUrl())
                    ->isActiveWhen(fn () => $page instanceof ResendSettings)
                    ->badge($currentMailer === 'resend' ? '✓' : null)
                    ->sort(25),

                NavigationItem::make('Sendmail')
                    ->url(SendmailSettings::getUrl())
                    ->isActiveWhen(fn () => $page instanceof SendmailSettings)
                    ->badge($currentMailer === 'sendmail' ? '✓' : null)
                    ->sort(26),
            ]);
    }

    protected static function getSocialLoginGroup(Page $page): NavigationGroup
    {
        return NavigationGroup::make()
            ->label('Social Login')
            ->icon('heroicon-o-lock-closed')
            ->collapsed()
            ->items([
                NavigationItem::make('Google')
                    ->url(GoogleSettings::getUrl())
                    ->isActiveWhen(fn () => $page instanceof GoogleSettings)
                    ->badge(settings('google.enabled', false) ? '✓' : null)
                    ->sort(31),
                NavigationItem::make('Facebook')
                    ->url(FacebookSettings::getUrl())
                    ->isActiveWhen(fn () => $page instanceof FacebookSettings)
                    ->badge(settings('facebook.enabled', false) ? '✓' : null)
                    ->sort(32),
                NavigationItem::make('GitHub')
                    ->url(GitHubSettings::getUrl())
                    ->isActiveWhen(fn () => $page instanceof GitHubSettings)
                    ->badge(settings('github.enabled', false) ? '✓' : null)
                    ->sort(33),
            ]);
    }

    protected static function getFulfillmentGroup(Page $page): NavigationGroup
    {
        return NavigationGroup::make()
            ->label('Fulfillment')
            ->icon('heroicon-o-cube')
            ->collapsed()
            ->items([
                NavigationItem::make('File Downloader')
                    ->url(FileDownloaderSettings::getUrl())
                    ->isActiveWhen(fn () => $page instanceof FileDownloaderSettings)
                    ->badge(settings('downloader.enabled', true) ? '✓' : null)
                    ->sort(41),
                NavigationItem::make('License Generator')
                    ->url(LicenseGeneratorSettings::getUrl())
                    ->isActiveWhen(fn () => $page instanceof LicenseGeneratorSettings)
                    ->badge(settings('license.enabled', true) ? '✓' : null)
                    ->sort(42),
            ]);
    }
}
