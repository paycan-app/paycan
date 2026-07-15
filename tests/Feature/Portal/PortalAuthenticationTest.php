<?php

use App\Models\User;
use App\Services\PortalService;
use Illuminate\Support\Facades\URL;

test('portal requires signed url', function () {
    $user = User::factory()->create();

    $response = $this->get(route('portal', ['user' => $user->id]));

    $response->assertForbidden();
});

test('portal accepts valid signed url', function () {
    $user = User::factory()->create();

    $portalUrl = PortalService::generatePortalUrl($user->id);

    $response = $this->get($portalUrl);

    $response->assertSuccessful();
});

test('portal rejects expired signed url', function () {
    $user = User::factory()->create();

    // Generate expired URL (0 seconds = already expired)
    $expiredUrl = URL::temporarySignedRoute(
        'portal',
        now()->subHour(),
        ['user' => $user->id]
    );

    $response = $this->get($expiredUrl);

    $response->assertForbidden();
});

test('portal rejects invalid user id', function () {
    $portalUrl = PortalService::generatePortalUrl('non-existent-user');

    $response = $this->get($portalUrl);

    $response->assertForbidden();
});

test('portal generates new JWT token on each access', function () {
    $user = User::factory()->create();

    // First access with signed URL
    $portalUrl = PortalService::generatePortalUrl($user->id);
    $response1 = $this->get($portalUrl);
    $response1->assertSuccessful();

    $token1 = $response1->viewData('page')['props']['userToken'];

    // Second access with same signed URL gets new JWT token
    $response2 = $this->get($portalUrl);
    $response2->assertSuccessful();

    $token2 = $response2->viewData('page')['props']['userToken'];

    // Tokens should be different (new token generated each time)
    expect($token1)->not->toBe($token2);
});
