<?php

namespace App\Services\Payment;

use App\Services\Payment\Gateways\PayPalSettingsProvider;
use App\Services\Payment\Gateways\StripeSettingsProvider;

class PaymentGatewayRegistry
{
    /**
     * Registry of all available payment gateways
     */
    private static array $gateways = [
        'stripe' => [
            'name' => 'Stripe',
            'class' => StripeGateway::class,
            'settings_provider' => StripeSettingsProvider::class,
            'supports_subscriptions' => true,
            'webhook_path' => '/webhooks/stripe',
            'icon' => 'stripe',
            'description' => 'Accept payments with Stripe',
            'supported_currencies' => ['USD', 'EUR', 'GBP', 'CAD', 'AUD'],
            'supported_product_types' => ['digital', 'physical', 'subscription'],
            // 'settings_keys' => [
            //     'stripe_enabled',
            //     'stripe_secret_key',
            //     'stripe_publishable_key',
            //     'stripe_webhook_secret',
            // ],
        ],
        'paypal' => [
            'name' => 'PayPal',
            'class' => PayPalGateway::class,
            'settings_provider' => PayPalSettingsProvider::class,
            'supports_subscriptions' => true,
            'webhook_path' => '/webhooks/paypal',
            'icon' => 'paypal',
            'description' => 'Accept payments with PayPal',
            'supported_currencies' => ['USD', 'EUR', 'GBP', 'CAD', 'AUD'],
            'supported_product_types' => ['digital', 'physical', 'subscription'],
            // 'settings_keys' => [
            //     'paypal_enabled',
            //     'paypal_client_id',
            //     'paypal_client_secret',
            //     'paypal_webhook_id',
            // ],
        ],
    ];

    /**
     * Get all registered gateways
     */
    public static function all(): array
    {
        return self::$gateways;
    }

    /**
     * Get gateway configuration by name
     */
    public static function get(string $gateway): ?array
    {
        return self::$gateways[$gateway] ?? null;
    }

    /**
     * Check if a gateway exists
     */
    public static function exists(string $gateway): bool
    {
        return isset(self::$gateways[$gateway]);
    }

    /**
     * Get all gateway names
     */
    public static function names(): array
    {
        return array_keys(self::$gateways);
    }

    /**
     * Get enabled gateways based on settings
     */
    public static function enabled(): array
    {
        $enabled = [];

        foreach (self::$gateways as $key => $config) {
            $settingsProvider = app($config['settings_provider']);

            if (method_exists($settingsProvider, 'isEnabled') && $settingsProvider->isEnabled()) {
                $enabled[$key] = $config;
            }
        }

        return $enabled;
    }

    /**
     * Get gateways that support subscriptions
     */
    public static function withSubscriptionSupport(): array
    {
        return array_filter(self::$gateways, function ($config) {
            return $config['supports_subscriptions'] ?? false;
        });
    }

    /**
     * Get gateways for a specific product
     */
    public static function forProduct($product): array
    {
        $productType = $product->type ?? 'digital';
        $currency = $product->currency ?? 'USD';

        return array_filter(self::enabled(), function ($config) use ($productType, $currency) {
            $supportedTypes = $config['supported_product_types'] ?? [];
            $supportedCurrencies = $config['supported_currencies'] ?? [];

            return in_array($productType, $supportedTypes) &&
                   in_array($currency, $supportedCurrencies);
        });
    }

    /**
     * Get gateways for a specific product price
     */
    public static function forProductPrice($productPrice): array
    {
        $product = $productPrice->product;
        $isSubscription = $productPrice->is_recurring ?? false;

        $availableGateways = self::forProduct($product);

        if ($isSubscription) {
            $availableGateways = array_filter($availableGateways, function ($config) {
                return $config['supports_subscriptions'] ?? false;
            });
        }

        return $availableGateways;
    }

    /**
     * Validate if a gateway can be used for a specific product/price
     */
    public static function canUseForProduct(string $gateway, $product): bool
    {
        if (! self::exists($gateway)) {
            return false;
        }

        $availableGateways = self::forProduct($product);

        return isset($availableGateways[$gateway]);
    }

