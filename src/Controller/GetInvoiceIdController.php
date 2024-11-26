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

        $results = $this->orderRepository->getInvoiceId((int) $data['invoice_id']);

        if (empty($results)) {
           return $this->success('No results found.', null, 200);
        }

        $payload = json_encode($results);

        $response->getBody()->write($payload);

        return $response;
    }
}
