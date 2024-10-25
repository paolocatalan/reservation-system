<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\OrderRepository;
use App\Services\ReservationService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

class OrderController
{
    public function __construct(
        private OrderRepository $orderRepository,
        private ReservationService $reservationService
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

    public function create(Request $request, Response $response): Response {
        $order = [
            'room_type' => "Family Room",
            'checkin_date' => "2024-11-27 12:00:00",
            'checkout_date' => "2024-11-28 12:00:00",
            'table_setting' => "Five Course Table Setting",
            'restaurant_date' => "2024-11-27 18:00:00",
            'name' => "Lex",
            'email' => "lex@aipodcast.com"
        ];

        $data = $this->reservationService->processOrder($order);

        $body = json_encode($data);

        $response->getBody()->write($body);

        return $response;
    }

}
