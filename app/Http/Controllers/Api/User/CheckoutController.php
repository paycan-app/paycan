<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Requests\Api\User\CheckoutCreateRequest;
use App\Http\Requests\Api\User\CheckoutPortalRequest;
use App\Http\Requests\Api\User\CheckoutPreviewRequest;
use App\Models\ProductPrice;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="User Checkout",
 *     description="User checkout and payment initiation endpoints"
 * )
 */
class CheckoutController extends UserApiController
{
    /**
     * @OA\Post(
     *     path="/api/user/checkout",
     *     summary="Create order and initiate checkout",
     *     description="Create a new order and get payment gateway checkout URL",
     *     operationId="createCheckout",
     *     tags={"User Checkout"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Checkout data",
     *
     *         @OA\JsonContent(
     *             required={"product_price_id", "gateway"},
     *
     *             @OA\Property(property="product_price_id", type="integer", example=1, description="Product price ID"),
     *             @OA\Property(property="gateway", type="string", enum={"stripe", "paypal"}, example="stripe", description="Payment gateway"),
     *             @OA\Property(property="quantity", type="integer", minimum=1, example=1, description="Quantity (optional, default 1)"),
     *             @OA\Property(
     *                 property="shipping_address",
     *                 type="object",
     *                 description="Shipping address for physical products (optional)",
     *                 @OA\Property(property="line1", type="string", example="123 Main St"),
     *                 @OA\Property(property="line2", type="string", example="Apt 4B"),
     *                 @OA\Property(property="city", type="string", example="New York"),
     *                 @OA\Property(property="state", type="string", example="NY"),
     *                 @OA\Property(property="postal_code", type="string", example="10001"),
     *                 @OA\Property(property="country", type="string", example="US")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Checkout session created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="checkout",
     *                 type="object",
     *                 @OA\Property(property="session_id", type="string", example="cs_test_..."),
     *                 @OA\Property(property="checkout_url", type="string", example="https://checkout.stripe.com/pay/cs_test_..."),
     *                 @OA\Property(property="order_id", type="string", example="ord_123"),
     *                 @OA\Property(property="subscription_id", type="string", nullable=true, example="sub_123")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=502,
     *         description="Payment provider failed to create a checkout session",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Product not available",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function create(CheckoutCreateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $price = ProductPrice::with('product')->findOrFail($validated['product_price_id']);

        // Resolve the user: session guard, then Sanctum bearer token (this route is
        // public so guests can check out, which means no auth middleware runs)
        $user = $request->user() ?? $request->user('sanctum');

        // Guest checkout: reuse the account tied to the billing email or create one
        if (! $user && ! empty($validated['billing_email'])) {
            $user = \App\Models\User::firstOrCreate(
                ['email' => $validated['billing_email']],
                [
                    'name' => $validated['billing_name'] ?? 'Guest',
                    'password' => bcrypt(str()->random(32)),
                ]
            );
        }

        if (! $user) {
            return response()->json([
                'error' => 'User authentication required',
            ], 401);
        }

        $quantity = (int) ($validated['quantity'] ?? 1);
        $billingCountry = $validated['billing_country'] ?? null;
        $billingState = $validated['billing_state'] ?? null;

        $paymentService = app(\App\Services\Payment\PaymentService::class);
        $totals = $paymentService->calculateTotals($price, $quantity);

        // Check if this is a subscription based on billing period OR product type
        // This ensures subscriptions are created even if billing_period is misconfigured
        $isSubscription = $price->billing_period !== 'once' || $price->product->type === 'subscription';

        // Create order (tax is handled by the payment gateway, not calculated here)
        $order = $user->orders()->create([
            'product_id' => $price->product_id,
            'product_price_id' => $price->id,
            'order_number' => 'ORD-'.strtoupper(uniqid()),
            'status' => 'pending',
            'total' => $totals['total'],
            'currency' => $price->currency,
            'tax' => 0,
            'billing_email' => $validated['billing_email'] ?? $user->email,
            'billing_name' => $validated['billing_name'] ?? $user->name,
            'billing_country' => $billingCountry,
            'billing_state' => $billingState,
            'gateway' => $validated['gateway'],
            'quantity' => $quantity,
            'meta' => [
                'shipping_address' => $validated['shipping_address'] ?? null,
                'billing_country' => $billingCountry,
                'billing_state' => $billingState,
                'source' => 'api',
            ],
        ]);

        // Create subscription record if this is a subscription product
        $subscription = null;
        if ($isSubscription) {
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'product_id' => $price->product_id,
                'product_price_id' => $price->id,
                'order_id' => $order->id,
                'title' => $price->product->title.' - '.$price->title,
                'status' => 'incomplete',
                'gateway' => $validated['gateway'],
                'trial_ends_at' => $price->trial_days > 0
                    ? now()->addDays($price->trial_days)
                    : null,
            ]);
        }

        // Generate checkout URL based on gateway
        $checkoutUrl = $this->generateCheckoutUrl($order, $validated['gateway'], $subscription);

        if ($checkoutUrl === null) {
            $order->update(['status' => 'failed']);
            $subscription?->update(['status' => 'canceled', 'canceled_at' => now(), 'ends_at' => now()]);

            return response()->json([
                'error' => 'Failed to create a checkout session with the payment provider. Please try again or use a different payment method.',
            ], 502);
        }

        return response()->json([
            'checkout' => [
                'session_id' => $order->gateway_order_id,
                'checkout_url' => $checkoutUrl,
                'order_id' => $order->id,
                'subscription_id' => $subscription?->id,
            ],
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/user/checkout/portal",
     *     summary="Create portal checkout session",
     *     description="Create a checkout session for the portal embedded view",
     *     operationId="createPortalCheckout",
     *     tags={"User Checkout"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Portal checkout data",
     *
     *         @OA\JsonContent(
     *             required={"product_price_id", "gateway"},
     *
     *             @OA\Property(property="product_price_id", type="integer", example=1),
     *             @OA\Property(property="gateway", type="string", enum={"stripe", "paypal"}, example="stripe"),
     *             @OA\Property(property="quantity", type="integer", minimum=1, example=1),
     *             @OA\Property(property="return_url", type="string", example="https://example.com/success")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Portal checkout session created",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="session_id", type="string", example="cs_test_..."),
     *             @OA\Property(property="checkout_url", type="string", example="https://checkout.stripe.com/..."),
     *             @OA\Property(property="order", ref="#/components/schemas/Order")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function portal(CheckoutPortalRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = auth()->user();

        if (! $user) {
            return response()->json([
                'error' => 'User authentication required',
            ], 401);
        }

        try {
            $stripeGateway = app(\App\Services\Payment\StripeGateway::class);
            $result = $stripeGateway->createCustomerPortalSession(
                $user,
                $validated['return_url'] ?? config('app.url')
            );

            if (! $result['success']) {
                return response()->json([
                    'error' => $result['error'] ?? 'Failed to create customer portal session',
                ], 500);
            }

            return response()->json([
                'portal' => [
                    'url' => $result['url'],
                    'session_id' => $result['id'] ?? null,
                ],
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Customer portal creation error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Failed to create customer portal session',
            ], 500);
        }
    }

    /**
     * Generate checkout URL for payment gateway.
     *
     * Returns null when the gateway session could not be created so the caller
     * can fail the order and report an honest error to the client.
     */
    private function generateCheckoutUrl($order, string $gateway, ?Subscription $subscription = null): ?string
    {
        try {
            $gatewayService = $gateway === 'stripe'
                ? app(\App\Services\Payment\StripeGateway::class)
                : app(\App\Services\Payment\PayPalGateway::class);

            $productPrice = $order->productPrice->load('product');

            // Generate secure cancellation token
            $cancellationToken = $order->getCancellationToken();

            // Generate signed cancel URL (3-hour expiration, tamper-proof)
            $cancelUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                'payment.cancel',
                now()->addHours(3),
                [
                    'order' => $order->id,
                    'token' => $cancellationToken,
                ]
            );

            $data = [
                'user' => $order->user,
                'order' => $order,
                'items' => [
                    [
                        'price' => $productPrice,
                        'name' => $productPrice->product->title.' - '.$productPrice->title,
                        'amount' => $productPrice->amount,
                        'currency' => $productPrice->currency,
                        'quantity' => $order->quantity ?? 1,
                    ],
                ],
                'success_url' => config('app.url').'/payment/success?order='.$order->id,
                'cancel_url' => $cancelUrl,
                'currency' => $productPrice->currency,
            ];

            // Add subscription to data if it exists
            if ($subscription) {
                $data['subscription'] = $subscription;
            }

            // Create checkout session
            $session = $gatewayService->createCheckoutSession($data);

            // Check if session creation was successful
            if (! isset($session['success']) || ! $session['success']) {
                throw new \Exception($session['message'] ?? 'Failed to create checkout session');
            }

            // Update order with gateway information
            $order->update([
                'gateway_order_id' => $session['id'],
                'gateway_data' => array_merge($order->gateway_data ?? [], [
                    'checkout_url' => $session['url'],
                    'checkout_session_id' => $session['id'],
                ]),
            ]);

            // Update subscription with gateway information if it exists
            if ($subscription) {
                $subscription->update([
                    'gateway_subscription_id' => $session['id'], // Will be updated to actual subscription ID in webhook
                    'gateway_status' => 'incomplete',
                    'gateway_data' => [
                        'checkout_session_id' => $session['id'],
                    ],
                ]);
            }

            return $session['url'];

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Checkout URL generation error', [
                'order_id' => $order->id,
                'gateway' => $gateway,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user/checkout/preview",
     *     summary="Get checkout preview for product/price",
     *     description="Returns product details, available prices, selected price breakdown, and available payment methods. Tax is calculated and collected by the payment gateway at checkout, so preview totals are pre-tax.",
     *     operationId="getCheckoutPreview",
     *     tags={"User Checkout"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         description="Product ID to preview",
     *         required=false,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="product_price_id",
     *         in="query",
     *         description="Product Price ID to preview",
     *         required=false,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="selected_price_id",
     *         in="query",
     *         description="Selected Product Price ID for detailed breakdown",
     *         required=false,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="quantity",
     *         in="query",
     *         description="Quantity for calculation (default: 1)",
     *         required=false,
     *
     *         @OA\Schema(type="integer", minimum=1, example=2)
     *     ),
     *
     *     @OA\Parameter(
     *         name="billing_country",
     *         in="query",
     *         description="Billing country (ISO code, e.g., US, EU)",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="US")
     *     ),
     *
     *     @OA\Parameter(
     *         name="billing_state",
     *         in="query",
     *         description="Billing state/province (ISO or short code, e.g., CA)",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="CA")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Checkout preview data",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="product", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Pro Plan"),
     *                 @OA\Property(property="type", type="string", example="subscription"),
     *                 @OA\Property(property="description", type="string", example="Access to all features"),
     *                 @OA\Property(property="image", type="string", nullable=true, example=null)
     *             ),
     *             @OA\Property(property="selected_price", type="object", nullable=true,
     *                 @OA\Property(property="id", type="integer", example=3),
     *                 @OA\Property(property="name", type="string", example="Monthly"),
     *                 @OA\Property(property="amount", type="number", format="float", example=9.99),
     *                 @OA\Property(property="currency", type="string", example="USD"),
     *                 @OA\Property(property="billing_period", type="string", example="monthly"),
     *                 @OA\Property(property="trial_days", type="integer", nullable=true, example=14),
     *                 @OA\Property(property="is_recurring", type="boolean", example=true),
     *                 @OA\Property(property="subtotal", type="number", format="float", example=19.98),
     *                 @OA\Property(property="final_price", type="number", format="float", example=19.98)
     *             ),
     *             @OA\Property(property="prices", type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="id", type="integer", example=3),
     *                     @OA\Property(property="name", type="string", example="Monthly"),
     *                     @OA\Property(property="amount", type="number", format="float", example=9.99),
     *                     @OA\Property(property="currency", type="string", example="USD"),
     *                     @OA\Property(property="billing_period", type="string", example="monthly"),
     *                     @OA\Property(property="trial_days", type="integer", nullable=true, example=14),
     *                     @OA\Property(property="is_recurring", type="boolean", example=true),
     *                     @OA\Property(property="subtotal", type="number", format="float", example=9.99),
     *                     @OA\Property(property="final_price", type="number", format="float", example=9.99)
     *                 )
     *             ),
     *             @OA\Property(property="quantity", type="integer", example=2),
     *             @OA\Property(property="payment_methods", type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="key", type="string", example="stripe"),
     *                     @OA\Property(property="name", type="string", example="Stripe"),
     *                     @OA\Property(property="icon", type="string", example="stripe"),
     *                     @OA\Property(property="description", type="string", example="Credit card, Apple Pay"),
     *                     @OA\Property(property="supports_subscriptions", type="boolean", example=true)
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or missing product/product_price",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Product not found or inactive",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function preview(CheckoutPreviewRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $paymentService = app(\App\Services\Payment\PaymentService::class);

        $quantity = (int) ($validated['quantity'] ?? 1);

        // Load product and selected price context
        $product = null;
        $selectedPrice = null;

        if (! empty($validated['product_price_id'])) {
            $selectedPrice = \App\Models\ProductPrice::with('product')->active()->findOrFail($validated['product_price_id']);
            $product = $selectedPrice->product;
        } elseif (! empty($validated['product_id'])) {
            $product = \App\Models\Product::with(['prices' => function ($q) {
                $q->active();
            }])->active()->findOrFail($validated['product_id']);
            if (! empty($validated['selected_price_id'])) {
                $selectedPrice = $product->prices->firstWhere('id', $validated['selected_price_id']);
            } else {
                $selectedPrice = $product->prices->first();
            }
        } else {
            return response()->json([
                'error' => 'Either product_id or product_price_id is required.',
            ], 422);
        }

        if (! $product) {
            return response()->json([
                'error' => 'Product not found or inactive.',
            ], 404);
        }

        // Build price list. Tax is applied by the payment gateway at checkout,
        // so previews show pre-tax totals.
        $prices = $product->prices()->active()->get()->map(function (\App\Models\ProductPrice $price) use ($paymentService, $quantity) {
            $totals = $paymentService->calculateTotals($price, (int) $quantity);

            return [
                'id' => $price->id,
                'name' => $price->title,
                'amount' => (float) $price->amount,
                'currency' => $price->currency,
                'billing_period' => $price->billing_period,
                'trial_days' => $price->trial_days,
                'is_recurring' => $price->billing_period !== 'once',
                'subtotal' => $totals['subtotal'],
                'final_price' => $totals['total'],
            ];
        })->values()->toArray();

        // Selected price details (if present)
        $selectedPriceDetails = null;
        if ($selectedPrice) {
            $totals = $paymentService->calculateTotals($selectedPrice, (int) $quantity);
            $selectedPriceDetails = [
                'id' => $selectedPrice->id,
                'name' => $selectedPrice->title,
                'amount' => (float) $selectedPrice->amount,
                'currency' => $selectedPrice->currency,
                'billing_period' => $selectedPrice->billing_period,
                'trial_days' => $selectedPrice->trial_days,
                'is_recurring' => $selectedPrice->billing_period !== 'once',
                'subtotal' => $totals['subtotal'],
                'final_price' => $totals['total'],
            ];
        }

        // Available payment methods (use selected price if given to account for subscriptions)
        $gatewaysConfig = $selectedPrice
            ? \App\Services\Payment\PaymentGatewayRegistry::forProductPrice($selectedPrice)
            : \App\Services\Payment\PaymentGatewayRegistry::forProduct($product);

        $paymentMethods = [];
        foreach ($gatewaysConfig as $key => $config) {
            $paymentMethods[] = [
                'key' => $key,
                'name' => $config['name'] ?? ucfirst($key),
                'icon' => $config['icon'] ?? null,
                'description' => $config['description'] ?? null,
                'supports_subscriptions' => (bool) ($config['supports_subscriptions'] ?? false),
            ];
        }

        return response()->json([
            'product' => [
                'id' => $product->id,
                'name' => $product->title,
                'type' => $product->type,
                'description' => $product->description,
                'image' => $product->image ?? null,
            ],
            'selected_price' => $selectedPriceDetails,
            'prices' => $prices,
            'quantity' => $quantity,
            'payment_methods' => $paymentMethods,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/user/checkout/{order}/cancel",
     *     summary="Cancel a pending order",
     *     description="Cancel an order that hasn't been paid yet",
     *     operationId="cancelOrder",
     *     tags={"User Checkout"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Order cancelled successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Order cancelled successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Order cannot be cancelled",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function cancel(string $orderId): JsonResponse
    {
        $user = auth()->user();

        if (! $user) {
            return response()->json([
                'error' => 'User authentication required',
            ], 401);
        }

        // Find the order with strict ownership check
        $order = \App\Models\Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->first();

        if (! $order) {
            // Security: Log potential unauthorized access attempt
            \Illuminate\Support\Facades\Log::warning('Unauthorized order cancellation attempt', [
                'user_id' => $user->id,
                'attempted_order_id' => $orderId,
                'ip' => request()->ip(),
            ]);

            return response()->json([
                'error' => 'Order not found',
            ], 404);
        }

        // Idempotent: Return success if already cancelled
        if ($order->status === 'cancelled') {
            return response()->json([
                'message' => 'Order already cancelled',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                ],
            ]);
        }

        // Only cancel pending orders
        if ($order->status !== 'pending') {
            return response()->json([
                'error' => "Cannot cancel order with status: {$order->status}",
            ], 422);
        }

        // Update order status to cancelled
        $order->update([
            'status' => 'cancelled',
            'meta' => array_merge($order->meta ?? [], [
                'cancelled_at' => now()->toIso8601String(),
                'cancelled_by' => 'user_api',
                'cancelled_by_user_id' => $user->id,
                'cancelled_from_ip' => request()->ip(),
            ]),
        ]);

        // Invalidate cancellation token to prevent replay
        $order->invalidateCancellationToken();

        \Illuminate\Support\Facades\Log::info("Order {$order->order_number} cancelled via API", [
            'user_id' => $user->id,
            'order_id' => $order->id,
            'gateway' => $order->gateway,
            'ip' => request()->ip(),
        ]);

        return response()->json([
            'message' => 'Order cancelled successfully',
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
            ],
        ]);
    }
}
