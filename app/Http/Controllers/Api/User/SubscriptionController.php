<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Requests\Api\User\SubscriptionChangeRequest;
use App\Http\Requests\Api\User\SubscriptionStoreRequest;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @OA\Tag(
 *     name="User Subscriptions",
 *     description="User subscription management endpoints"
 * )
 */
class SubscriptionController extends UserApiController
{
    /**
     * @OA\Get(
     *     path="/api/user/subscriptions",
     *     summary="Get user subscriptions",
     *     description="Retrieve paginated and filterable list of current user's subscriptions with optional related data",
     *     operationId="getUserSubscriptions",
     *     tags={"User Subscriptions"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *
     *         @OA\Schema(type="integer", minimum=1, default=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page (max 100)",
     *         required=false,
     *
     *         @OA\Schema(type="integer", minimum=1, maximum=100, default=15)
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[status]",
     *         in="query",
     *         description="Filter by subscription status",
     *         required=false,
     *
     *         @OA\Schema(type="string", enum={"active", "trialing", "past_due", "canceled", "incomplete", "incomplete_expired"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[gateway]",
     *         in="query",
     *         description="Filter by payment gateway",
     *         required=false,
     *
     *         @OA\Schema(type="string", enum={"stripe", "paypal"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[created_after]",
     *         in="query",
     *         description="Filter subscriptions created after date",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="date", example="2024-01-01")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[created_before]",
     *         in="query",
     *         description="Filter subscriptions created before date",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="date", example="2024-12-31")
     *     ),
     *
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Include related data (comma-separated)",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="product,productPrice,order,transactions")
     *     ),
     *
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort subscriptions (prefix with - for descending)",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="-created_at", enum={"created_at", "-created_at", "status", "-status", "next_billing_date", "-next_billing_date"})
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Subscriptions retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(ref="#/components/schemas/Subscription")
     *             ),
     *
     *             @OA\Property(property="links", ref="#/components/schemas/PaginationLinks"),
     *             @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
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
    public function index(): JsonResponse
    {
        $subscriptions = QueryBuilder::for(Subscription::where('user_id', auth()->id()))
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('gateway'),
                AllowedFilter::scope('created_after'),
                AllowedFilter::scope('created_before'),
            ])
            ->allowedIncludes([
                'product',
                'productPrice',
                'productPrice.product',
                'order',
                'transactions',
            ])
            ->allowedSorts([
                AllowedSort::field('created_at'),
                AllowedSort::field('status'),
                AllowedSort::field('next_billing_date'),
            ])
            ->defaultSort('-created_at')
            ->paginate(request('per_page', 15))
            ->appends(request()->query());

        return response()->json($subscriptions);
    }

    /**
     * @OA\Post(
     *     path="/api/user/subscriptions",
     *     summary="Create subscription",
     *     description="Create a new subscription for a product price",
     *     operationId="createUserSubscription",
     *     tags={"User Subscriptions"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"product_price_id"},
     *
     *             @OA\Property(property="product_price_id", type="string", description="Product price ID to subscribe to", example="price_123"),
     *             @OA\Property(property="gateway", type="string", enum={"stripe", "paypal"}, description="Payment gateway to use"),
     *             @OA\Property(property="payment_method_id", type="string", description="Payment method ID from gateway")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Subscription created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", ref="#/components/schemas/Subscription")
     *         )
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
     *         description="Validation error",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function store(SubscriptionStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $user = auth()->user();
            $productPrice = \App\Models\ProductPrice::findOrFail($validated['product_price_id']);

            // Validate that this is actually a subscription product
            if ($productPrice->billing_period === 'once') {
                return response()->json([
                    'error' => 'This product is not a subscription',
                    'message' => 'The selected product is a one-time purchase, not a subscription.',
                ], 422);
            }

            // Check for existing active subscription
            $existingSubscription = \App\Models\Subscription::where('user_id', $user->id)
                ->where('product_price_id', $productPrice->id)
                ->whereIn('status', ['active', 'trialing'])
                ->first();

            if ($existingSubscription) {
                return response()->json([
                    'error' => 'Subscription already exists',
                    'message' => 'You already have an active subscription for this product.',
                    'subscription' => $existingSubscription,
                ], 422);
            }

            // Use the PaymentService to create the subscription
            $paymentService = app(\App\Services\Payment\PaymentService::class);

            $options = [
                'gateway' => $validated['gateway'],
                'payment_method_id' => $validated['payment_method_id'] ?? null,
            ];

            $result = $paymentService->createSubscription($user, $productPrice, $options);

            if ($result['success']) {
                $subscription = \App\Models\Subscription::where('user_id', $user->id)
                    ->where('product_price_id', $productPrice->id)
                    ->latest()
                    ->first();

                return response()->json([
                    'message' => 'Subscription created successfully',
                    'subscription' => $subscription,
                    'checkout_url' => $result['url'] ?? null,
                ], 201);
            } else {
                return response()->json([
                    'error' => 'Failed to create subscription',
                    'message' => $result['message'] ?? $result['error'] ?? 'An error occurred while creating the subscription.',
                ], 422);
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Subscription creation error', [
                'user_id' => auth()->id(),
                'product_price_id' => $validated['product_price_id'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Subscription creation failed',
                'message' => 'An unexpected error occurred. Please try again or contact support.',
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user/subscriptions/{subscription}",
     *     summary="Get subscription details",
     *     description="Retrieve a specific subscription by ID for the current user",
     *     operationId="getUserSubscription",
     *     tags={"User Subscriptions"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="subscription",
     *         in="path",
     *         description="Subscription ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Include related data (comma-separated)",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="product,productPrice,order,transactions")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Subscription retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", ref="#/components/schemas/Subscription")
     *         )
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
     *         response=403,
     *         description="Access denied - not your subscription",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Subscription not found",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function show(Subscription $subscription): JsonResponse
    {
        if ($subscription->user_id !== auth()->id()) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $subscription = QueryBuilder::for(Subscription::where('id', $subscription->id))
            ->allowedIncludes([
                'product',
                'productPrice',
                'productPrice.product',
                'order',
                'transactions',
            ])
            ->first();

        return response()->json(['data' => $subscription]);
    }

    /**
     * @OA\Post(
     *     path="/api/user/subscriptions/{subscription}/cancel",
     *     summary="Cancel subscription",
     *     description="Cancel an active subscription at the end of the billing period",
     *     operationId="cancelUserSubscription",
     *     tags={"User Subscriptions"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="subscription",
     *         in="path",
     *         description="Subscription ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=false,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="immediately", type="boolean", description="Cancel immediately instead of at period end", example=false)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Subscription canceled successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", ref="#/components/schemas/Subscription"),
     *             @OA\Property(property="message", type="string", example="Subscription canceled successfully")
     *         )
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
     *         response=403,
     *         description="Access denied - not your subscription",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Subscription cannot be canceled",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function cancel(Request $request, Subscription $subscription): JsonResponse
    {
        if ($subscription->user_id !== auth()->id()) {
            return response()->json(['error' => 'Not found'], 404);
        }

        if ($subscription->status === 'canceled') {
            return response()->json(['error' => 'Subscription is already canceled'], 422);
        }

        $immediately = $request->boolean('immediately', false);

        // Subscriptions that never became active at the gateway are cancelled locally
        if ($subscription->status === 'incomplete' || ! $subscription->gateway_subscription_id) {
            $subscription->update([
                'status' => 'canceled',
                'canceled_at' => now(),
                'ends_at' => now(),
            ]);

            return response()->json([
                'subscription' => $subscription->fresh(),
                'message' => 'Subscription canceled successfully',
            ]);
        }

        try {
            $paymentService = app(\App\Services\Payment\PaymentService::class);
            $result = $paymentService->cancelSubscription($subscription, $immediately);

            if (! ($result['success'] ?? false)) {
                \Illuminate\Support\Facades\Log::error('Gateway subscription cancellation failed', [
                    'subscription_id' => $subscription->id,
                    'gateway' => $subscription->gateway,
                    'error' => $result['error'] ?? 'Unknown error',
                ]);

                return response()->json([
                    'error' => 'Failed to cancel the subscription with the payment provider. Please try again or contact support.',
                ], 502);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gateway subscription cancellation error', [
                'subscription_id' => $subscription->id,
                'gateway' => $subscription->gateway,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Failed to cancel the subscription with the payment provider. Please try again or contact support.',
            ], 502);
        }

        return response()->json([
            'subscription' => $subscription->fresh(),
            'message' => $immediately
                ? 'Subscription canceled successfully'
                : 'Subscription canceled; it remains active until the end of the current billing period',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/user/subscriptions/{subscription}/resume",
     *     summary="Resume subscription",
     *     description="Resume a canceled subscription before the end date",
     *     operationId="resumeUserSubscription",
     *     tags={"User Subscriptions"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="subscription",
     *         in="path",
     *         description="Subscription ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Subscription resumed successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", ref="#/components/schemas/Subscription"),
     *             @OA\Property(property="message", type="string", example="Subscription resumed successfully")
     *         )
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
     *         response=403,
     *         description="Access denied - not your subscription",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Subscription cannot be resumed",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function resume(Subscription $subscription): JsonResponse
    {
        if ($subscription->user_id !== auth()->id()) {
            return response()->json(['error' => 'Not found'], 404);
        }

        if (! in_array($subscription->status, ['canceled', 'paused'])) {
            return response()->json(['error' => 'Only canceled subscriptions can be resumed'], 422);
        }

        if ($subscription->ends_at && $subscription->ends_at->isPast()) {
            return response()->json(['error' => 'Subscription has already ended and cannot be resumed'], 422);
        }

        if (! $subscription->gateway_subscription_id) {
            return response()->json(['error' => 'Subscription cannot be resumed. Please create a new subscription.'], 422);
        }

        try {
            $paymentService = app(\App\Services\Payment\PaymentService::class);
            $result = $paymentService->resumeSubscription($subscription);

            if (! ($result['success'] ?? false)) {
                \Illuminate\Support\Facades\Log::error('Gateway subscription resume failed', [
                    'subscription_id' => $subscription->id,
                    'gateway' => $subscription->gateway,
                    'error' => $result['error'] ?? 'Unknown error',
                ]);

                return response()->json([
                    'error' => $result['error'] ?? 'Failed to resume the subscription with the payment provider.',
                ], 502);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gateway subscription resume error', [
                'subscription_id' => $subscription->id,
                'gateway' => $subscription->gateway,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Failed to resume the subscription with the payment provider. Please try again or contact support.',
            ], 502);
        }

        return response()->json([
            'subscription' => $subscription->fresh(),
            'message' => 'Subscription resumed successfully',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/user/subscriptions/{subscription}/change",
     *     summary="Change subscription plan",
     *     description="Change the subscription to a different product price",
     *     operationId="changeUserSubscription",
     *     tags={"User Subscriptions"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="subscription",
     *         in="path",
     *         description="Subscription ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"product_price_id"},
     *
     *             @OA\Property(property="product_price_id", type="string", description="New product price ID", example="price_456"),
     *             @OA\Property(property="prorate", type="boolean", description="Whether to prorate the change", example=true)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Subscription changed successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", ref="#/components/schemas/Subscription"),
     *             @OA\Property(property="message", type="string", example="Subscription changed successfully")
     *         )
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
     *         response=403,
     *         description="Access denied - not your subscription",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or subscription cannot be changed",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function change(SubscriptionChangeRequest $request, Subscription $subscription): JsonResponse
    {
        if ($subscription->user_id !== auth()->id()) {
            return response()->json(['error' => 'Not found'], 404);
        }

        if ($subscription->status === 'canceled') {
            return response()->json(['error' => 'Cannot change a canceled subscription'], 422);
        }

        $validated = $request->validated();

        if ($subscription->product_price_id === $validated['product_price_id']) {
            return response()->json(['error' => 'Subscription is already on this plan'], 422);
        }

        $newPrice = \App\Models\ProductPrice::with('product')->find($validated['product_price_id']);

        if (! $newPrice || ! $newPrice->is_active || ! $newPrice->product->is_active) {
            return response()->json(['error' => 'The selected plan is not available'], 422);
        }

        if ($newPrice->billing_period === 'once') {
            return response()->json(['error' => 'The selected plan is not a subscription'], 422);
        }

        if (! $subscription->gateway_subscription_id || $subscription->status === 'incomplete') {
            return response()->json(['error' => 'Subscription is not active at the payment provider yet'], 422);
        }

        try {
            $paymentService = app(\App\Services\Payment\PaymentService::class);
            $result = $paymentService->changeSubscriptionPlan($subscription, $newPrice);

            if (! ($result['success'] ?? false)) {
                \Illuminate\Support\Facades\Log::error('Gateway subscription plan change failed', [
                    'subscription_id' => $subscription->id,
                    'gateway' => $subscription->gateway,
                    'error' => $result['error'] ?? 'Unknown error',
                ]);

                return response()->json([
                    'error' => 'Failed to change the subscription plan with the payment provider.',
                ], 502);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gateway subscription plan change error', [
                'subscription_id' => $subscription->id,
                'gateway' => $subscription->gateway,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Failed to change the subscription plan with the payment provider. Please try again or contact support.',
            ], 502);
        }

        $response = [
            'subscription' => $subscription->fresh(['productPrice', 'product']),
            'message' => 'Subscription changed successfully',
        ];

        // PayPal plan changes require the customer to approve the revision
        if (! empty($result['approval_url'])) {
            $response['approval_url'] = $result['approval_url'];
            $response['message'] = 'Plan change requires approval. Redirect the customer to the approval URL.';
        }

        return response()->json($response);
    }
}
