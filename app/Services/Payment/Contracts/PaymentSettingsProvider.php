<?php

namespace App\Services\Payment\Contracts;

use App\Contracts\SettingProvider;

interface PaymentSettingsProvider extends SettingProvider
{
    /**
     * Check if this payment gateway supports subscriptions
     */
    public function supportsSubscriptions(): bool;

    /**
     * Get the gateway class name
     */
    public function getGatewayClass(): string;
}
