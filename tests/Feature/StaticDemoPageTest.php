<?php

uses(Tests\TestCase::class);

it('normalizes a missing trailing slash so relative assets resolve', function () {
    $html = file_get_contents(public_path('demo/index.html'));

    expect($html)->toContain("location.pathname.endsWith('/')")
        ->and($html)->toContain('location.replace(location.pathname');
});

it('ships every relative asset the demo page references', function (string $asset) {
    expect(file_exists(public_path('demo/'.$asset)))->toBeTrue();
})->with([
    'logo' => 'assets/logo.jpg',
    'demo script' => 'demo.js',
    'mock api' => 'mock-api.js',
    'vendored sdk' => 'vendor/paycan-sdk.js',
    'favicon' => 'favicon.ico',
    'success page' => 'success.html',
]);

it('references only assets that exist inside the demo folder', function () {
    $html = file_get_contents(public_path('demo/index.html'));

    preg_match_all('/(?:src|href)="(?!https?:|#|\/)([^"]+)"/', $html, $matches);

    expect($matches[1])->not->toBeEmpty();

    foreach ($matches[1] as $relative) {
        expect(file_exists(public_path('demo/'.$relative)))
            ->toBeTrue("Missing demo asset: {$relative}");
    }
});
