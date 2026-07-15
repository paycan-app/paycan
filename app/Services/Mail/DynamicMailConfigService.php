<?php

namespace App\Services\Mail;

use App\Services\Settings\SettingsManager;
use Illuminate\Support\Facades\Config;

class DynamicMailConfigService
{
    public function __construct(
        protected SettingsManager $settings
    ) {}

    /**
     * Configure mail settings from database
     */
    public function configure(): void
    {
        // Get mail configuration from settings
        $mailer = $this->settings->get('mail.mailer');

        if (! $mailer) {
            return;
        }

        // Set default mailer
        Config::set('mail.default', $mailer);

        // Configure from address
        Config::set('mail.from.address', $this->settings->get('mail.from_address', config('mail.from.address')));
        Config::set('mail.from.name', $this->settings->get('mail.from_name', config('mail.from.name')));

        // Configure SMTP (from smtp.*)
        if ($mailer === 'smtp') {
            Config::set('mail.mailers.smtp.host', $this->settings->get('smtp.host'));
            Config::set('mail.mailers.smtp.port', $this->settings->get('smtp.port'));
            // Use 'scheme' per current mail.php config
            Config::set('mail.mailers.smtp.scheme', $this->settings->get('smtp.encryption'));
            Config::set('mail.mailers.smtp.username', $this->settings->get('smtp.username'));
            Config::set('mail.mailers.smtp.password', $this->settings->get('smtp.password'));
        }

        // Configure Mailgun (from mailgun.*)
        if ($mailer === 'mailgun') {
            Config::set('services.mailgun.domain', $this->settings->get('mailgun.domain'));
            Config::set('services.mailgun.secret', $this->settings->get('mailgun.secret'));
            Config::set('services.mailgun.endpoint', $this->settings->get('mailgun.endpoint', 'api.mailgun.net'));
        }

        // Configure Amazon SES (from ses.*)
        if ($mailer === 'ses') {
            Config::set('services.ses.key', $this->settings->get('ses.key'));
            Config::set('services.ses.secret', $this->settings->get('ses.secret'));
            Config::set('services.ses.region', $this->settings->get('ses.region', 'us-east-1'));
            Config::set('services.ses.configuration_set', $this->settings->get('ses.configuration_set'));
        }

        // Configure Postmark (from postmark.*)
        if ($mailer === 'postmark') {
            Config::set('services.postmark.token', $this->settings->get('postmark.token'));
            Config::set('services.postmark.message_stream_id', $this->settings->get('postmark.message_stream_id', 'outbound'));
        }

        // Configure Resend (from resend.*)
        if ($mailer === 'resend') {
            Config::set('services.resend.key', $this->settings->get('resend.key'));
        }

        // Configure Sendmail (from sendmail.*)
        if ($mailer === 'sendmail') {
            Config::set('mail.mailers.sendmail.path', $this->settings->get('sendmail.path', '/usr/sbin/sendmail -bs -i'));
        }
    }
}
