<?php

use App\Services\Settings\SettingsManager;
use Tests\Feature\BaseApiTest;

uses(BaseApiTest::class);

beforeEach(function () {
    // Set up a test API key
    $this->testApiKey = 'pk_test123456789012345678901234567890';
    app(SettingsManager::class)->set('app.api_key', $this->testApiKey, 'string', false);
});

test('api key validation requires api key', function () {
    Route::middleware('api.key')->get('/test-api', fn () => response()->json(['success' => true]));

    $response = $this->getJson('/test-api');

    $response->assertStatus(401)
        ->assertJson([
            'error' => 'Unauthorized',
            'message' => 'API key is required',
        ]);
});

test('api key validation does not accept bearer token', function () {
    Route::middleware('api.key')->get('/test-api', fn () => response()->json(['success' => true]));

    $response = $this->withHeader('Authorization', "Bearer {$this->testApiKey}")
        ->getJson('/test-api');

    $response->assertStatus(401)
        ->assertJson([
            'error' => 'Unauthorized',
            'message' => 'API key is required',
        ]);
});

test('api key validation accepts valid x-api-key header', function () {
    Route::middleware('api.key')->get('/test-api', fn () => response()->json(['success' => true]));

    $response = $this->withHeader('X-API-Key', $this->testApiKey)
        ->getJson('/test-api');

    $response->assertStatus(200)
        ->assertJson(['success' => true]);
});

test('api key validation accepts valid query parameter in local environment', function () {
    Route::middleware('api.key')->get('/test-api', fn () => response()->json(['success' => true]));

    // Ensure we're in local environment
    app()->instance('env', 'local');

    $response = $this->getJson("/test-api?api_key={$this->testApiKey}");

    $response->assertStatus(200)
        ->assertJson(['success' => true]);
});

test('api key validation rejects invalid key', function () {
    Route::middleware('api.key')->get('/test-api', fn () => response()->json(['success' => true]));

    $response = $this->withHeader('X-API-Key', 'invalid_key_123')
        ->getJson('/test-api');

    $response->assertStatus(401)
        ->assertJson([
            'error' => 'Unauthorized',
            'message' => 'Invalid API key',
        ]);
});

test('api key validation rejects malformed bearer token', function () {
    Route::middleware('api.key')->get('/test-api', fn () => response()->json(['success' => true]));

    $response = $this->withHeader('Authorization', $this->testApiKey) // Missing 'Bearer ' prefix
        ->getJson('/test-api');

    $response->assertStatus(401)
        ->assertJson([
            'error' => 'Unauthorized',
            'message' => 'API key is required',
        ]);
});
