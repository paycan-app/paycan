<?php

use App\Models\AdminUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('generates a valid Sanctum token for demo user', function () {
    $adminUser = AdminUser::factory()->create();
    actingAs($adminUser, 'admin');

    $response = $this->get('/admin/web-components-demo');

    $response->assertSuccessful();

    // Get the demo user from the page component
    $pageClass = new \App\Filament\Pages\WebComponentsDemo;
    $pageClass->mount();

    expect($pageClass->token)->not->toBeNull();
    expect($pageClass->demoUser)->toBeInstanceOf(User::class);

    // Verify the token is a valid Sanctum token (plain text, not JWT)
    expect($pageClass->token)->toContain('|');

    // Verify token works for API authentication
    $apiResponse = $this->withHeaders([
        'Authorization' => 'Bearer '.$pageClass->token,
        'Accept' => 'application/json',
    ])->get('/api/user/me');

    $apiResponse->assertSuccessful();
    $apiResponse->assertJson([
        'data' => [
            'id' => $pageClass->demoUser->id,
            'email' => $pageClass->demoUser->email,
            'name' => $pageClass->demoUser->name,
        ],
    ]);
});

it('renders the demo page with working SDK wiring', function () {
    $adminUser = AdminUser::factory()->create();
    actingAs($adminUser, 'admin');

    $response = $this->get('/admin/web-components-demo');

    $response->assertSuccessful();

    // The live SDK must target the origin the page is served from, not
    // config('app.url'), which breaks with CORS when the two differ.
    $response->assertSee('apiUrl: window.location.origin', false);

    // Filament v4 exposes notifications via the FilamentNotification global.
    $response->assertSee('new FilamentNotification()', false);
    $response->assertDontSee('new Filament.Notification()', false);
});

it('can authenticate with demo token and access protected endpoints', function () {
    $adminUser = AdminUser::factory()->create();
    actingAs($adminUser, 'admin');

    // Create demo user and token
    $pageClass = new \App\Filament\Pages\WebComponentsDemo;
    $pageClass->mount();

    $token = $pageClass->token;

    // Test accessing subscriptions endpoint
    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$token,
        'Accept' => 'application/json',
    ])->get('/api/user/subscriptions');

    $response->assertSuccessful();
});

it('token is not a JWT format', function () {
    $adminUser = AdminUser::factory()->create();
    actingAs($adminUser, 'admin');

    $pageClass = new \App\Filament\Pages\WebComponentsDemo;
    $pageClass->mount();

    $token = $pageClass->token;

    // Sanctum tokens have format: {id}|{hash}
    // They are NOT JWTs (which have 3 parts separated by dots)
    $parts = explode('|', $token);
    expect($parts)->toHaveCount(2);

    // Should not be parseable as JWT
    $dotParts = explode('.', $token);
    expect(count($dotParts))->not->toBe(3);
});
