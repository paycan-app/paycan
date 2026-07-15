<?php

namespace App\Services\Settings\Providers;

use App\Contracts\SettingProvider;
use App\Services\Settings\Concerns\HasStatusIndicator;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\HtmlString;

class NotificationSettingsProvider implements SettingProvider
{
    use HasStatusIndicator;

    public function getGroup(): string
    {
        return 'notifications';
    }

    public function getLabel(): string
    {
        return 'Notifications & Email Templates';
    }

    public function getCategory(): string
    {
        return 'notifications';
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function getSchema(): \Filament\Schemas\Schema
    {
        return \Filament\Schemas\Schema::make()
            ->components([
                Section::make('Admin Notifications')
                    ->description('Configure admin notification settings')
                    ->schema([
                        TextInput::make('notifications__admin_email')
                            ->label('Admin Email')
                            ->email()
                            ->helperText('Email address for admin notifications')
                            ->columnSpanFull(),

                        Toggle::make('notifications__notify_admin_new_order')
                            ->label('New Order Notifications')
                            ->helperText('Notify admin when new orders are placed')
                            ->default(false),

                        Toggle::make('notifications__notify_admin_failed_payment')
                            ->label('Failed Payment Notifications')
                            ->helperText('Notify admin when payments fail')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make(
                    fn (Get $get): HtmlString|string => $get('notifications__order_confirmation')
                        ? new HtmlString('<span style="display:inline-block;width:8px;height:8px;background-color:rgb(34,197,94);border-radius:50%;margin-right:8px;vertical-align:middle;"></span>Order Confirmation')
                        : 'Order Confirmation'
                )
                    ->description('Email sent when an order is placed')
                    ->schema([
                        Toggle::make('notifications__order_confirmation')
                            ->label('Enable Order Confirmation Emails')
                            ->helperText('Send email when order is placed')
                            ->default(true)
                            ->columnSpanFull(),

                        TextInput::make('notifications__order_confirmation_subject')
                            ->label('Email Subject')
                            ->default('Order Confirmation - Order {{order_number}}')
                            ->helperText('Available variables: {{order_number}}, {{customer_name}}, {{total}}')
                            ->columnSpanFull(),

                        MarkdownEditor::make('notifications__order_confirmation_body')
                            ->label('Email Body')
                            ->default($this->getDefaultOrderConfirmationTemplate())
                            ->helperText('Available variables: {{order_number}}, {{customer_name}}, {{order_date}}, {{total}}, {{items}}, {{shipping_address}}')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsed()
                    ->collapsible(),

                Section::make(
                    fn (Get $get): HtmlString|string => $get('notifications__digital_order_fulfilled')
                        ? new HtmlString('<span style="display:inline-block;width:8px;height:8px;background-color:rgb(34,197,94);border-radius:50%;margin-right:8px;vertical-align:middle;"></span>Digital Order Fulfilled')
                        : 'Digital Order Fulfilled'
                )
                    ->description('Email sent when a digital product order is fulfilled')
                    ->schema([
                        Toggle::make('notifications__digital_order_fulfilled')
                            ->label('Enable Digital Order Fulfilled Emails')
                            ->helperText('Send email when digital product order is fulfilled')
                            ->default(true)
                            ->columnSpanFull(),

                        TextInput::make('notifications__digital_order_fulfilled_subject')
                            ->label('Email Subject')
                            ->default('Your Digital Product is Ready - Order {{order_number}}')
                            ->helperText('Available variables: {{order_number}}, {{customer_name}}, {{product_name}}')
                            ->columnSpanFull(),

                        MarkdownEditor::make('notifications__digital_order_fulfilled_body')
                            ->label('Email Body')
                            ->default($this->getDefaultDigitalOrderFulfilledTemplate())
                            ->helperText('Available variables: {{order_number}}, {{customer_name}}, {{product_name}}, {{license_key}}, {{download_url}}, {{download_link}}, {{expires_at}}, {{max_downloads}}')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsed()
                    ->collapsible(),

                Section::make(
                    fn (Get $get): HtmlString|string => $get('notifications__physical_order_fulfilled')
                        ? new HtmlString('<span style="display:inline-block;width:8px;height:8px;background-color:rgb(34,197,94);border-radius:50%;margin-right:8px;vertical-align:middle;"></span>Physical Order Fulfilled')
                        : 'Physical Order Fulfilled'
                )
                    ->description('Email sent when a physical product order is shipped')
                    ->schema([
                        Toggle::make('notifications__physical_order_fulfilled')
                            ->label('Enable Physical Order Fulfilled Emails')
                            ->helperText('Send email when physical product order is shipped')
                            ->default(true)
                            ->columnSpanFull(),

                        TextInput::make('notifications__physical_order_fulfilled_subject')
                            ->label('Email Subject')
                            ->default('Your Order Has Been Shipped - Order {{order_number}}')
                            ->helperText('Available variables: {{order_number}}, {{customer_name}}, {{product_name}}, {{tracking_number}}')
                            ->columnSpanFull(),

                        MarkdownEditor::make('notifications__physical_order_fulfilled_body')
                            ->label('Email Body')
                            ->default($this->getDefaultPhysicalOrderFulfilledTemplate())
                            ->helperText('Available variables: {{order_number}}, {{customer_name}}, {{product_name}}, {{tracking_number}}, {{carrier}}, {{estimated_delivery}}, {{shipping_address}}')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsed()
                    ->collapsible(),

                Section::make(
                    fn (Get $get): HtmlString|string => $get('notifications__service_order_fulfilled')
                        ? new HtmlString('<span style="display:inline-block;width:8px;height:8px;background-color:rgb(34,197,94);border-radius:50%;margin-right:8px;vertical-align:middle;"></span>Service Order Fulfilled')
                        : 'Service Order Fulfilled'
                )
                    ->description('Email sent when a service order is fulfilled')
                    ->schema([
                        Toggle::make('notifications__service_order_fulfilled')
                            ->label('Enable Service Order Fulfilled Emails')
                            ->helperText('Send email when service order is fulfilled')
                            ->default(true)
                            ->columnSpanFull(),

                        TextInput::make('notifications__service_order_fulfilled_subject')
                            ->label('Email Subject')
                            ->default('Your Service is Ready - Order {{order_number}}')
                            ->helperText('Available variables: {{order_number}}, {{customer_name}}, {{product_name}}')
                            ->columnSpanFull(),

                        MarkdownEditor::make('notifications__service_order_fulfilled_body')
                            ->label('Email Body')
                            ->default($this->getDefaultServiceOrderFulfilledTemplate())
                            ->helperText('Available variables: {{order_number}}, {{customer_name}}, {{product_name}}, {{service_code}}, {{instructions}}, {{valid_until}}')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsed()
                    ->collapsible(),

                Section::make(
                    fn (Get $get): HtmlString|string => $get('notifications__order_fulfilled')
                        ? new HtmlString('<span style="display:inline-block;width:8px;height:8px;background-color:rgb(34,197,94);border-radius:50%;margin-right:8px;vertical-align:middle;"></span>Order Fulfilled (Legacy)')
                        : 'Order Fulfilled (Legacy)'
                )
                    ->description('Legacy email sent when an order is fulfilled (deprecated - use product-type-specific templates above)')
                    ->schema([
                        Toggle::make('notifications__order_fulfilled')
                            ->label('Enable Order Fulfilled Emails')
                            ->helperText('Send email when order is fulfilled (fallback for subscription products)')
                            ->default(true)
                            ->columnSpanFull(),

                        TextInput::make('notifications__order_fulfilled_subject')
                            ->label('Email Subject')
                            ->default('Your Order Has Been Shipped - Order {{order_number}}')
                            ->helperText('Available variables: {{order_number}}, {{customer_name}}, {{tracking_number}}')
                            ->columnSpanFull(),

                        MarkdownEditor::make('notifications__order_fulfilled_body')
                            ->label('Email Body')
                            ->default($this->getDefaultOrderFulfilledTemplate())
                            ->helperText('Available variables: {{order_number}}, {{customer_name}}, {{tracking_number}}, {{carrier}}, {{estimated_delivery}}')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsed()
                    ->collapsible(),

                Section::make(
                    fn (Get $get): HtmlString|string => $get('notifications__subscription_created')
                        ? new HtmlString('<span style="display:inline-block;width:8px;height:8px;background-color:rgb(34,197,94);border-radius:50%;margin-right:8px;vertical-align:middle;"></span>Subscription Created')
                        : 'Subscription Created'
                )
                    ->description('Email sent when a subscription starts')
                    ->schema([
                        Toggle::make('notifications__subscription_created')
                            ->label('Enable Subscription Created Emails')
                            ->helperText('Send email when subscription starts')
                            ->default(true)
                            ->columnSpanFull(),

                        TextInput::make('notifications__subscription_created_subject')
                            ->label('Email Subject')
                            ->default('Welcome to {{plan_name}} - Subscription Confirmed')
                            ->helperText('Available variables: {{plan_name}}, {{customer_name}}, {{amount}}')
                            ->columnSpanFull(),

                        MarkdownEditor::make('notifications__subscription_created_body')
                            ->label('Email Body')
                            ->default($this->getDefaultSubscriptionCreatedTemplate())
                            ->helperText('Available variables: {{plan_name}}, {{customer_name}}, {{amount}}, {{billing_cycle}}, {{next_billing_date}}')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsed()
                    ->collapsible(),

                Section::make(
                    fn (Get $get): HtmlString|string => $get('notifications__subscription_renewed')
                        ? new HtmlString('<span style="display:inline-block;width:8px;height:8px;background-color:rgb(34,197,94);border-radius:50%;margin-right:8px;vertical-align:middle;"></span>Subscription Renewed')
                        : 'Subscription Renewed'
                )
                    ->description('Email sent when a subscription renews')
                    ->schema([
                        Toggle::make('notifications__subscription_renewed')
                            ->label('Enable Subscription Renewed Emails')
                            ->helperText('Send email when subscription renews')
                            ->default(true)
                            ->columnSpanFull(),

                        TextInput::make('notifications__subscription_renewed_subject')
                            ->label('Email Subject')
                            ->default('Subscription Renewed - {{plan_name}}')
                            ->helperText('Available variables: {{plan_name}}, {{customer_name}}, {{amount}}')
                            ->columnSpanFull(),

                        MarkdownEditor::make('notifications__subscription_renewed_body')
                            ->label('Email Body')
                            ->default($this->getDefaultSubscriptionRenewedTemplate())
                            ->helperText('Available variables: {{plan_name}}, {{customer_name}}, {{amount}}, {{next_billing_date}}')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsed()
                    ->collapsible(),

                Section::make(
                    fn (Get $get): HtmlString|string => $get('notifications__subscription_cancelled')
                        ? new HtmlString('<span style="display:inline-block;width:8px;height:8px;background-color:rgb(34,197,94);border-radius:50%;margin-right:8px;vertical-align:middle;"></span>Subscription Cancelled')
                        : 'Subscription Cancelled'
                )
                    ->description('Email sent when a subscription is cancelled')
                    ->schema([
                        Toggle::make('notifications__subscription_cancelled')
                            ->label('Enable Subscription Cancelled Emails')
                            ->helperText('Send email when subscription is cancelled')
                            ->default(true)
                            ->columnSpanFull(),

                        TextInput::make('notifications__subscription_cancelled_subject')
                            ->label('Email Subject')
                            ->default('Subscription Cancelled - {{plan_name}}')
                            ->helperText('Available variables: {{plan_name}}, {{customer_name}}')
                            ->columnSpanFull(),

                        MarkdownEditor::make('notifications__subscription_cancelled_body')
                            ->label('Email Body')
                            ->default($this->getDefaultSubscriptionCancelledTemplate())
                            ->helperText('Available variables: {{plan_name}}, {{customer_name}}, {{cancellation_date}}, {{access_until}}')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsed()
                    ->collapsible(),

                Section::make(
                    fn (Get $get): HtmlString|string => $get('notifications__payment_failed')
                        ? new HtmlString('<span style="display:inline-block;width:8px;height:8px;background-color:rgb(34,197,94);border-radius:50%;margin-right:8px;vertical-align:middle;"></span>Payment Failed')
                        : 'Payment Failed'
                )
                    ->description('Email sent when a payment fails')
                    ->schema([
                        Toggle::make('notifications__payment_failed')
                            ->label('Enable Payment Failed Emails')
                            ->helperText('Send email when payment fails')
                            ->default(true)
                            ->columnSpanFull(),

                        TextInput::make('notifications__payment_failed_subject')
                            ->label('Email Subject')
                            ->default('Payment Failed - Action Required')
                            ->helperText('Available variables: {{customer_name}}, {{amount}}')
                            ->columnSpanFull(),

                        MarkdownEditor::make('notifications__payment_failed_body')
                            ->label('Email Body')
                            ->default($this->getDefaultPaymentFailedTemplate())
                            ->helperText('Available variables: {{customer_name}}, {{amount}}, {{plan_name}}, {{retry_date}}, {{update_payment_url}}')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsed()
                    ->collapsible(),
            ]);
    }

    public function getDefaults(): array
    {
        return [
            'admin_email' => config('mail.from.address'),
            'notify_admin_new_order' => false,
            'notify_admin_failed_payment' => true,

            'order_confirmation' => true,
            'order_confirmation_subject' => 'Order Confirmation - Order {{order_number}}',
            'order_confirmation_body' => $this->getDefaultOrderConfirmationTemplate(),

            'digital_order_fulfilled' => true,
            'digital_order_fulfilled_subject' => 'Your Digital Product is Ready - Order {{order_number}}',
            'digital_order_fulfilled_body' => $this->getDefaultDigitalOrderFulfilledTemplate(),

            'physical_order_fulfilled' => true,
            'physical_order_fulfilled_subject' => 'Your Order Has Been Shipped - Order {{order_number}}',
            'physical_order_fulfilled_body' => $this->getDefaultPhysicalOrderFulfilledTemplate(),

            'service_order_fulfilled' => true,
            'service_order_fulfilled_subject' => 'Your Service is Ready - Order {{order_number}}',
            'service_order_fulfilled_body' => $this->getDefaultServiceOrderFulfilledTemplate(),

            'order_fulfilled' => true,
            'order_fulfilled_subject' => 'Your Order Has Been Shipped - Order {{order_number}}',
            'order_fulfilled_body' => $this->getDefaultOrderFulfilledTemplate(),

            'subscription_created' => true,
            'subscription_created_subject' => 'Welcome to {{plan_name}} - Subscription Confirmed',
            'subscription_created_body' => $this->getDefaultSubscriptionCreatedTemplate(),

            'subscription_renewed' => true,
            'subscription_renewed_subject' => 'Subscription Renewed - {{plan_name}}',
            'subscription_renewed_body' => $this->getDefaultSubscriptionRenewedTemplate(),

            'subscription_cancelled' => true,
            'subscription_cancelled_subject' => 'Subscription Cancelled - {{plan_name}}',
            'subscription_cancelled_body' => $this->getDefaultSubscriptionCancelledTemplate(),

            'payment_failed' => true,
            'payment_failed_subject' => 'Payment Failed - Action Required',
            'payment_failed_body' => $this->getDefaultPaymentFailedTemplate(),
        ];
    }

    protected function getDefaultOrderConfirmationTemplate(): string
    {
        return "## Thank you for your order!\n\n"
            ."Hi {{customer_name}},\n\n"
            ."We've received your order and are getting it ready. Here are the details:\n\n"
            ."- Order Number: {{order_number}}\n"
            ."- Order Date: {{order_date}}\n"
            ."- Total: {{total}}\n\n"
            ."### Order Items\n"
            ."{{items}}\n\n"
            ."### Shipping Address\n"
            ."{{shipping_address}}\n\n"
            ."We'll send you another email when your order ships.\n\n"
            .'Thank you for your business!';
    }

    protected function getDefaultOrderFulfilledTemplate(): string
    {
        return "## Your order has been shipped!\n\n"
            ."Hi {{customer_name}},\n\n"
            ."Good news! Your order #{{order_number}} has been shipped and is on its way.\n\n"
            ."- Tracking Number: {{tracking_number}}\n"
            ."- Carrier: {{carrier}}\n"
            ."- Estimated Delivery: {{estimated_delivery}}\n\n"
            ."You can track your shipment using the tracking number above.\n\n"
            .'Thank you for your order!';
    }

    protected function getDefaultSubscriptionCreatedTemplate(): string
    {
        return "## Welcome to {{plan_name}}!\n\n"
            ."Hi {{customer_name}},\n\n"
            ."Thank you for subscribing to {{plan_name}}. Your subscription is now active!\n\n"
            ."- Plan: {{plan_name}}\n"
            ."- Amount: {{amount}}\n"
            ."- Billing Cycle: {{billing_cycle}}\n"
            ."- Next Billing Date: {{next_billing_date}}\n\n"
            ."You now have full access to all features included in your plan.\n\n"
            ."If you have any questions, please don't hesitate to contact us.";
    }

    protected function getDefaultSubscriptionRenewedTemplate(): string
    {
        return "## Subscription Renewed\n\n"
            ."Hi {{customer_name}},\n\n"
            ."Your {{plan_name}} subscription has been successfully renewed.\n\n"
            ."- Amount Charged: {{amount}}\n"
            ."- Next Billing Date: {{next_billing_date}}\n\n"
            .'Thank you for continuing your subscription with us!';
    }

    protected function getDefaultSubscriptionCancelledTemplate(): string
    {
        return "## Subscription Cancelled\n\n"
            ."Hi {{customer_name}},\n\n"
            ."Your {{plan_name}} subscription has been cancelled as requested.\n\n"
            ."- Cancellation Date: {{cancellation_date}}\n"
            ."- Access Until: {{access_until}}\n\n"
            ."You will continue to have access to your subscription until {{access_until}}.\n\n"
            ."We're sorry to see you go. If you change your mind, you can resubscribe anytime.";
    }

    protected function getDefaultPaymentFailedTemplate(): string
    {
        return "## Payment Failed - Action Required\n\n"
            ."Hi {{customer_name}},\n\n"
            ."We were unable to process your payment for {{plan_name}}.\n\n"
            ."- Amount: {{amount}}\n"
            ."- Retry Date: {{retry_date}}\n\n"
            ."Please update your payment method to avoid service interruption.\n\n"
            ."[Update Payment Method]({{update_payment_url}})\n\n"
            .'If you have any questions, please contact our support team.';
    }

    protected function getDefaultDigitalOrderFulfilledTemplate(): string
    {
        return "## Your Digital Product is Ready!\n\n"
            ."Hi {{customer_name}},\n\n"
            ."Great news! Your digital product **{{product_name}}** (Order #{{order_number}}) is now available for download.\n\n"
            ."### Download Details\n"
            ."- License Key: `{{license_key}}`\n"
            ."- Download Link: [Click here to download]({{download_url}})\n"
            ."- Link Expires: {{expires_at}}\n"
            ."- Maximum Downloads: {{max_downloads}}\n\n"
            ."**Important:** Please save your license key in a safe place. You'll need it to activate your product.\n\n"
            ."Your download link will expire after the date shown above. If you need a new link, please contact our support team.\n\n"
            .'Thank you for your purchase!';
    }

    protected function getDefaultPhysicalOrderFulfilledTemplate(): string
    {
        return "## Your Order Has Been Shipped!\n\n"
            ."Hi {{customer_name}},\n\n"
            ."Good news! Your order **{{product_name}}** (Order #{{order_number}}) has been shipped and is on its way.\n\n"
            ."### Shipping Details\n"
            ."- Tracking Number: {{tracking_number}}\n"
            ."- Carrier: {{carrier}}\n"
            ."- Estimated Delivery: {{estimated_delivery}}\n\n"
            ."### Shipping Address\n"
            ."{{shipping_address}}\n\n"
            ."You can track your shipment using the tracking number above with {{carrier}}.\n\n"
            .'Thank you for your order!';
    }

    protected function getDefaultServiceOrderFulfilledTemplate(): string
    {
        return "## Your Service is Ready!\n\n"
            ."Hi {{customer_name}},\n\n"
            ."Great news! Your service **{{product_name}}** (Order #{{order_number}}) has been activated and is ready to use.\n\n"
            ."### Service Details\n"
            ."- Service Code: `{{service_code}}`\n"
            ."- Valid Until: {{valid_until}}\n\n"
            ."### Instructions\n"
            ."{{instructions}}\n\n"
            ."**Important:** Please save your service code in a safe place. You may need it to access your service.\n\n"
            ."If you have any questions or need assistance, please don't hesitate to contact our support team.\n\n"
            .'Thank you for your purchase!';
    }
}
