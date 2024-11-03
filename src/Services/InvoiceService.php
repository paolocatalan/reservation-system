<?php

declare(strict_types = 1);

namespace App\Services;

class InvoiceService
{
    public function __construct(
        protected SalesTaxService $salesTaxService,
        protected PaymentGatewayService $paymentGatewayService,
        protected EmailService $emailService
    ) {}

    public function process(array $order): array 
    {
        $tax = $this->salesTaxService->calculate((float) $order['amount']);

        $invoiceId = $this->paymentGatewayService->charge($order['email'], $order['amount'], $tax);

        $this->emailService->send($order, 'receipt');

        return $invoiceId;
    }
}

