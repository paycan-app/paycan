<?php

namespace App\Services\Fulfillment\Contracts;

use App\Contracts\SettingProvider;
use App\Models\Fulfillment;
use App\Models\Order;

interface FulfillmentProvider extends SettingProvider
{
    /**
     * Get the fulfillment type: carrier, license, download
     */
    public function getFulfillmentType(): string;

    /**
     * Process fulfillment for an order
     */
    public function process(Order $order, Fulfillment $fulfillment): bool;
}
