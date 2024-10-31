<?php

declare(strict_types = 1);

namespace App\Services;

use App\Services\PaymentGateway\Visa;

class InvoiceService
{
  public function __construct(
    protected SalesTaxService $salesTaxService,
    protected PaymentGatewayService $paymentGatewayService,
    protected EmailService $emailService
  ) { }

  public function process(array $order): bool
  {
    $tax = $this->salesTaxService->calculate((float) $order['amount']);

    $paymentType = new Visa($order['name'], $order['amount'], $tax);
    if (!$this->paymentGatewayService->charge($paymentType)) {
      return false;
    }

    $this->emailService->send($order, 'receipt');

    return true;
  }
}

