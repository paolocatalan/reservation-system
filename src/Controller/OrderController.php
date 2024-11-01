<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\OrderRepository;
use App\RequestValidator\CreateOrderValidator;
use App\Services\InvoiceService;
use App\Services\ReservationService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

class OrderController
{
    public function __construct(
        private OrderRepository $orderRepository,
        private ReservationService $reservationService,
        private InvoiceService $invoiceService
    ) { }

    public function index(Request $request, Response $response): Response
    {
        $data = $this->orderRepository->getAll();

        $body = json_encode($data);

        $response->getBody()->write($body);

        return $response;
    }

    public function show(Request $request, Response $response, string $id): Response
    {
        $data = $this->orderRepository->getById((int) $id); 

        if ($data === false) {
            throw new HttpNotFoundException($request, message: 'order not found');
        }

        $body = json_encode($data);

        $response->getBody()->write($body);

        return $response;
    }

    public function create(Request $request, Response $response): Response
    {
        $validator = new CreateOrderValidator($request->getParsedBody());
 
        if (!$validator->validate()) {
            $body = json_encode($validator->errorBag());
            $response->getBody()->write($body);

            return $response->withStatus(422);
        } 

        $invoiceId = $this->invoiceService->process($request->getParsedBody());

        $orderId = $this->reservationService->add($request->getParsedBody());

        $body = json_encode([
            'message' => 'Your reservation was successfully created.',
            'invoice_id' => $invoiceId,
            'order_id' => $orderId
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(201);
    }

    public function findOrder(Request $request, Response $response): Response
    {
        $id = 14;

        $data = $this->orderRepository->find($id);

        $body = json_encode($data);

        $response->getBody()->write($body);

        return $response;
    }

}
