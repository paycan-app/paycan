<?php

use App\Models\Setting;
use Tests\Feature\BaseApiTest;

class SettingsManagementTest extends BaseApiTest
{
    protected function authenticateAdmin(): array
    {
        return ['X-API-Key' => 'test_admin_key_for_testing'];
    }

    public function test_admin_can_view_all_settings()
    {
        settings()->set('app.name', 'PayCan');
        settings()->set('stripe.publishable_key', 'pk_test_123');

        $response = $this->getJson('/api/admin/settings', $this->authenticateAdmin());

        $this->assertApiResponse($response, 200, [
            'data',
        ]);

        $settings = $response->json('data');
        $this->assertIsArray($settings);
        $this->assertSame('PayCan', $settings['app.name']);
        $this->assertSame('pk_test_123', $settings['stripe.publishable_key']);
    }

    public function test_admin_can_update_settings()
    {
        settings()->set('app.name', 'Old Name');

        $response = $this->putJson('/api/admin/settings', [
            'settings' => [
                ['key' => 'app.name', 'value' => 'New PayCan Name', 'type' => 'string'],
                ['key' => 'app.new_setting', 'value' => 'new_value', 'type' => 'string'],
            ],
        ], $this->authenticateAdmin());

        $this->assertApiResponse($response, 200, [
            'message',
        ]);

        $this->assertSame('New PayCan Name', settings('app.name'));
        $this->assertSame('new_value', settings('app.new_setting'));

        $this->assertDatabaseHas('settings', [
            'group' => 'app',
            'key' => 'name',
            'value' => 'New PayCan Name',
        ]);
    }

    public function test_settings_update_requires_settings_array()
    {
        $response = $this->putJson('/api/admin/settings', [
            'app_name' => 'Flat payload is not accepted',
        ], $this->authenticateAdmin());

        $this->assertApiResponse($response, 422);
        $this->assertArrayHasKey('settings', $response->json('errors'));
    }

    public function test_unauthorized_access_to_settings()
    {
        $response = $this->getJson('/api/admin/settings');
        $this->assertApiResponse($response, 401);

        $response = $this->putJson('/api/admin/settings', [
            'settings' => [
                ['key' => 'app.name', 'value' => 'Hacked'],
            ],
        ]);
        $this->assertApiResponse($response, 401);
    }

    public function test_settings_with_array_values()
    {
        $response = $this->putJson('/api/admin/settings', [
            'settings' => [
                [
                    'key' => 'payment.gateways_enabled',
                    'value' => ['stripe', 'paypal'],
                    'type' => 'array',
                ],
            ],
        ], $this->authenticateAdmin());

        $this->assertApiResponse($response, 200);

        $this->assertSame(['stripe', 'paypal'], settings('payment.gateways_enabled'));

        $setting = Setting::where('group', 'payment')->where('key', 'gateways_enabled')->first();
        $this->assertEquals(['stripe', 'paypal'], json_decode($setting->value, true));
    }

    public function test_settings_encryption_for_sensitive_data()
    {
        $response = $this->putJson('/api/admin/settings', [
            'settings' => [
                [
                    'key' => 'stripe.api_key',
                    'value' => 'sk_test_sensitive_key_123',
                    'type' => 'encrypted',
                ],
            ],
        ], $this->authenticateAdmin());

        $this->assertApiResponse($response, 200);

        // Stored encrypted at rest
        $setting = Setting::where('group', 'stripe')->where('key', 'api_key')->first();
        $this->assertNotEquals('sk_test_sensitive_key_123', $setting->value);

        // Decrypted when read through the settings manager
        $this->assertSame('sk_test_sensitive_key_123', settings('stripe.api_key'));
    }
}
