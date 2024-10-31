<?php

declare(strict_types = 1);

namespace App\Services;

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

        if (!$this->paymentGatewayService->charge($order['name'], $order['amount'], $tax)) {
            return false;
        }

        $this->emailService->send($order, 'receipt');

        return true;
    }
}

