<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\OrderRepository;
use App\Traits\HttpResponses;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class GetInvoiceIdController
{
    use HttpResponses;

    public function __construct(
        private OrderRepository $orderRepository
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $data = (array) $request->getParsedBody();

        if (filter_var($data['invoice_id'], FILTER_VALIDATE_INT) === false) {
            return $this->error('Invalid Invoice ID', null, 244);        
        }

        $invoice = $this->orderRepository->getInvoiceId((int) $data['invoice_id']);

        if (empty($invoice)) {
           return $this->success('No results found.', null, 200);
        }

        $payload = json_encode($invoice);

        $response->getBody()->write($payload);

        return $response;
    }
}
