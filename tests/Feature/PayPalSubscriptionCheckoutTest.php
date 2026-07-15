<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Payment\PayPalGateway;
use App\Services\Payment\WebhookProcessingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\mock;
use function Pest\Laravel\post;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Normalize and process a PayPal webhook the way WebhookController does.
 */
function processPayPalWebhookPayload(array $payload): array
{
    $normalized = (new PayPalGateway)->handleWebhook($payload);

    return app(WebhookProcessingService::class)->processWebhookAction($normalized);
}

/**
 * Mock the PayPal gateway for checkout-creation tests (no real API calls).
 */
function mockPayPalCheckout(): void
{
    mock(PayPalGateway::class, function ($mock) {
        $mock->shouldReceive('createCheckoutSession')
            ->andReturn([
                'success' => true,
                'id' => 'I-MOCKSUB123',
                'status' => 'APPROVAL_PENDING',
                'url' => 'https://www.paypal.com/webapps/billing/subscriptions?ba_token=BA-MOCK',
            ]);
    });
}

beforeEach(function () {
    // Enable PayPal gateway for tests
    settings()->set('paypal.enabled', true);
    settings()->set('paypal.enable_subscriptions', true);

    $this->user = User::factory()->create();
    $this->product = Product::factory()->create([
        'title' => 'Premium Membership',
        'type' => 'subscription',
        'is_active' => true,
    ]);
    $this->price = ProductPrice::factory()->create([
        'product_id' => $this->product->id,
        'title' => 'Monthly',
        'amount' => 19.99,
        'currency' => 'USD',
        'billing_period' => 'monthly',
        'trial_days' => 0,
        'is_active' => true,
    ]);
});

it('creates subscription and order for recurring price checkout', function () {
    mockPayPalCheckout();
    actingAs($this->user);

    $response = post('/api/user/checkout', [
        'product_id' => $this->product->id,
        'product_price_id' => $this->price->id,
        'gateway' => 'paypal',
        'billing_email' => $this->user->email,
        'billing_name' => $this->user->name,
    ]);

    $response->assertStatus(201);
    $response->assertJsonStructure([
        'checkout' => [
            'session_id',
            'checkout_url',
            'order_id',
            'subscription_id',
        ],
    ]);

    // Verify order was created with correct status
    $orderId = $response->json('checkout.order_id');
    assertDatabaseHas('orders', [
        'id' => $orderId,
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'product_price_id' => $this->price->id,
        'status' => 'pending',
        'gateway' => 'paypal',
    ]);

    // Verify subscription was created with incomplete status
    $subscriptionId = $response->json('checkout.subscription_id');
    assertDatabaseHas('subscriptions', [
        'id' => $subscriptionId,
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'product_price_id' => $this->price->id,
        'order_id' => $orderId,
        'status' => 'incomplete',
        'gateway' => 'paypal',
    ]);
});

it('activates subscription when paypal capture webhook is received', function () {
    // Create order and subscription first
    $order = Order::factory()->create([
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'product_price_id' => $this->price->id,
        'status' => 'pending',
        'gateway' => 'paypal',
        'gateway_order_id' => 'PAYPAL-ORDER-123',
        'total' => 19.99,
        'currency' => 'USD',
    ]);

    $subscription = Subscription::factory()->create([
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'product_price_id' => $this->price->id,
        'order_id' => $order->id,
        'status' => 'incomplete',
        'gateway' => 'paypal',
    ]);

    // Simulate PayPal PAYMENT.CAPTURE.COMPLETED webhook
    $webhookPayload = [
        'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
        'resource' => [
            'id' => 'CAPTURE-123',
            'supplementary_data' => [
                'related_ids' => [
                    'order_id' => 'PAYPAL-ORDER-123',
                ],
            ],
        ],
    ];

    $result = processPayPalWebhookPayload($webhookPayload);

    expect($result['success'])->toBeTrue();

    // Verify order was marked as paid
    $order->refresh();
    expect($order->status)->toBe('paid');

    // Verify subscription was activated (or trialing if has trial_days)
    $subscription->refresh();
    expect($subscription->status)->toBeIn(['active', 'trialing']);

    // Verify transaction was created for the order
    assertDatabaseHas('transactions', [
        'order_id' => $order->id,
        'user_id' => $this->user->id,
        'type' => 'payment',
        'status' => 'completed',
        'gateway' => 'paypal',
        'amount' => '19.99',
        'gateway_transaction_id' => 'CAPTURE-123',
    ]);

    // The charge is recorded exactly once (no duplicate subscription_payment record)
    expect(Transaction::where('order_id', $order->id)->count())->toBe(1);
});

