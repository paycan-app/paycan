<?php

namespace App\Services\Settings;

use Illuminate\Support\Facades\Config;

class DynamicConfigService
{
    public function __construct(
        protected SettingsManager $settings
    ) {}

    /**
     * Configure all application settings from database
     */
    public function configure(): void
    {
        $this->configureApp();
        $this->configurePaymentGateways();
        $this->configureSocialLogin();
    }

    /**
     * Configure application settings from database
     */
    protected function configureApp(): void
    {
        if ($appName = $this->settings->get('app.name')) {
            Config::set('app.name', $appName);
        }

        if ($appUrl = $this->settings->get('app.url')) {
            Config::set('app.url', $appUrl);
        }

        if ($appTimezone = $this->settings->get('app.timezone')) {
            Config::set('app.timezone', $appTimezone);
        }

        if ($appLocale = $this->settings->get('app.locale')) {
            Config::set('app.locale', $appLocale);
        }
    }

    /**
     * Configure payment gateway settings from database
     */
    protected function configurePaymentGateways(): void
    {
        // Configure Stripe
        if ($this->settings->get('stripe.enabled')) {
            Config::set('services.stripe.key', $this->settings->get('stripe.publishable_key'));
            Config::set('services.stripe.secret', $this->settings->get('stripe.api_key'));
            Config::set('services.stripe.webhook_secret', $this->settings->get('stripe.webhook_secret'));
        }

        // Configure PayPal
        if ($this->settings->get('paypal.enabled')) {
            Config::set('services.paypal.client_id', $this->settings->get('paypal.client_id'));
            Config::set('services.paypal.client_secret', $this->settings->get('paypal.client_secret'));
            Config::set('services.paypal.mode', $this->settings->get('paypal.mode', 'sandbox'));
            Config::set('services.paypal.webhook_id', $this->settings->get('paypal.webhook_id'));
        }
    }

    /**
     * Configure social login settings from database
     */
    protected function configureSocialLogin(): void
    {
        // Configure Google OAuth
        if ($googleClientId = $this->settings->get('google.client_id')) {
            Config::set('services.google.client_id', $googleClientId);
            Config::set('services.google.client_secret', $this->settings->get('google.client_secret'));
            Config::set('services.google.redirect', $this->settings->get('google.redirect', '/auth/google/callback'));
        }

        // Configure Facebook OAuth
        if ($facebookClientId = $this->settings->get('facebook.client_id')) {
            Config::set('services.facebook.client_id', $facebookClientId);
            Config::set('services.facebook.client_secret', $this->settings->get('facebook.client_secret'));
            Config::set('services.facebook.redirect', $this->settings->get('facebook.redirect', '/auth/facebook/callback'));
        }

        // Configure GitHub OAuth
        if ($githubClientId = $this->settings->get('github.client_id')) {
            Config::set('services.github.client_id', $githubClientId);
            Config::set('services.github.client_secret', $this->settings->get('github.client_secret'));
            Config::set('services.github.redirect', $this->settings->get('github.redirect', '/auth/github/callback'));
        }
    }
}
