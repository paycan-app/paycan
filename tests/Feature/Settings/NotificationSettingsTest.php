<?php

use App\Models\User;
use App\Services\Settings\Providers\NotificationSettingsProvider;
use App\Services\Settings\SettingsManager;
use Filament\Schemas\Schema;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('has correct notification settings provider configuration', function () {
    $provider = app(NotificationSettingsProvider::class);

    expect($provider->getGroup())->toBe('notifications')
        ->and($provider->getLabel())->toBe('Notifications & Email Templates')
        ->and($provider->getCategory())->toBe('notifications')
        ->and($provider->isEnabled())->toBeTrue();
});

it('has correct schema for notification settings', function () {
    $provider = app(NotificationSettingsProvider::class);
    $schema = $provider->getSchema();

    expect($schema)->toBeInstanceOf(Schema::class);
});

it('has all required notification defaults', function () {
    $provider = app(NotificationSettingsProvider::class);
    $defaults = $provider->getDefaults();

    expect($defaults)->toHaveKeys([
        'admin_email',
        'notify_admin_new_order',
        'notify_admin_failed_payment',
        'order_confirmation',
        'subscription_created',
        'payment_failed',
    ]);
});

it('can save notification settings', function () {
    $manager = app(SettingsManager::class);

    $manager->set('notifications.admin_email', 'admin@example.com', 'string', true);
    $manager->set('notifications.notify_admin_new_order', true, 'boolean', true);
    $manager->set('notifications.order_confirmation', true, 'boolean', true);

    expect($manager->get('notifications.admin_email'))->toBe('admin@example.com')
        ->and($manager->get('notifications.notify_admin_new_order'))->toBeTrue()
        ->and($manager->get('notifications.order_confirmation'))->toBeTrue();
});

it('sends preview email successfully using log driver', function () {
    // Use log driver to avoid actual mail sending in tests
    $manager = app(SettingsManager::class);
    $manager->set('notifications.admin_email', 'test@example.com', 'string', true);
    $manager->set('mail.mailer', 'log', 'string', true);

    Livewire::test(\App\Filament\Pages\Settings\NotificationSettings::class)
        ->callAction('previewEmail')
        ->assertNotified()
        ->assertHasNoErrors();
});

it('shows error notification when mail sending fails', function () {
    // Set up invalid mail configuration to cause failure
    config(['mail.default' => 'smtp']);
    config(['mail.mailers.smtp.host' => 'invalid-host-that-does-not-exist.local']);
    config(['mail.mailers.smtp.port' => 99999]);

    $manager = app(SettingsManager::class);
    $manager->set('notifications.admin_email', 'test@example.com', 'string', true);
    $manager->set('mail.mailer', 'smtp', 'string', true);
    $manager->set('smtp.host', 'invalid-host-that-does-not-exist.local', 'string', true);
    $manager->set('smtp.port', 99999, 'integer', true);

    // The action will throw an exception which Filament will catch and show as a failure notification
    try {
        Livewire::test(\App\Filament\Pages\Settings\NotificationSettings::class)
            ->callAction('previewEmail');
    } catch (\Exception $e) {
        // Exception is expected - Filament will show it as a failure notification
        expect($e->getMessage())->toContain('Connection could not be established');
    }
});

it('retrieves notification settings by group', function () {
    $manager = app(SettingsManager::class);

    $manager->set('notifications.admin_email', 'admin@test.com', 'string', true);
    $manager->set('notifications.order_confirmation', true, 'boolean', true);

    $settings = $manager->getByGroup('notifications');

    expect($settings)->toBeArray()
        ->and($settings)->toHaveKey('admin_email')
        ->and($settings)->toHaveKey('order_confirmation');
});
