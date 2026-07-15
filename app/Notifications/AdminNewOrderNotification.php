<?php

namespace App\Notifications;

use App\Models\Order;
use App\Services\Notifications\NotificationTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewOrderNotification extends Notification implements ShouldQueue
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
        // Check if admin notifications are enabled
        if (! settings('notifications.notify_admin_new_order', false)) {
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

        // Build order items list from product relationship
        $productTitle = optional($this->order->product)->title ?? 'Product';
        $unitPrice = optional($this->order->productPrice)->amount ?? $this->order->total;
        $quantity = $this->order->quantity ?? 1;
        $items = "- {$productTitle} (x{$quantity}): {$this->order->currency} ".number_format($unitPrice, 2);

        $customer = $this->order->user;
        $subject = "New Order Received - Order #{$this->order->order_number}";
        $adminPanelUrl = config('app.url').'/admin/orders/'.$this->order->id;

        $message = (new MailMessage)
            ->from($mailConfig['from']['address'], $mailConfig['from']['name'])
            ->subject($subject)
            ->greeting('New Order Notification')
            ->line("A new order has been placed by {$customer->name}.")
            ->line('')
            ->line("**Order Number:** {$this->order->order_number}")
            ->line("**Customer:** {$customer->name} ({$customer->email})")
            ->line("**Total:** {$this->order->currency} ".number_format($this->order->total, 2))
            ->line("**Order Date:** {$this->order->created_at->format('F d, Y H:i:s')}")
            ->line('')
            ->line('**Order Items:**')
            ->line($items)
            ->action('View Order in Admin Panel', $adminPanelUrl)
            ->line('You can view and manage this order in your admin panel.');

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
        ];
    }
}
