<?php

namespace App\Services\Notifications;

use App\Services\Settings\SettingsManager;

class NotificationTemplateService
{
    public function __construct(
        protected SettingsManager $settings
    ) {}

    public function getTemplate(string $notificationType): array
    {
        // Prefer email_templates group for backward compatibility
        $subject = $this->settings->get("email_templates.{$notificationType}_subject");
        $bodyHtml = $this->settings->get("email_templates.{$notificationType}_body_html");
        $body = $this->settings->get("email_templates.{$notificationType}_body");

        // Fallback to notifications group if email_templates are not present
        if ($subject === null) {
            $subject = $this->settings->get("notifications.{$notificationType}_subject");
        }
        if ($body === null && empty($bodyHtml)) {
            $body = $this->settings->get("notifications.{$notificationType}_body");
        }

        // Ensure strings, not nulls
        $subject = $subject ?? '';
        $body = ($bodyHtml ?: $body) ?? '';

        return [
            'subject' => $subject,
            'body' => $body,
            'is_html' => ! empty($bodyHtml),
        ];
    }

    /**
     * Check if notification type is enabled
     */
    public function isEnabled(string $notificationType): bool
    {
        return (bool) $this->settings->get("notifications.{$notificationType}", true);
    }

    /**
     * Render template with variables
     */
    public function render(string $template, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $template = str_replace("{{{$key}}}", $value, $template);
        }

        return $template;
    }

    /**
     * Get mail configuration
     */
    public function getMailConfig(): array
    {
        return [
            // Align key names with MailSettingsProvider defaults and SettingsManager storage
            'mailer' => $this->settings->get('mail.mailer', config('mail.default')),
            'from' => [
                'address' => $this->settings->get('mail.from_address', config('mail.from.address')),
                'name' => $this->settings->get('mail.from_name', config('mail.from.name')),
            ],
        ];
    }
}
