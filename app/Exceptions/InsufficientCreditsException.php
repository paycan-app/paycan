<?php

namespace App\Exceptions;

use Exception;

class InsufficientCreditsException extends Exception
{
    public function __construct(
        public string $walletType,
        public float $currentBalance,
        public float $requiredAmount,
        string $message = 'Insufficient credits in wallet.'
    ) {
        parent::__construct($message);
    }
}
