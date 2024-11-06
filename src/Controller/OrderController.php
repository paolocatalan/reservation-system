<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\OrderRepository;
use App\Repositories\RestaurantRepository;
use App\Repositories\RoomRepository;
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
        private InvoiceService $invoiceService,
        private ReservationService $reservationService,
        private RoomRepository $roomRepository,
        private RestaurantRepository $restaurantRepository,
        private CreateOrderValidator $validator
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

    public function store(Request $request, Response $response): Response
    {
        $validated = $this->validator->validate($request->getParsedBody());
 
        if (!$validated) {
            $body = json_encode($this->validator->errorBag());
            $response->getBody()->write($body);

            return $response->withStatus(422);
        } 

        $invoice = $this->invoiceService->process($validated);

        // $this->bus->dispatch(new AddReservation($validator->validate()));
        $orderId = $this->reservationService->add($validated, $invoice['id']);

        $body = json_encode([
            'message' => 'Your reservation was successfully created.',
            'invoice_id' => $invoice['id'],
            'order_id' => $orderId
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(201);
    }

    public function findOrder(Request $request, Response $response): Response
    {
        // $id = 2;

        // $data = $this->restaurantRepository->getByOrderId($id);
        // $data = $this->roomRepository->getAvailability('Villa', '2024-11-09 12:00:00');
        $data = $this->restaurantRepository->getAvailability('Informal Table Setting', '2024-11-08 18:00:00');

        $body = json_encode($data);

        $response->getBody()->write($body);

        return $response;
    }

}
