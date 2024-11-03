<?php

declare(strict_types = 1);

namespace App\Services\PaymentGateway;

use App\Services\PaymentGatewayService;

class Visa implements PaymentGatewayService
{
    public function charge($customerEmail, $amount, $tax): array
    {
        $total = $amount + $tax;

        $invoiceId = mt_rand(8000, 9000);

        return [
            'id' => $invoiceId,
            'total' => $total,
            'status' => true
        ];
    }
}
