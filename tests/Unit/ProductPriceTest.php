<?php

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('auto-generates slug from title when creating', function () {
    $product = Product::factory()->create();

    $price = ProductPrice::create([
        'product_id' => $product->id,
        'title' => 'Monthly Subscription',
        'amount' => 9.99,
        'currency' => 'USD',
        'billing_period' => 'monthly',
    ]);

    expect($price->slug)->toBe('monthly-subscription');
});

test('uses provided slug if given', function () {
    $product = Product::factory()->create();

    $price = ProductPrice::create([
        'product_id' => $product->id,
        'title' => 'Monthly Subscription',
        'slug' => 'custom-slug',
        'amount' => 9.99,
        'currency' => 'USD',
        'billing_period' => 'monthly',
    ]);

    expect($price->slug)->toBe('custom-slug');
});

test('handles special characters in title when generating slug', function () {
    $product = Product::factory()->create();

    $price = ProductPrice::create([
        'product_id' => $product->id,
        'title' => 'Premium Plan @ $19.99',
        'amount' => 19.99,
        'currency' => 'USD',
        'billing_period' => 'monthly',
    ]);

    expect($price->slug)->toBe('premium-plan-at-1999');
});

test('does not regenerate slug when updating title', function () {
    $product = Product::factory()->create();

    $price = ProductPrice::create([
        'product_id' => $product->id,
        'title' => 'Original Title',
        'amount' => 9.99,
        'currency' => 'USD',
        'billing_period' => 'monthly',
    ]);

    $originalSlug = $price->slug;
    expect($originalSlug)->toBe('original-title');

    // Update title
    $price->update(['title' => 'Updated Title']);

    // Slug should remain unchanged
    $price->refresh();
    expect($price->slug)->toBe($originalSlug);
});
