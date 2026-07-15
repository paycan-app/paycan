<?php

namespace App\Filament\Pages\Settings;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\IncompleteDsnException;

class MailSettings extends BaseSettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Mail';

    protected static ?string $title = 'Mail Settings';

    protected static ?int $navigationSort = 999;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'settings/mail';

    protected function getProviders(): array
    {
        return [app(\App\Services\Settings\Providers\MailSettingsProvider::class)];
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('previewEmail')
                ->label('Send Preview Email')
                ->requiresConfirmation()
                ->modalHeading('Send Preview Email')
                ->modalDescription('This will send a test order confirmation email to the admin email address.')
                ->modalSubmitActionLabel('Send')
                ->action(function (): void {
                    $settings = app(\App\Services\Settings\SettingsManager::class);
                    $adminEmail = $settings->get('notifications.admin_email', config('mail.from.address'));


                    $templateService = app(\App\Services\Notifications\NotificationTemplateService::class);
                    $template = $templateService->getTemplate('order_confirmation');
                    $mailConfig = $templateService->getMailConfig();

                    // Ensure valid "from"
                    $fromAddress = $mailConfig['from']['address'] ?? null;
                    $fromName = $mailConfig['from']['name'] ?? null;
                    if (! is_string($fromAddress) || ! filter_var($fromAddress, FILTER_VALIDATE_EMAIL)) {
                        $fromAddress = config('mail.from.address') ?: 'no-reply@localhost';
                    }
                    if (! is_string($fromName) || $fromName === '') {
                        $fromName = config('mail.from.name') ?: config('app.name');
                    }

                    $variables = [
                        'customer_name' => 'Admin',
                        'order_number' => 'PREVIEW-12345',
                        'order_date' => now()->format('F d, Y'),
                        'total' => '$0.00',
                        'items' => '- Sample Item (x1): $0.00',
                        'shipping_address' => '123 Demo St, City',
                    ];

                    $subjectTemplate = $template['subject'] ?: 'Order Confirmation - Order {{order_number}}';
                    $bodyTemplate = $template['body'] ?: 'Order {{order_number}} placed by {{customer_name}}';
                    $subject = $templateService->render($subjectTemplate, $variables);
                    $body = $templateService->render($bodyTemplate, $variables);

                    $mailer = $mailConfig['mailer'] ?? null;

                    $callback = function ($message) use ($adminEmail, $subject, $fromAddress, $fromName) {
                        $message->to($adminEmail)
                            ->subject('[Preview] ' . $subject)
                            ->from($fromAddress, $fromName);
                    };

                    $html = Str::markdown($body);

                    $sendFn = function ($view, $data, $callback) use ($mailer) {
                        if ($mailer) {
                            Mail::mailer($mailer)->send($view, $data, $callback);
                        } else {
                            Mail::send($view, $data, $callback);
                        }
                    };

                    try {
                        $sendFn('emails.custom', ['content' => $html], $callback);

                        Notification::make()
                            ->title('Preview email sent')
                            ->success()
                            ->body('Sent to ' . $adminEmail . ' via ' . ($mailer ?: config('mail.default')) . ' mailer.')
                            ->send();

                        logger()->info('Preview email sent to admin', ['email' => $adminEmail, 'subject' => $subject, 'mailer' => $mailer ?: config('mail.default')]);
                    } catch (IncompleteDsnException $e) {
                       
                            //fallback Mail::mailer('log')->send('emails.custom', ['content' => $html], $callback);

                            Notification::make()
                                ->title('Preview email failed')
                                ->danger()
                                ->body('Mailer ' . ($mailer ?: config('mail.default')) . ' misconfiguration: ' . $e->getMessage() . ' .')
                                ->send();
                        

                            logger()->error('Preview email failed', ['error' => $e->getMessage(), 'mailer' => $mailer ?: config('mail.default')]);
                        
                    }
                })
                ->successNotification(null)
                ->failureNotification(null),
        ];
    }
}
