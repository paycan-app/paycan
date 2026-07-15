<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use App\Notifications\AdminNewOrderNotification;
use App\Notifications\AdminPaymentFailedNotification;
use App\Notifications\DigitalOrderFulfilledNotification;
use App\Notifications\OrderFulfilledNotification;
use App\Notifications\PaymentSuccessNotification;
use App\Notifications\PhysicalOrderFulfilledNotification;
use App\Notifications\ServiceOrderFulfilledNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('sends user and admin notifications when order status changes to paid', function () {
    Notification::fake();

    // Set admin email for testing
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'admin_email', 'value' => 'admin@example.com', 'type' => 'string']);
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'notify_admin_new_order', 'value' => '1', 'type' => 'boolean']);
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'order_confirmation', 'value' => '1', 'type' => 'boolean']);

    $user = User::factory()->create();
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
    ]);

    // Update order status to paid
    $order->update(['status' => 'paid']);

    // Assert user received notification
    Notification::assertSentTo($user, PaymentSuccessNotification::class);

    // Assert admin received notification
    Notification::assertSentOnDemand(AdminNewOrderNotification::class, function ($notification, $channels, $notifiable) {
        return $notifiable->routes['mail'] === 'admin@example.com';
    });
});

test('does not send admin notification when setting is disabled', function () {
    Notification::fake();

    // Set admin email for testing
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'admin_email', 'value' => 'admin@example.com', 'type' => 'string']);
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'notify_admin_new_order', 'value' => '0', 'type' => 'boolean']);
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'order_confirmation', 'value' => '1', 'type' => 'boolean']);

    $user = User::factory()->create();
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
    ]);

    // Update order status to paid (admin notification already disabled via Setting)
    $order->update(['status' => 'paid']);

    // Assert user received notification
    Notification::assertSentTo($user, PaymentSuccessNotification::class);

    // Assert admin did NOT receive notification
    Notification::assertSentTimes(AdminNewOrderNotification::class, 0);
});

test('sends admin notification when payment fails', function () {
    Notification::fake();

    // Set admin email for testing
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'admin_email', 'value' => 'admin@example.com', 'type' => 'string']);
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'notify_admin_failed_payment', 'value' => '1', 'type' => 'boolean']);

    $user = User::factory()->create();
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
    ]);

    // Update order status to failed
    $order->update([
        'status' => 'failed',
        'meta' => [
            'payment_failed_at' => now(),
            'failure_reason' => 'Card declined',
        ],
    ]);

    // Assert admin received failure notification
    Notification::assertSentOnDemand(AdminPaymentFailedNotification::class, function ($notification, $channels, $notifiable) {
        return $notifiable->routes['mail'] === 'admin@example.com';
    });
});

test('does not send admin failure notification when setting is disabled', function () {
    Notification::fake();

    // Set admin email for testing
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'admin_email', 'value' => 'admin@example.com', 'type' => 'string']);
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'notify_admin_failed_payment', 'value' => '0', 'type' => 'boolean']);

    $user = User::factory()->create();
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
    ]);

    // Update order status to failed
    $order->update(['status' => 'failed']);

    // Assert admin did NOT receive notification
    Notification::assertSentTimes(AdminPaymentFailedNotification::class, 0);
});

test('sends notifications when order status changes to completed', function () {
    Notification::fake();

    // Set admin email for testing
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'admin_email', 'value' => 'admin@example.com', 'type' => 'string']);
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'notify_admin_new_order', 'value' => '1', 'type' => 'boolean']);
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'order_confirmation', 'value' => '1', 'type' => 'boolean']);

    $user = User::factory()->create();
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
    ]);

    // Update order status to completed
    $order->update(['status' => 'completed']);

    // Assert user received notification
    Notification::assertSentTo($user, PaymentSuccessNotification::class);

    // Assert admin received notification
    Notification::assertSentOnDemand(AdminNewOrderNotification::class);
});

test('sends digital order fulfilled notification with download link for digital products', function () {
    Notification::fake();

    // Enable digital order fulfilled notifications
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'digital_order_fulfilled', 'value' => '1', 'type' => 'boolean']);
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'order_confirmation', 'value' => '1', 'type' => 'boolean']);

    $user = User::factory()->create();
    $product = Product::factory()->create(['type' => 'digital']);
    $price = ProductPrice::factory()->create(['product_id' => $product->id]);

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'product_price_id' => $price->id,
        'status' => 'pending',
    ]);

    // Update order status to paid (triggers fulfillment)
    $order->update(['status' => 'paid']);

    // Assert user received digital order fulfilled notification
    Notification::assertSentTo($user, DigitalOrderFulfilledNotification::class, function ($notification) use ($order) {
        $array = $notification->toArray($order->user);

        return $array['type'] === 'digital' && $array['order_id'] === $order->id;
    });
});

