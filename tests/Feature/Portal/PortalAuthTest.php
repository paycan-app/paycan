<?php

use App\Models\User;
use App\Services\PortalService;
use Illuminate\Support\Facades\URL;

it('validates signed portal URL and generates JWT token', function () {
    $user = User::factory()->create();

    // Generate signed portal URL
    $portalUrl = PortalService::generatePortalUrl($user->id, 24);

    // Access portal with signed URL
    $response = $this->get($portalUrl);

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Portal/App')
        ->has('userToken')
        ->has('apiBaseUrl')
    );
});

it('rejects expired signed URL', function () {
    $user = User::factory()->create();

    // Generate expired URL (expires immediately)
    $expiredUrl = URL::temporarySignedRoute(
        'portal',
        now()->subMinute(),
        ['user' => $user->id]
    );

    // Access portal with expired URL
    $response = $this->get($expiredUrl);

    $response->assertForbidden();
});

it('rejects unsigned URL', function () {
    $user = User::factory()->create();

    // Access portal without signature
    $response = $this->get(route('portal', ['user' => $user->id]));

    $response->assertForbidden();
});

it('rejects tampered signed URL', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    // Generate signed URL for one user
    $portalUrl = PortalService::generatePortalUrl($user->id, 24);

    // Tamper with URL by changing user ID
    $tamperedUrl = str_replace("user={$user->id}", "user={$otherUser->id}", $portalUrl);

    // Access portal with tampered URL
    $response = $this->get($tamperedUrl);

    $response->assertForbidden();
});

it('rejects portal access for non-existent user', function () {
    // Generate signed URL with non-existent user ID
    $portalUrl = URL::temporarySignedRoute(
        'portal',
        now()->addHours(24),
        ['user' => 99999]
    );

    // Access portal with non-existent user
    $response = $this->get($portalUrl);

    $response->assertForbidden();
});

it('rejects portal access without user parameter', function () {
    // Generate signed URL without user parameter
    $portalUrl = URL::temporarySignedRoute(
        'portal',
        now()->addHours(24),
        []
    );

    // Access portal without user parameter
    $response = $this->get($portalUrl);

    $response->assertForbidden();
});

it('generates unique JWT token for each portal access', function () {
    $user = User::factory()->create();

    // Generate signed portal URL
    $portalUrl = PortalService::generatePortalUrl($user->id, 24);

    // Access portal first time
    $response1 = $this->get($portalUrl);
    $token1 = $response1->viewData('page')['props']['userToken'];

    // Access portal second time (same URL should work again)
    $response2 = $this->get($portalUrl);
    $token2 = $response2->viewData('page')['props']['userToken'];

    // Tokens should be different
    expect($token1)->not->toBe($token2);
});

it('portal demo route generates valid signed URL', function () {
    // Access portal demo
    $response = $this->get(route('portal.demo'));

    $response->assertRedirect();

    // Extract redirect URL
    $redirectUrl = $response->headers->get('Location');

    // Verify it redirects to portal route with signature
    expect($redirectUrl)->toContain('/portal?');
    expect($redirectUrl)->toContain('signature=');
    expect($redirectUrl)->toContain('user=');
});
