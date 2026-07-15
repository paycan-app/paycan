<?php

namespace App\Notifications;

use App\Models\Fulfillment;
use App\Models\Order;
use App\Services\Notifications\NotificationTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderFulfilledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Order $order,
        protected Fulfillment $fulfillment
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $templateService = app(NotificationTemplateService::class);

        if (! $templateService->isEnabled('order_fulfilled')) {
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
        $template = $templateService->getTemplate('order_fulfilled');
        $mailConfig = $templateService->getMailConfig();

        $variables = [
            'customer_name' => $notifiable->name,
            'order_number' => $this->order->order_number,
            'tracking_number' => $this->fulfillment->meta['tracking_number'] ?? 'N/A',
            'carrier' => $this->fulfillment->meta['carrier'] ?? 'N/A',
            'estimated_delivery' => $this->fulfillment->meta['estimated_delivery'] ?? 'N/A',
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
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'fulfillment_id' => $this->fulfillment->id,
        ];
    }
}
