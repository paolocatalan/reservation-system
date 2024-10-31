<?php

declare(strict_types = 1);

namespace App\Services\PaymentGateway;

use App\Services\PaymentGatewayService;

class Visa implements PaymentGatewayService
{
  public function __construct(array $customer, array $invoice, float $tax) {}

  public function charge(): bool
  {
     return true;
  }
}