test('sends physical order fulfilled notification with tracking info for physical products', function () {
    Notification::fake();

    // Enable physical order fulfilled notifications
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'physical_order_fulfilled', 'value' => '1', 'type' => 'boolean']);
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'order_confirmation', 'value' => '1', 'type' => 'boolean']);

    $user = User::factory()->create();
    $product = Product::factory()->create(['type' => 'physical']);
    $price = ProductPrice::factory()->create(['product_id' => $product->id]);

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'product_price_id' => $price->id,
        'status' => 'pending',
    ]);

    // Update order status to paid (triggers fulfillment)
    $order->update(['status' => 'paid']);

    // Assert user received physical order fulfilled notification
    Notification::assertSentTo($user, PhysicalOrderFulfilledNotification::class, function ($notification) use ($order) {
        $array = $notification->toArray($order->user);

        return $array['type'] === 'physical' && $array['order_id'] === $order->id;
    });
});

test('sends service order fulfilled notification with service code for service products', function () {
    Notification::fake();

    // Enable service order fulfilled notifications
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'service_order_fulfilled', 'value' => '1', 'type' => 'boolean']);
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'order_confirmation', 'value' => '1', 'type' => 'boolean']);

    $user = User::factory()->create();
    $product = Product::factory()->create(['type' => 'service']);
    $price = ProductPrice::factory()->create(['product_id' => $product->id]);

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'product_price_id' => $price->id,
        'status' => 'pending',
    ]);

    // Update order status to paid (triggers fulfillment)
    $order->update(['status' => 'paid']);

    // Assert user received service order fulfilled notification
    Notification::assertSentTo($user, ServiceOrderFulfilledNotification::class, function ($notification) use ($order) {
        $array = $notification->toArray($order->user);

        return $array['type'] === 'service' && $array['order_id'] === $order->id;
    });
});

test('does not send fulfillment notification for subscription products', function () {
    Notification::fake();

    // Enable order fulfilled notifications
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'order_fulfilled', 'value' => '1', 'type' => 'boolean']);
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'order_confirmation', 'value' => '1', 'type' => 'boolean']);

    $user = User::factory()->create();
    $product = Product::factory()->create(['type' => 'subscription']);
    $price = ProductPrice::factory()->create(['product_id' => $product->id, 'billing_period' => 'monthly']);

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'product_price_id' => $price->id,
        'status' => 'pending',
    ]);

    // Update order status to paid (triggers fulfillment)
    $order->update(['status' => 'paid']);

    // Subscriptions get subscription notifications instead of fulfillment ones
    Notification::assertSentTimes(OrderFulfilledNotification::class, 0);
});

test('digital order fulfilled notification includes download url and license key', function () {
    Notification::fake();

    // Enable digital order fulfilled notifications
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'digital_order_fulfilled', 'value' => '1', 'type' => 'boolean']);
    \App\Models\Setting::create(['group' => 'notifications', 'key' => 'order_confirmation', 'value' => '1', 'type' => 'boolean']);

    $user = User::factory()->create();
    $product = Product::factory()->create(['type' => 'digital', 'title' => 'Test Software']);
    $price = ProductPrice::factory()->create(['product_id' => $product->id]);

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'product_price_id' => $price->id,
        'status' => 'pending',
    ]);

    // Update order status to paid (triggers fulfillment)
    $order->update(['status' => 'paid']);

    // Assert user received digital order fulfilled notification with correct data
    Notification::assertSentTo($user, DigitalOrderFulfilledNotification::class, function ($notification) use ($order) {
        // Get the fulfillment to check the data
        $fulfillment = $order->fulfillments()->first();

        expect($fulfillment)->not->toBeNull();
        expect($fulfillment->meta)->toHaveKey('download_url');
        expect($fulfillment->meta)->toHaveKey('license_key');
        expect($fulfillment->meta)->toHaveKey('expires_at');
        expect($fulfillment->meta)->toHaveKey('max_downloads');

        return true;
    });
});
