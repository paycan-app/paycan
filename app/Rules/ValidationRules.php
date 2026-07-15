<?php

namespace App\Rules;

use App\Services\Payment\PaymentGatewayRegistry;

class ValidationRules
{
    public static function productRules(): array
    {
        return [
            'id' => 'nullable|string|max:50|unique:products,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'type' => 'required|string|in:physical,digital,service,subscription',
            'image' => 'nullable|string|max:255',
            'active' => 'boolean',
            'metadata' => 'nullable|array',
        ];
    }

    public static function productPriceRules(): array
    {
        return [
            'id' => 'nullable|string|max:50|unique:product_prices,id',
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'currency' => 'string|max:3|in:USD,EUR,GBP,CAD,AUD,JPY',
            'billing_period' => 'string|in:once,daily,weekly,monthly,yearly',
            'trial_days' => 'integer|min:0|max:365',
            'gateway_data' => 'nullable|array',
            'is_active' => 'boolean',
        ];
    }

    public static function orderRules(): array
    {
        $supportedGateways = implode(',', PaymentGatewayRegistry::names());

        return [
            'user_id' => 'required|exists:users,id',
            'product_price_id' => 'required|exists:product_prices,id',
            'product_id' => 'required|exists:products,id',
            'order_number' => 'required|string|max:255|unique:orders,order_number',
            'status' => 'required|string|in:pending,processing,completed,failed,cancelled,refunded',
            'total' => 'required|numeric|min:0',
            'currency' => 'string|max:3|in:USD,EUR,GBP,CAD,AUD,JPY',
            'tax' => 'numeric|min:0',
            'billing_email' => 'required|email|max:255',
            'billing_name' => 'required|string|max:255',
            'billing_address' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:255',
            'billing_state' => 'nullable|string|max:255',
            'billing_zipcode' => 'nullable|string|max:20',
            'billing_country' => 'nullable|string|max:2',
            'gateway' => "required|string|in:{$supportedGateways}",
            'gateway_order_id' => 'nullable|string|max:255',
            'gateway_data' => 'nullable|array',
            'notes' => 'nullable|string',
        ];
    }

    public static function subscriptionRules(): array
    {
        $supportedGateways = implode(',', PaymentGatewayRegistry::names());

        return [
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'product_price_id' => 'required|exists:product_prices,id',
            'order_id' => 'required|exists:orders,id',
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:active,trialing,past_due,canceled,incomplete,incomplete_expired',
            'gateway' => "required|string|in:{$supportedGateways}",
            'gateway_subscription_id' => 'nullable|string|max:255',
            'gateway_status' => 'nullable|string|max:255',
            'gateway_data' => 'nullable|array',
            'trial_ends_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
            'next_billing_date' => 'nullable|date',
            'canceled_at' => 'nullable|date',
        ];
    }

    public static function transactionRules(): array
    {
        $supportedGateways = implode(',', PaymentGatewayRegistry::names());

        return [
            'user_id' => 'required|exists:users,id',
            'order_id' => 'nullable|exists:orders,id',
            'subscription_id' => 'nullable|exists:subscriptions,id',
            'type' => 'required|string|in:charge,refund,subscription_create,subscription_renew,subscription_update,subscription_cancel',
            'status' => 'required|string|in:pending,completed,failed,refunded',
            'gateway' => "required|string|in:{$supportedGateways}",
            'amount' => 'required|numeric|min:0',
            'currency' => 'string|max:3|in:USD,EUR,GBP,CAD,AUD,JPY',
            'gateway_transaction_id' => 'required|string|max:255',
            'gateway_data' => 'nullable|array',
        ];
    }

    public static function fulfillmentRules(): array
    {
        return [
            'id' => 'nullable|string|max:50|unique:fulfillments,id',
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|string|in:pending,processing,completed,failed',
            'type' => 'required|string|in:digital,physical,service,subscription_access',
            'tracking_number' => 'nullable|string|max:255',
            'carrier' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
            'fulfilled_at' => 'nullable|date',
        ];
    }

    public static function socialConnectionRules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'provider' => 'required|string|in:google,facebook,github,twitter,linkedin,apple',
            'provider_id' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'avatar' => 'nullable|url|max:255',
            'access_token' => 'required|string',
            'refresh_token' => 'nullable|string',
            'token_expires_at' => 'nullable|date',
            'metadata' => 'nullable|array',
            'connection_type' => 'string|in:login,connect',
        ];
    }

    public static function userRules(): array
    {
        return [
            'id' => 'nullable|string|max:50|unique:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'gateway_data' => 'nullable|array',
        ];
    }

    public static function userUpdateRules($userId = null): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$userId,
            'password' => 'nullable|string|min:8',
            'gateway_data' => 'nullable|array',
        ];
    }

    public static function productUpdateRules($productId = null): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,'.$productId,
            'description' => 'nullable|string',
            'type' => 'required|string|in:physical,digital,service,subscription',
            'image' => 'nullable|string|max:255',
            'active' => 'boolean',
            'metadata' => 'nullable|array',
        ];
    }

    public static function productPriceUpdateRules($productPriceId = null): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'currency' => 'string|max:3|in:USD,EUR,GBP,CAD,AUD,JPY',
            'billing_period' => 'string|in:once,daily,weekly,monthly,yearly',
            'trial_days' => 'integer|min:0|max:365',
            'gateway_data' => 'nullable|array',
            'is_active' => 'boolean',
        ];
    }

    public static function checkoutRules(): array
    {
        return [
            'product_price_id' => 'required|exists:product_prices,id',
            'gateway' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (! PaymentGatewayRegistry::exists($value)) {
                        $fail('The selected payment gateway is invalid.');
                    }
                },
            ],
            'success_url' => 'nullable|url',
            'cancel_url' => 'nullable|url',
            'customer_email' => 'nullable|email',
            'metadata' => 'nullable|array',
        ];
    }
}
