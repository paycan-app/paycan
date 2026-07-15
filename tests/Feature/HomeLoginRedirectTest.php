<?php

uses(Tests\TestCase::class);

it('redirects the home page to the admin login', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('filament.admin.auth.login'));
});

it('redirects /login to the admin login', function () {
    $response = $this->get('/login');

    $response->assertRedirect(route('filament.admin.auth.login'));
});
