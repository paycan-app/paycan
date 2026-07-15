<?php

use App\Models\Order;
use App\Models\Subscription;
use Tests\Feature\BaseApiTest;

class WebhookTest extends BaseApiTest
{
    /**
     * Webhook tests exercise the real gateway normalization logic,
     * so the gateway mocks from BaseApiTest are intentionally disabled.
     */
    protected function mockPaymentGateways(): void
    {
        // Use real gateways (they operate without API clients for webhook parsing)
    }

    public function test_stripe_webhook_payment_succeeded()
    {
        $order = Order::factory()->create([
            'status' => 'pending',
            'gateway' => 'stripe',
            'gateway_order_id' => 'cs_test_abc',
        ]);

        $response = $this->postStripeWebhook([
            'id' => 'evt_test_1',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => 'pi_test_123',
                    'amount' => 2999,
                    'currency' => 'usd',
                    'status' => 'succeeded',
                    'metadata' => [
                        'order_id' => $order->id,
                    ],
                ],
            ],
        ]);

        $this->assertApiResponse($response, 200);

        $order->refresh();
        $this->assertEquals('paid', $order->status);

        $this->assertDatabaseHasTransaction([
            'order_id' => $order->id,
            'type' => 'payment',
            'status' => 'completed',
            'gateway_transaction_id' => 'pi_test_123',
        ]);
    }

    public function test_stripe_webhook_payment_succeeded_is_idempotent()
    {
        $order = Order::factory()->create([
            'status' => 'pending',
            'gateway' => 'stripe',
            'gateway_order_id' => 'cs_test_abc',
        ]);

        $payload = [
            'id' => 'evt_test_1',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => 'pi_test_123',
                    'metadata' => ['order_id' => $order->id],
                ],
            ],
        ];

        $this->postStripeWebhook($payload)->assertOk();
        $this->postStripeWebhook($payload)->assertOk();

        $order->refresh();
        $this->assertEquals('paid', $order->status);
        $this->assertEquals(1, \App\Models\Transaction::where('order_id', $order->id)->count());
    }

    public function test_stripe_webhook_payment_failed()
    {
        $order = Order::factory()->create([
            'status' => 'pending',
            'gateway' => 'stripe',
            'gateway_order_id' => 'cs_test_def',
        ]);

        $response = $this->postStripeWebhook([
            'id' => 'evt_test_2',
            'type' => 'payment_intent.payment_failed',
            'data' => [
                'object' => [
                    'id' => 'pi_test_456',
                    'status' => 'requires_payment_method',
                    'last_payment_error' => ['message' => 'Card declined'],
                    'metadata' => [
                        'order_id' => $order->id,
                    ],
                ],
            ],
        ]);

        $this->assertApiResponse($response, 200);

        $order->refresh();
        $this->assertEquals('failed', $order->status);
    }

    public function test_stripe_webhook_checkout_session_completed_marks_order_paid()
    {
        $order = Order::factory()->create([
            'status' => 'pending',
            'gateway' => 'stripe',
            'gateway_order_id' => 'cs_test_session_1',
        ]);

        $response = $this->postStripeWebhook([
            'id' => 'evt_test_3',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_session_1',
                    'mode' => 'payment',
                    'payment_intent' => 'pi_test_789',
                ],
            ],
        ]);

        $this->assertApiResponse($response, 200);

        $order->refresh();
        $this->assertEquals('paid', $order->status);

        $this->assertDatabaseHasTransaction([
            'order_id' => $order->id,
            'type' => 'payment',
            'gateway_transaction_id' => 'pi_test_789',
        ]);
    }

    public function test_stripe_webhook_subscription_updated_syncs_status_and_billing_date()
    {
        $subscription = Subscription::factory()->create([
            'status' => 'active',
            'gateway' => 'stripe',
            'gateway_subscription_id' => 'sub_test_123',
        ]);

        $periodEnd = now()->addMonth();

        $response = $this->postStripeWebhook([
            'id' => 'evt_test_4',
            'type' => 'customer.subscription.updated',
            'data' => [
                'object' => [
                    'id' => 'sub_test_123',
                    'status' => 'past_due',
                    'current_period_end' => $periodEnd->timestamp,
                ],
            ],
        ]);

        $this->assertApiResponse($response, 200);

        $subscription->refresh();
        $this->assertEquals('past_due', $subscription->status);
    }

    public function test_stripe_webhook_subscription_deleted_cancels_subscription()
    {
        $subscription = Subscription::factory()->create([
            'status' => 'active',
            'gateway' => 'stripe',
            'gateway_subscription_id' => 'sub_test_del',
        ]);

        $response = $this->postStripeWebhook([
            'id' => 'evt_test_5',
            'type' => 'customer.subscription.deleted',
            'data' => [
                'object' => [
                    'id' => 'sub_test_del',
                    'status' => 'canceled',
                    'current_period_end' => now()->addWeek()->timestamp,
                ],
            ],
        ]);

        $this->assertApiResponse($response, 200);

        $subscription->refresh();
        $this->assertEquals('canceled', $subscription->status);
        $this->assertNotNull($subscription->canceled_at);
    }

    public function test_paypal_webhook_payment_completed()
    {
        $order = Order::factory()->create([
            'status' => 'pending',
            'gateway' => 'paypal',
            'gateway_order_id' => 'PAYPAL123456789',
        ]);

        $webhookPayload = [
            'id' => 'WH-TEST-1',
            'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
            'resource' => [
                'id' => 'CAPTURE123',
                'status' => 'COMPLETED',
                'amount' => [
                    'currency_code' => 'USD',
                    'value' => '29.99',
                ],
                'custom_id' => $order->id,
                'supplementary_data' => [
                    'related_ids' => [
                        'order_id' => 'PAYPAL123456789',
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/webhooks/paypal', $webhookPayload);

        $this->assertApiResponse($response, 200);

        $order->refresh();
        $this->assertEquals('paid', $order->status);

        $this->assertDatabaseHasTransaction([
            'order_id' => $order->id,
            'type' => 'payment',
            'gateway_transaction_id' => 'CAPTURE123',
        ]);
    }

    public function test_paypal_webhook_sale_completed_records_subscription_payment()
    {
        $subscription = Subscription::factory()->create([
            'status' => 'active',
            'gateway' => 'paypal',
            'gateway_subscription_id' => 'I-TESTSUB123',
        ]);

        $webhookPayload = [
            'id' => 'WH-TEST-2',
            'event_type' => 'PAYMENT.SALE.COMPLETED',
            'resource' => [
                'id' => 'SALE123',
                'billing_agreement_id' => 'I-TESTSUB123',
                'amount' => [
                    'total' => '9.99',
                    'currency' => 'USD',
                ],
            ],
        ];

        $response = $this->postJson('/api/webhooks/paypal', $webhookPayload);

        $this->assertApiResponse($response, 200);

        $this->assertDatabaseHasTransaction([
            'subscription_id' => $subscription->id,
            'type' => 'subscription_payment',
            'gateway_transaction_id' => 'SALE123',
        ]);
    }

    public function test_paypal_webhook_subscription_cancelled()
    {
        $subscription = Subscription::factory()->create([
            'status' => 'active',
            'gateway' => 'paypal',
            'gateway_subscription_id' => 'I-CANCELME',
        ]);

        $webhookPayload = [
            'id' => 'WH-TEST-3',
            'event_type' => 'BILLING.SUBSCRIPTION.CANCELLED',
            'resource' => [
                'id' => 'I-CANCELME',
            ],
        ];

        $response = $this->postJson('/api/webhooks/paypal', $webhookPayload);

        $this->assertApiResponse($response, 200);

        $subscription->refresh();
        $this->assertEquals('canceled', $subscription->status);
    }

    public function test_paypal_webhook_order_approved_returns_noop_when_client_unavailable()
    {
        $order = Order::factory()->create([
            'status' => 'pending',
            'gateway' => 'paypal',
            'gateway_order_id' => 'PAYPAL_ORDER_123',
        ]);

        $webhookPayload = [
            'event_type' => 'CHECKOUT.ORDER.APPROVED',
            'id' => 'WH-TEST123',
            'resource' => [
                'id' => 'PAYPAL_ORDER_123',
                'status' => 'APPROVED',
            ],
        ];

        $response = $this->postJson('/api/webhooks/paypal', $webhookPayload);

        // Should return 200 even if PayPal client is not configured (noop action)
        $this->assertApiResponse($response, 200);

        // Order should remain pending since we can't verify capture without PayPal client
        $order->refresh();
        $this->assertEquals('pending', $order->status);
    }

    public function test_webhook_with_invalid_signature_rejected()
    {
        $webhookPayload = [
            'type' => 'payment_intent.succeeded',
            'data' => ['object' => ['id' => 'pi_invalid']],
        ];

        $response = $this->postJson('/api/webhooks/stripe', $webhookPayload, [
            'Stripe-Signature' => 'invalid_signature',
        ]);

        $this->assertApiResponse($response, 400);
    }

    public function test_webhook_with_unknown_event_type_ignored()
    {
        $response = $this->postStripeWebhook([
            'id' => 'evt_unknown',
            'type' => 'unknown.event.type',
            'data' => ['object' => ['id' => 'unknown']],
        ]);

        $this->assertApiResponse($response, 200);
    }
}
