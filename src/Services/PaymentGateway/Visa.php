<?php

declare(strict_types = 1);

namespace App\Services\PaymentGateway;

use App\Services\PaymentGatewayService;

class Visa implements PaymentGatewayService
{
    public function charge($customerName, $amount, $tax): bool
    {
        $total = $amount + $tax;

        return true;
    }
}
