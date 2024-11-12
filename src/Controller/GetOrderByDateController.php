<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\OrderRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class GetOrderByDateController
{
    public function __construct(
        private OrderRepository $orderRepository
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $data = $request->getQueryParams();

        $results = $this->orderRepository->getOrderByDates($data['after'], $data['before']);

        $payload = json_encode($results);

        $response->getBody()->write($payload);

        return $response;

    }
}
