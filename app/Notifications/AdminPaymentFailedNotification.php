<?php

namespace App\Notifications;

use App\Models\Order;
use App\Services\Notifications\NotificationTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminPaymentFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Order $order
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Check if admin failed payment notifications are enabled
        if (! settings('notifications.notify_admin_failed_payment', false)) {
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
        $mailConfig = $templateService->getMailConfig();

        $customer = $this->order->user;
        $subject = "Payment Failed - Order #{$this->order->order_number}";
        $adminPanelUrl = config('app.url').'/admin/orders/'.$this->order->id;
        $failureReason = $this->order->meta['failure_reason'] ?? 'Unknown reason';

        $message = (new MailMessage)
            ->from($mailConfig['from']['address'], $mailConfig['from']['name'])
            ->subject($subject)
            ->greeting('Payment Failure Alert')
            ->line("A payment has failed for order #{$this->order->order_number}.")
            ->line('')
            ->line("**Order Number:** {$this->order->order_number}")
            ->line("**Customer:** {$customer->name} ({$customer->email})")
            ->line('**Total:** $'.number_format($this->order->total / 100, 2))
            ->line("**Failure Reason:** {$failureReason}")
            ->line('**Failed At:** '.($this->order->meta['payment_failed_at'] ?? 'Unknown'))
            ->action('View Order in Admin Panel', $adminPanelUrl)
            ->line('You may need to contact the customer or investigate further.');

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
            'customer_name' => $this->order->user->name,
            'total' => $this->order->total,
            'failure_reason' => $this->order->meta['failure_reason'] ?? 'Unknown',
        ];
    }
}
