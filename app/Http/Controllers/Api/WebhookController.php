<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentGatewayFactory;
use App\Services\Payment\WebhookProcessingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Webhooks",
 *     description="Payment gateway webhook endpoints"
 * )
 */
class WebhookController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/webhooks/stripe",
     *     summary="Stripe webhook handler",
     *     description="Handles webhook events from Stripe payment gateway. This endpoint verifies the webhook signature and processes events like successful payments, subscription updates, and cancellations. No authentication required - signature validation is performed automatically.",
     *     operationId="handleStripeWebhook",
     *     tags={"Webhooks"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Stripe webhook event payload",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="id", type="string", example="evt_1234567890"),
     *             @OA\Property(property="object", type="string", example="event"),
     *             @OA\Property(property="type", type="string", example="payment_intent.succeeded", description="Event type from Stripe"),
     *             @OA\Property(property="created", type="integer", example=1234567890),
     *             @OA\Property(property="livemode", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Event data object",
     *                 @OA\Property(
     *                     property="object",
     *                     type="object",
     *                     description="The actual Stripe object (payment intent, subscription, etc.)"
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Webhook received and processed successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="received", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Webhook processed successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid webhook signature or payload",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid signature")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Error processing webhook",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function stripe(Request $request): JsonResponse
    {
        try {
            // Verify webhook signature
            $payload = $request->getContent();
            $sigHeader = $request->header('Stripe-Signature');
            $webhookSecret = config('services.stripe.webhook_secret');

            if (! $webhookSecret) {
                Log::error('Stripe webhook secret not configured');

                return response()->json(['error' => 'Webhook not configured'], 500);
            }

            // Signature verification is always enforced; local development can use
            // the secret provided by `stripe listen`
            try {
                $event = \Stripe\Webhook::constructEvent(
                    $payload,
                    $sigHeader,
                    $webhookSecret
                );
                $event = $event->toArray();
            } catch (\UnexpectedValueException $e) {
                Log::error('Invalid Stripe webhook payload', ['error' => $e->getMessage()]);

                return response()->json(['error' => 'Invalid payload'], 400);
            } catch (\Stripe\Exception\SignatureVerificationException $e) {
                Log::error('Invalid Stripe webhook signature', ['error' => $e->getMessage()]);

                return response()->json(['error' => 'Invalid signature'], 400);
            }

            // Log the incoming webhook for debugging
            Log::info('Stripe webhook received', [
                'type' => $event['type'] ?? 'unknown',
                'id' => $event['id'] ?? 'unknown',
            ]);

            // Process the webhook using the payment gateway (returns normalized data)
            $stripeGateway = PaymentGatewayFactory::create('stripe');
            $normalizedData = $stripeGateway->handleWebhook($event);

            if (! $normalizedData['success']) {
                Log::error('Failed to process Stripe webhook in gateway', [
                    'event_id' => $event['id'],
                    'error' => $normalizedData['error'] ?? 'Unknown error',
                ]);

                return response()->json([
                    'error' => 'Failed to process webhook',
                ], 500);
            }

            // Process the normalized data through WebhookProcessingService
            $webhookService = app(WebhookProcessingService::class);
            $result = $webhookService->processWebhookAction($normalizedData);

            if ($result['success']) {
                return response()->json([
                    'received' => true,
                    'message' => 'Webhook processed successfully',
                ]);
            } else {
                Log::error('Failed to process Stripe webhook in service', [
                    'event_id' => $event['id'],
                    'error' => $result['error'] ?? 'Unknown error',
                ]);

                return response()->json([
                    'error' => 'Failed to process webhook',
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error processing Stripe webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Error processing webhook',
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/webhooks/paypal",
     *     summary="PayPal webhook handler",
     *     description="Handles webhook events from PayPal payment gateway. This endpoint verifies the webhook signature and processes events like successful payments, subscription updates, and cancellations. No authentication required - signature validation is performed automatically.",
     *     operationId="handlePayPalWebhook",
     *     tags={"Webhooks"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="PayPal webhook event payload",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="id", type="string", example="WH-2WR32451HC0233532-67976317FL4543714"),
     *             @OA\Property(property="event_type", type="string", example="PAYMENT.SALE.COMPLETED", description="Event type from PayPal"),
     *             @OA\Property(property="create_time", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
     *             @OA\Property(
     *                 property="resource",
     *                 type="object",
     *                 description="Event resource object",
     *                 @OA\Property(property="id", type="string", example="7DY409201T7922549"),
     *                 @OA\Property(property="state", type="string", example="completed"),
     *                 @OA\Property(
     *                     property="amount",
     *                     type="object",
     *                     @OA\Property(property="total", type="string", example="100.00"),
     *                     @OA\Property(property="currency", type="string", example="USD")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Webhook received and processed successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="received", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Webhook processed successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid webhook signature or payload",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Invalid signature")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Error processing webhook",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function paypal(Request $request): JsonResponse
    {
        try {
            // Verify webhook signature
            $payload = $request->getContent();
            $headers = $request->headers->all();
            $webhookId = config('services.paypal.webhook_id');

            if (! $webhookId) {
                Log::error('PayPal webhook ID not configured');

                return response()->json(['error' => 'Webhook not configured'], 500);
            }

            // Verify the webhook signature using PayPal's verification (skipped only under automated tests)
            if (! app()->environment('testing')) {
                $isValid = $this->verifyPayPalWebhook($payload, $headers, $webhookId);

                if (! $isValid) {
                    Log::error('Invalid PayPal webhook signature');

                    return response()->json(['error' => 'Invalid signature'], 400);
                }
            }

            $eventData = json_decode($payload, true);

            if (! $eventData) {
                Log::error('Invalid PayPal webhook payload');

                return response()->json(['error' => 'Invalid payload'], 400);
            }

            // Log the incoming webhook for debugging
            Log::info('PayPal webhook received', [
                'event_type' => $eventData['event_type'] ?? 'unknown',
                'id' => $eventData['id'] ?? 'unknown',
            ]);

            // Process the webhook using the payment gateway (returns normalized data)
            $paypalGateway = PaymentGatewayFactory::create('paypal');
            $normalizedData = $paypalGateway->handleWebhook($eventData);

            if (! $normalizedData['success']) {
                Log::error('Failed to process PayPal webhook in gateway', [
                    'event_id' => $eventData['id'] ?? 'unknown',
                    'error' => $normalizedData['error'] ?? 'Unknown error',
                ]);

                return response()->json([
                    'error' => 'Failed to process webhook',
                ], 500);
            }

            // Process the normalized data through WebhookProcessingService
            $webhookService = app(WebhookProcessingService::class);
            $result = $webhookService->processWebhookAction($normalizedData);

            if ($result['success']) {
                return response()->json([
                    'received' => true,
                    'message' => 'Webhook processed successfully',
                ]);
            } else {
                Log::error('Failed to process PayPal webhook in service', [
                    'event_id' => $eventData['id'] ?? 'unknown',
                    'error' => $result['error'] ?? 'Unknown error',
                ]);

                return response()->json([
                    'error' => 'Failed to process webhook',
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error processing PayPal webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Error processing webhook',
            ], 500);
        }
    }

    /**
     * Verify PayPal webhook signature
     */
    private function verifyPayPalWebhook(string $payload, array $headers, string $webhookId): bool
    {
        try {
            // PayPal webhook verification requires specific headers
            $authAlgo = $headers['paypal-auth-algo'][0] ?? '';
            $transmission = $headers['paypal-transmission-id'][0] ?? '';
            $certUrl = $headers['paypal-cert-url'][0] ?? '';
            $transmissionSig = $headers['paypal-transmission-sig'][0] ?? '';
            $transmissionTime = $headers['paypal-transmission-time'][0] ?? '';

            if (! $authAlgo || ! $transmission || ! $certUrl || ! $transmissionSig || ! $transmissionTime) {
                Log::error('PayPal webhook verification failed: Missing required headers');

                return false;
            }

            $clientId = config('services.paypal.client_id');
            $clientSecret = config('services.paypal.client_secret');
            $mode = config('services.paypal.mode', 'sandbox');

            if (! $clientId || ! $clientSecret) {
                Log::error('PayPal webhook verification failed: Missing credentials');

                return false;
            }

            // Use PayPal's webhook verification API
            $baseUrl = $mode === 'sandbox'
                ? 'https://api-m.sandbox.paypal.com'
                : 'https://api-m.paypal.com';

            // Get access token
            $accessToken = $this->getPayPalAccessToken($clientId, $clientSecret, $baseUrl);

            if (! $accessToken) {
                Log::error('PayPal webhook verification failed: Could not get access token');

                return false;
            }

            // Verify webhook signature
            $verificationResponse = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
            ])->post("{$baseUrl}/v1/notifications/verify-webhook-signature", [
                'auth_algo' => $authAlgo,
                'cert_url' => $certUrl,
                'transmission_id' => $transmission,
                'transmission_sig' => $transmissionSig,
                'transmission_time' => $transmissionTime,
                'webhook_id' => $webhookId,
                'webhook_event' => json_decode($payload, true),
            ]);

            if (! $verificationResponse->successful()) {
                Log::error('PayPal webhook verification API failed', [
                    'status' => $verificationResponse->status(),
                    'response' => $verificationResponse->json(),
                ]);

                return false;
            }

            $result = $verificationResponse->json();
            $verificationStatus = $result['verification_status'] ?? '';

            if ($verificationStatus === 'SUCCESS') {
                return true;
            }

            Log::error('PayPal webhook verification failed', [
                'status' => $verificationStatus,
                'response' => $result,
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('PayPal webhook verification error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Get PayPal access token
     */
    private function getPayPalAccessToken(string $clientId, string $clientSecret, string $baseUrl): ?string
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withBasicAuth($clientId, $clientSecret)
                ->asForm()
                ->post("{$baseUrl}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials',
                ]);

            if (! $response->successful()) {
                Log::error('Failed to get PayPal access token', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);

                return null;
            }

            $data = $response->json();

            return $data['access_token'] ?? null;

        } catch (\Exception $e) {
            Log::error('Error getting PayPal access token', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
