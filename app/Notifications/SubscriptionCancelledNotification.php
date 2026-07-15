<?php

namespace App\Notifications;

use App\Models\Subscription;
use App\Services\Notifications\NotificationTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Subscription $subscription
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $templateService = app(NotificationTemplateService::class);

        if (! $templateService->isEnabled('subscription_cancelled')) {
            return [];
        }

        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $templateService = app(NotificationTemplateService::class);
        $template = $templateService->getTemplate('subscription_cancelled');
        $mailConfig = $templateService->getMailConfig();

        $variables = [
            'customer_name' => $notifiable->name,
            'plan_name' => $this->subscription->productPrice->product->name,
            'cancellation_date' => $this->subscription->updated_at->format('F d, Y'),
            'access_until' => $this->subscription->ends_at?->format('F d, Y') ?? 'N/A',
        ];

        $subject = $templateService->render($template['subject'], $variables);
        $body = $templateService->render($template['body'], $variables);

        $message = (new MailMessage)
            ->from($mailConfig['from']['address'], $mailConfig['from']['name'])
            ->subject($subject)
            ->markdown('emails.custom-markdown', ['content' => $body]);

        if ($mailConfig['mailer']) {
            $message->mailer($mailConfig['mailer']);
        }

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subscription_id' => $this->subscription->id,
            'plan_name' => $this->subscription->productPrice->product->name,
        ];
    }
}
