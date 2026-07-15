<?php

use App\Models\User;
use App\Services\Settings\Providers\MailSettingsProvider;
use App\Services\Settings\SettingsManager;
use Filament\Schemas\Schema;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('has correct mail settings provider configuration', function () {
    $provider = app(MailSettingsProvider::class);

    expect($provider->getGroup())->toBe('mail')
        ->and($provider->getLabel())->toBe('Email Settings')
        ->and($provider->getCategory())->toBe('mail')
        ->and($provider->isEnabled())->toBeTrue();
});

it('has correct schema for mail settings', function () {
    $provider = app(MailSettingsProvider::class);
    $schema = $provider->getSchema();

    expect($schema)->toBeInstanceOf(Schema::class);
});

it('has all required mail defaults', function () {
    $provider = app(MailSettingsProvider::class);
    $defaults = $provider->getDefaults();

    expect($defaults)->toHaveKeys([
        'mailer',
        'from_address',
        'from_name',
    ]);
});

it('can save smtp mail settings', function () {
    $manager = app(SettingsManager::class);

    $manager->set('mail.mailer', 'smtp', 'string', true);
    $manager->set('mail.from_address', 'test@example.com', 'string', true);
    $manager->set('mail.from_name', 'Test App', 'string', true);
    $manager->set('smtp.host', 'smtp.gmail.com', 'string', true);
    $manager->set('smtp.port', 587, 'integer', true);
    $manager->set('smtp.encryption', 'tls', 'string', true);
    $manager->set('smtp.username', 'user@gmail.com', 'string', true);
    $manager->set('smtp.password', 'secret123', 'encrypted', false);

    expect($manager->get('mail.mailer'))->toBe('smtp')
        ->and($manager->get('mail.from_address'))->toBe('test@example.com')
        ->and($manager->get('mail.from_name'))->toBe('Test App')
        ->and($manager->get('smtp.host'))->toBe('smtp.gmail.com')
        ->and($manager->get('smtp.port'))->toBe(587)
        ->and($manager->get('smtp.encryption'))->toBe('tls')
        ->and($manager->get('smtp.username'))->toBe('user@gmail.com')
        ->and($manager->get('smtp.password'))->toBe('secret123');
});

it('can save mailgun settings', function () {
    $manager = app(SettingsManager::class);

    $manager->set('mail.mailer', 'mailgun', 'string', true);
    $manager->set('mailgun.domain', 'mg.example.com', 'string', true);
    $manager->set('mailgun.secret', 'key-123456789', 'encrypted', false);
    $manager->set('mailgun.endpoint', 'api.eu.mailgun.net', 'string', true);

    expect($manager->get('mail.mailer'))->toBe('mailgun')
        ->and($manager->get('mailgun.domain'))->toBe('mg.example.com')
        ->and($manager->get('mailgun.secret'))->toBe('key-123456789')
        ->and($manager->get('mailgun.endpoint'))->toBe('api.eu.mailgun.net');
});

it('encrypts sensitive mail settings', function () {
    $manager = app(SettingsManager::class);

    $manager->set('smtp.password', 'secret-password', 'encrypted', false);
    $manager->set('mailgun.secret', 'key-secret', 'encrypted', false);

    $smtp = $manager->getByGroup('smtp');
    $mailgun = $manager->getByGroup('mailgun');

    expect($smtp['password'])->toBe('secret-password')
        ->and($mailgun['secret'])->toBe('key-secret');
});

it('retrieves mail settings by group', function () {
    $manager = app(SettingsManager::class);

    $manager->set('mail.mailer', 'smtp', 'string', true);
    $manager->set('mail.from_address', 'noreply@example.com', 'string', true);

    $settings = $manager->getByGroup('mail');

    expect($settings)->toBeArray()
        ->and($settings)->toHaveKey('mailer')
        ->and($settings)->toHaveKey('from_address');
});

it('has default mailgun endpoint', function () {
    $provider = app(\App\Services\Settings\Providers\MailgunSettingsProvider::class);
    $defaults = $provider->getDefaults();

    expect($defaults['endpoint'])->toBe('api.mailgun.net');
});

it('can update mail settings and clear cache', function () {
    $manager = app(SettingsManager::class);

    $manager->set('mail.from_name', 'Old Name', 'string', true);
    expect($manager->get('mail.from_name'))->toBe('Old Name');

    $manager->set('mail.from_name', 'New Name', 'string', true);
    $manager->clearCache();

    expect($manager->get('mail.from_name'))->toBe('New Name');
});
