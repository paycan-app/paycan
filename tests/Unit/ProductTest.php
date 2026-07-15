<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('auto-generates slug from title when creating', function () {
    $product = Product::create([
        'title' => 'Amazing Product',
        'type' => 'digital',
        'description' => 'A great product',
    ]);

    expect($product->slug)->toBe('amazing-product');
});

test('uses provided slug if given', function () {
    $product = Product::create([
        'title' => 'Amazing Product',
        'slug' => 'custom-product-slug',
        'type' => 'digital',
        'description' => 'A great product',
    ]);

    expect($product->slug)->toBe('custom-product-slug');
});

test('handles special characters in title when generating slug', function () {
    $product = Product::create([
        'title' => 'Super Cool Product @ $99.99!',
        'type' => 'physical',
        'description' => 'An expensive product',
    ]);

    expect($product->slug)->toBe('super-cool-product-at-9999');
});

test('does not regenerate slug when updating title', function () {
    $product = Product::create([
        'title' => 'Original Product Name',
        'type' => 'service',
        'description' => 'Original description',
    ]);

    $originalSlug = $product->slug;
    expect($originalSlug)->toBe('original-product-name');

    // Update title
    $product->update(['title' => 'Completely Different Name']);

    // Slug should remain unchanged
    $product->refresh();
    expect($product->slug)->toBe($originalSlug);
});
