<?php

namespace App\Services\Payment;

class PaymentGatewayFactory
{
    /**
     * Create a new payment gateway instance
     */
    public static function create(string $gateway): PaymentGatewayInterface
    {
        if (! PaymentGatewayRegistry::exists($gateway)) {
            throw new \InvalidArgumentException("Unsupported payment gateway: {$gateway}");
        }

        $gatewayClass = PaymentGatewayRegistry::getClass($gateway);

        if (! $gatewayClass) {
            throw new \InvalidArgumentException("Gateway class not found for: {$gateway}");
        }

        return app($gatewayClass);
    }

    /**
     * Get all supported gateway names
     */
    public static function getSupportedGateways(): array
    {
        return PaymentGatewayRegistry::names();
    }

    /**
     * Check if a gateway is supported
     */
    public static function isSupported(string $gateway): bool
    {
        return PaymentGatewayRegistry::exists($gateway);
    }
}