it('does not create duplicate transactions when webhook is received multiple times', function () {
    // Create order
    $order = Order::factory()->create([
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'product_price_id' => $this->price->id,
        'status' => 'pending',
        'gateway' => 'paypal',
        'gateway_order_id' => 'PAYPAL-ORDER-456',
        'total' => 19.99,
        'currency' => 'USD',
    ]);

    $subscription = Subscription::factory()->create([
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'product_price_id' => $this->price->id,
        'order_id' => $order->id,
        'status' => 'incomplete',
        'gateway' => 'paypal',
    ]);

    // Simulate webhook payload
    $webhookPayload = [
        'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
        'resource' => [
            'id' => 'CAPTURE-456',
            'supplementary_data' => [
                'related_ids' => [
                    'order_id' => 'PAYPAL-ORDER-456',
                ],
            ],
        ],
    ];

    // Process webhook first time
    processPayPalWebhookPayload($webhookPayload);
    $firstTransactionCount = Transaction::where('order_id', $order->id)->count();

    // Process same webhook again (simulating duplicate)
    processPayPalWebhookPayload($webhookPayload);
    $secondTransactionCount = Transaction::where('order_id', $order->id)->count();

    // Transaction count should remain the same
    expect($firstTransactionCount)->toBe($secondTransactionCount);
});

it('properly separates business logic from payment gateway', function () {
    // This test verifies architectural separation
    // Payment gateways should only handle API communication, not business logic

    $order = Order::factory()->create([
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'product_price_id' => $this->price->id,
        'status' => 'pending',
        'gateway' => 'paypal',
        'gateway_order_id' => 'PAYPAL-TEST',
        'total' => 19.99,
    ]);

    $subscription = Subscription::factory()->create([
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'product_price_id' => $this->price->id,
        'order_id' => $order->id,
        'status' => 'incomplete',
        'gateway' => 'paypal',
        'trial_ends_at' => null,
    ]);

    // WebhookProcessingService should handle all business logic
    $result = processPayPalWebhookPayload([
        'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
        'resource' => [
            'id' => 'CAPTURE-TEST',
            'supplementary_data' => [
                'related_ids' => [
                    'order_id' => 'PAYPAL-TEST',
                ],
            ],
        ],
    ]);

    // WebhookProcessingService successfully processes the webhook
    expect($result['success'])->toBeTrue();

    // Order and subscription are updated through domain services
    $order->refresh();
    $subscription->refresh();

    expect($order->status)->toBe('paid');
    expect($subscription->status)->toBe('active');

    // The charge is recorded exactly once through domain services
    $transactions = Transaction::where('order_id', $order->id)->get();
    expect($transactions)->toHaveCount(1);
});

it('sends notification emails after successful payment', function () {
    Notification::fake();

    $order = Order::factory()->create([
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'product_price_id' => $this->price->id,
        'status' => 'pending',
        'gateway' => 'paypal',
        'total' => 19.99,
    ]);

    // Trigger order status change to paid (which triggers OrderObserver)
    $order->update(['status' => 'paid']);

    // Verify user notification was sent
    Notification::assertSentTo(
        $this->user,
        \App\Notifications\PaymentSuccessNotification::class
    );
});

it('creates subscription for subscription-type product even with billing_period once', function () {
    // This tests the edge case where a subscription product has billing_period='once'
    // due to misconfiguration. The fix ensures subscriptions are still created.

    mockPayPalCheckout();

    $misconfiguredPrice = ProductPrice::factory()->create([
        'product_id' => $this->product->id, // subscription type product
        'title' => 'Misconfigured',
        'amount' => 5.00,
        'currency' => 'USD',
        'billing_period' => 'once', // Wrong! Should be recurring for subscription type
        'trial_days' => 0,
        'is_active' => true,
    ]);

    actingAs($this->user);

    $response = post('/api/user/checkout', [
        'product_id' => $this->product->id,
        'product_price_id' => $misconfiguredPrice->id,
        'gateway' => 'paypal',
        'billing_email' => $this->user->email,
        'billing_name' => $this->user->name,
    ]);

    $response->assertStatus(201);

    // Verify subscription WAS created despite billing_period='once'
    // because the product type is 'subscription'
    $subscriptionId = $response->json('checkout.subscription_id');
    expect($subscriptionId)->not->toBeNull();

    assertDatabaseHas('subscriptions', [
        'id' => $subscriptionId,
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'product_price_id' => $misconfiguredPrice->id,
        'status' => 'incomplete',
        'gateway' => 'paypal',
    ]);
});
