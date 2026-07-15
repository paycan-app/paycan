<?php

namespace App\Http\Controllers\Concerns;

use App\Services\Payment\PayPalGateway;
use App\Services\Payment\StripeGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesWebhooks
{
    /**
     * Handle Stripe webhook events
     */
    protected function processStripeWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        Log::info('Stripe webhook received', [
            'payload_length' => strlen($payload),
            'has_signature' => ! empty($sigHeader),
            'webhook_secret_configured' => ! empty(config('services.stripe.webhook_secret')),
            'server_time' => now()->toISOString(),
            'app_env' => config('app.env'),
        ]);

        try {
            // Temporarily disable signature verification for testing
            if (config('app.env') === 'local') {
                Log::info('Skipping webhook signature verification in local environment');
                $event = json_decode($payload, true);
            } else {
                // Verify webhook signature with increased tolerance
                $stripeSecret = config('services.stripe.webhook_secret');
                $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $stripeSecret, 600);
            }

            Log::info('Stripe webhook event', [
                'type' => $event['type'] ?? 'unknown',
                'id' => $event['id'] ?? 'unknown',
            ]);

            // Process the event
            $paymentGateway = app(StripeGateway::class);
            $result = $paymentGateway->handleWebhook((array) $event);

            if (! $result['success']) {
                throw new \Exception($result['error'] ?? 'Webhook handling failed');
            }

            Log::info('Stripe webhook processed successfully');

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error: '.$e->getMessage(), [
                'payload_snippet' => substr($payload, 0, 200),
                'signature_present' => ! empty($sigHeader),
            ]);

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Handle PayPal webhook events
     */
    protected function processPayPalWebhook(Request $request): JsonResponse
    {
        try {
            // Process the event
            $paymentGateway = app(PayPalGateway::class);
            $result = $paymentGateway->handleWebhook($request->all());

            return response()->json(['success' => $result]);
        } catch (\Exception $e) {
            Log::error('PayPal webhook error: '.$e->getMessage());

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
