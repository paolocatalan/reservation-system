<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\OrderRepository;
use App\Services\ReservationService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;
use Valitron\Validator;

class OrderController
{
    public function __construct(
        private OrderRepository $orderRepository,
        private ReservationService $reservationService,
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
        $requestData = $request->getParsedBody();

        $validator = new Validator($requestData);

        $validator->rule('required', ['room_type', 'checkin_date', 'checkout_date', 'name', 'email']);

        if (!$validator->validate()) {
            $response->getBody()->write(json_encode($validator->errors()));
            return $response->withStatus(403);
        }
        
        $orderId = $this->reservationService->processOrder($requestData);

        $body = json_encode([
            'message' => 'Your order has been successfully placed.',
            'id' => $orderId
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