    /**
     * Validate if a gateway can be used for a specific product price
     */
    public static function canUseForProductPrice(string $gateway, $productPrice): bool
    {
        if (! self::exists($gateway)) {
            return false;
        }

        $availableGateways = self::forProductPrice($productPrice);

        return isset($availableGateways[$gateway]);
    }

    /**
     * Get gateway class name
     */
    public static function getClass(string $gateway): ?string
    {
        $config = self::get($gateway);

        return $config['class'] ?? null;
    }

    /**
     * Get gateway settings provider class
     */
    public static function getSettingsProvider(string $gateway): ?string
    {
        $config = self::get($gateway);

        return $config['settings_provider'] ?? null;
    }

    /**
     * Register a new gateway
     */
    public static function register(string $name, array $config): void
    {
        self::$gateways[$name] = $config;
    }

    /**
     * Unregister a gateway
     */
    public static function unregister(string $name): void
    {
        unset(self::$gateways[$name]);
    }

    /**
     * Get gateway options formatted for product
     */
    public static function getOptionsForProduct($product): array
    {
        $gateways = self::forProduct($product);

        return array_map(function ($config, $key) {
            return [
                'key' => $key,
                'name' => $config['name'],
                'icon' => $config['icon'],
                'description' => $config['description'],
                'supports_subscriptions' => $config['supports_subscriptions'],
            ];
        }, $gateways, array_keys($gateways));
    }

    /**
     * Get gateway options formatted for product price
     */
    public static function getOptionsForProductPrice($productPrice): array
    {
        $gateways = self::forProductPrice($productPrice);

        return array_map(function ($config, $key) {
            return [
                'key' => $key,
                'name' => $config['name'],
                'icon' => $config['icon'],
                'description' => $config['description'],
                'supports_subscriptions' => $config['supports_subscriptions'],
            ];
        }, $gateways, array_keys($gateways));
    }

    /**
     * Validate if a gateway can be used for checkout with a product price
     */
    public static function validateForCheckout(string $gateway, $productPrice): array
    {
        // Check if gateway exists
        if (! self::exists($gateway)) {
            return [
                'valid' => false,
                'error' => 'The selected payment gateway does not exist.',
                'code' => 'gateway_not_found',
            ];
        }

        // Check if gateway is enabled
        $config = self::get($gateway);
        $settingsProvider = app($config['settings_provider']);

        if (! method_exists($settingsProvider, 'isEnabled') || ! $settingsProvider->isEnabled()) {
            return [
                'valid' => false,
                'error' => 'The selected payment gateway is not enabled.',
                'code' => 'gateway_disabled',
            ];
        }

        // Check if gateway supports this product price
        if (! self::canUseForProductPrice($gateway, $productPrice)) {
            $product = $productPrice->product;
            $isSubscription = $productPrice->billing_period !== 'once';

            // Determine specific reason
            if ($isSubscription && ! ($config['supports_subscriptions'] ?? false)) {
                return [
                    'valid' => false,
                    'error' => 'The selected payment gateway does not support subscription payments.',
                    'code' => 'subscriptions_not_supported',
                ];
            }

            if (! in_array($productPrice->currency, $config['supported_currencies'] ?? [])) {
                return [
                    'valid' => false,
                    'error' => "The selected payment gateway does not support {$productPrice->currency} currency.",
                    'code' => 'currency_not_supported',
                ];
            }

            if (! in_array($product->type, $config['supported_product_types'] ?? [])) {
                return [
                    'valid' => false,
                    'error' => "The selected payment gateway does not support {$product->type} products.",
                    'code' => 'product_type_not_supported',
                ];
            }

            return [
                'valid' => false,
                'error' => 'The selected payment gateway is not available for this product.',
                'code' => 'gateway_not_available',
            ];
        }

        return [
            'valid' => true,
            'error' => null,
            'code' => null,
        ];
    }
}
