<?php

declare(strict_types=1);

namespace App\Services;

interface PaymentGatewayService
{
    public function charge($customerName, $amount, $tax): bool;
}
