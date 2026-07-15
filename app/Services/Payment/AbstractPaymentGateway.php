<?php

namespace App\Services\Payment;

abstract class AbstractPaymentGateway implements PaymentGatewayInterface
{
    // This abstract class can be extended for common gateway functionality
    // Currently, each gateway implements the interface directly with no shared methods
}
