<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\OrderRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class FetchOrderController
{
    public function __construct(
        private OrderRepository $orderRepository
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $data = $this->orderRepository->getAll();

        $body = json_encode($data);

        $response->getBody()->write($body);

        return $response;

    }
}
