<?php

use App\Models\User;
use App\Services\Settings\Providers\FileDownloaderSettingsProvider;
use App\Services\Settings\Providers\FulfillmentProvidersSettingsProvider;
use App\Services\Settings\Providers\LicenseGeneratorSettingsProvider;
use App\Services\Settings\SettingsManager;
use Filament\Schemas\Schema;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('has correct fulfillment providers settings provider configuration', function () {
    $provider = app(FulfillmentProvidersSettingsProvider::class);

    expect($provider->getGroup())->toBe('downloader')
        ->and($provider->getLabel())->toBe('Fulfillment Providers')
        ->and($provider->getCategory())->toBe('fulfillment')
        ->and($provider->isEnabled())->toBeTrue();
});

it('has correct schema for fulfillment providers settings', function () {
    $provider = app(FulfillmentProvidersSettingsProvider::class);
    $schema = $provider->getSchema();

    expect($schema)->toBeInstanceOf(Schema::class);
});

it('has all required fulfillment providers defaults', function () {
    $licenseDefaults = app(LicenseGeneratorSettingsProvider::class)->getDefaults();
    $downloaderDefaults = app(FileDownloaderSettingsProvider::class)->getDefaults();

    expect($licenseDefaults)->toHaveKeys(['enabled', 'key_length', 'prefix'])
        ->and($downloaderDefaults)->toHaveKeys(['enabled', 'link_expiry', 'max_downloads']);
});

it('has default license generator settings', function () {
    $defaults = app(LicenseGeneratorSettingsProvider::class)->getDefaults();

    expect($defaults['enabled'])->toBeTrue()
        ->and($defaults['key_length'])->toBe(16)
        ->and($defaults['prefix'])->toBe('');
});

it('has default downloader settings', function () {
    $defaults = app(FileDownloaderSettingsProvider::class)->getDefaults();

    expect($defaults['enabled'])->toBeTrue()
        ->and($defaults['link_expiry'])->toBe(48)
        ->and($defaults['max_downloads'])->toBe(5);
});

it('can save license generator settings', function () {
    $manager = app(SettingsManager::class);

    $manager->set('fulfillment_providers.license_generator_enabled', true, 'boolean', true);
    $manager->set('fulfillment_providers.license_generator_key_length', 20, 'integer', true);
    $manager->set('fulfillment_providers.license_generator_prefix', 'PROD-', 'string', true);

    expect($manager->get('fulfillment_providers.license_generator_enabled'))->toBeTrue()
        ->and($manager->get('fulfillment_providers.license_generator_key_length'))->toBe(20)
        ->and($manager->get('fulfillment_providers.license_generator_prefix'))->toBe('PROD-');
});

it('can save downloader settings', function () {
    $manager = app(SettingsManager::class);

    $manager->set('fulfillment_providers.downloader_enabled', true, 'boolean', true);
    $manager->set('fulfillment_providers.downloader_link_expiry', 72, 'integer', true);
    $manager->set('fulfillment_providers.downloader_max_downloads', 10, 'integer', true);

    expect($manager->get('fulfillment_providers.downloader_enabled'))->toBeTrue()
        ->and($manager->get('fulfillment_providers.downloader_link_expiry'))->toBe(72)
        ->and($manager->get('fulfillment_providers.downloader_max_downloads'))->toBe(10);
});

it('can disable license generator', function () {
    $manager = app(SettingsManager::class);

    $manager->set('fulfillment_providers.license_generator_enabled', false, 'boolean', true);

    expect($manager->get('fulfillment_providers.license_generator_enabled'))->toBeFalse();
});

it('can disable downloader', function () {
    $manager = app(SettingsManager::class);

    $manager->set('fulfillment_providers.downloader_enabled', false, 'boolean', true);

    expect($manager->get('fulfillment_providers.downloader_enabled'))->toBeFalse();
});

it('retrieves fulfillment providers settings by group', function () {
    $manager = app(SettingsManager::class);

    $manager->set('fulfillment_providers.license_generator_enabled', true, 'boolean', true);
    $manager->set('fulfillment_providers.downloader_enabled', false, 'boolean', true);

    $settings = $manager->getByGroup('fulfillment_providers');

    expect($settings)->toBeArray()
        ->and($settings)->toHaveKey('license_generator_enabled')
        ->and($settings)->toHaveKey('downloader_enabled')
        ->and($settings['license_generator_enabled'])->toBeTrue()
        ->and($settings['downloader_enabled'])->toBeFalse();
});

it('can update fulfillment providers settings and clear cache', function () {
    $manager = app(SettingsManager::class);

    $manager->set('fulfillment_providers.license_generator_key_length', 16, 'integer', true);
    expect($manager->get('fulfillment_providers.license_generator_key_length'))->toBe(16);

    $manager->set('fulfillment_providers.license_generator_key_length', 24, 'integer', true);
    $manager->clearCache();

    expect($manager->get('fulfillment_providers.license_generator_key_length'))->toBe(24);
});

it('validates key length is within bounds', function () {
    $manager = app(SettingsManager::class);

    // Test minimum value
    $manager->set('fulfillment_providers.license_generator_key_length', 8, 'integer', true);
    expect($manager->get('fulfillment_providers.license_generator_key_length'))->toBe(8);

    // Test maximum value
    $manager->set('fulfillment_providers.license_generator_key_length', 64, 'integer', true);
    expect($manager->get('fulfillment_providers.license_generator_key_length'))->toBe(64);
});

it('validates download expiry is within bounds', function () {
    $manager = app(SettingsManager::class);

    // Test minimum value
    $manager->set('fulfillment_providers.downloader_link_expiry', 1, 'integer', true);
    expect($manager->get('fulfillment_providers.downloader_link_expiry'))->toBe(1);

    // Test maximum value (30 days)
    $manager->set('fulfillment_providers.downloader_link_expiry', 720, 'integer', true);
    expect($manager->get('fulfillment_providers.downloader_link_expiry'))->toBe(720);
});
