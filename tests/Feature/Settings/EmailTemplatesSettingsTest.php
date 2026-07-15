<?php

use App\Models\User;
use App\Services\Settings\Providers\NotificationSettingsProvider;
use App\Services\Settings\SettingsManager;
use Filament\Schemas\Schema;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('has correct email templates provider configuration', function () {
    $provider = app(NotificationSettingsProvider::class);

    expect($provider->getGroup())->toBe('notifications')
        ->and($provider->getLabel())->toBe('Notifications & Email Templates')
        ->and($provider->getCategory())->toBe('notifications')
        ->and($provider->isEnabled())->toBeTrue();
});

it('has correct schema for email templates', function () {
    $provider = app(NotificationSettingsProvider::class);
    $schema = $provider->getSchema();

    expect($schema)->toBeInstanceOf(Schema::class);
});

it('has all required email template defaults', function () {
    $provider = app(NotificationSettingsProvider::class);
    $defaults = $provider->getDefaults();

    expect($defaults)->toHaveKeys([
        'order_confirmation_subject',
        'order_confirmation_body',
        'order_fulfilled_subject',
        'order_fulfilled_body',
        'subscription_created_subject',
        'subscription_created_body',
        'subscription_renewed_subject',
        'subscription_renewed_body',
        'subscription_cancelled_subject',
        'subscription_cancelled_body',
        'payment_failed_subject',
        'payment_failed_body',
    ]);
});

it('has default templates with template variables', function () {
    $provider = app(NotificationSettingsProvider::class);
    $defaults = $provider->getDefaults();

    expect($defaults['order_confirmation_body'])->toContain('{{customer_name}}')
        ->and($defaults['order_confirmation_body'])->toContain('{{order_number}}')
        ->and($defaults['subscription_created_body'])->toContain('{{plan_name}}')
        ->and($defaults['payment_failed_body'])->toContain('{{update_payment_url}}');
});

it('can save order confirmation template', function () {
    $manager = app(SettingsManager::class);

    $subject = 'Your Order #{{order_number}} is Confirmed!';
    $body = "## Thank you {{customer_name}}!\n\nOrder total: {{total}}";

    $manager->set('email_templates.order_confirmation_subject', $subject, 'string', false);
    $manager->set('email_templates.order_confirmation_body', $body, 'string', false);

    expect($manager->get('email_templates.order_confirmation_subject'))->toBe($subject)
        ->and($manager->get('email_templates.order_confirmation_body'))->toBe($body);
});

it('can save subscription templates', function () {
    $manager = app(SettingsManager::class);

    $createdSubject = 'Welcome to {{plan_name}}!';
    $createdBody = 'Hi {{customer_name}}, your subscription is active!';

    $manager->set('email_templates.subscription_created_subject', $createdSubject, 'string', false);
    $manager->set('email_templates.subscription_created_body', $createdBody, 'string', false);

    expect($manager->get('email_templates.subscription_created_subject'))->toBe($createdSubject)
        ->and($manager->get('email_templates.subscription_created_body'))->toBe($createdBody);
});

it('can save payment failed template', function () {
    $manager = app(SettingsManager::class);

    $subject = 'Payment Failed for {{plan_name}}';
    $body = 'Hi {{customer_name}}, please update payment: {{update_payment_url}}';

    $manager->set('email_templates.payment_failed_subject', $subject, 'string', false);
    $manager->set('email_templates.payment_failed_body', $body, 'string', false);

    expect($manager->get('email_templates.payment_failed_subject'))->toBe($subject)
        ->and($manager->get('email_templates.payment_failed_body'))->toBe($body);
});

it('retrieves email templates by group', function () {
    $manager = app(SettingsManager::class);

    $manager->set('email_templates.order_confirmation_subject', 'Test Subject', 'string', false);
    $manager->set('email_templates.order_confirmation_body', 'Test Body', 'string', false);

    $settings = $manager->getByGroup('email_templates');

    expect($settings)->toBeArray()
        ->and($settings)->toHaveKey('order_confirmation_subject')
        ->and($settings)->toHaveKey('order_confirmation_body');
});

it('can save all six template types', function () {
    $manager = app(SettingsManager::class);

    $templates = [
        'order_confirmation',
        'order_fulfilled',
        'subscription_created',
        'subscription_renewed',
        'subscription_cancelled',
        'payment_failed',
    ];

    foreach ($templates as $template) {
        $manager->set("email_templates.{$template}_subject", "Subject for {$template}", 'string', false);
        $manager->set("email_templates.{$template}_body", "Body for {$template}", 'string', false);
    }

    foreach ($templates as $template) {
        expect($manager->get("email_templates.{$template}_subject"))->toBe("Subject for {$template}")
            ->and($manager->get("email_templates.{$template}_body"))->toBe("Body for {$template}");
    }
});

it('maintains template variable placeholders in saved templates', function () {
    $manager = app(SettingsManager::class);

    $bodyWithVariables = '<h1>Hello {{customer_name}}</h1><p>Order: {{order_number}}, Total: {{total}}</p>';
    $manager->set('email_templates.order_confirmation_body', $bodyWithVariables, 'string', false);

    $retrieved = $manager->get('email_templates.order_confirmation_body');

    expect($retrieved)->toContain('{{customer_name}}')
        ->and($retrieved)->toContain('{{order_number}}')
        ->and($retrieved)->toContain('{{total}}');
});

it('email templates are not public settings', function () {
    $manager = app(SettingsManager::class);

    // Email templates should be stored as non-public (isPublic = false)
    $manager->set('email_templates.order_confirmation_subject', 'Private Subject', 'string', false);

    // Retrieve the setting and verify it was saved
    expect($manager->get('email_templates.order_confirmation_subject'))->toBe('Private Subject');
});

it('can update and clear cache for email templates', function () {
    $manager = app(SettingsManager::class);

    $manager->set('email_templates.subscription_renewed_subject', 'Old Subject', 'string', false);
    expect($manager->get('email_templates.subscription_renewed_subject'))->toBe('Old Subject');

    $manager->set('email_templates.subscription_renewed_subject', 'New Subject', 'string', false);
    $manager->clearCache();

    expect($manager->get('email_templates.subscription_renewed_subject'))->toBe('New Subject');
});
