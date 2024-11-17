<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\OrderRepository;
use App\RequestValidator\StoreOrderValidator;
use App\Services\InvoiceService;
use App\Services\ReservationService;
use App\Traits\HttpResponses;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class OrderController
{
    use HttpResponses;

    public function __construct(
        private OrderRepository $orderRepository,
        private InvoiceService $invoiceService,
        private ReservationService $reservationService,
        private StoreOrderValidator $storeOrderValidator
    ) {}

    public function index(Request $request, Response $response): Response
    {
        $data = (array) $request->getQueryParams();

        $orders = $this->orderRepository->getAll((int) $data['offset'], (int) $data['limit']);
        $ordersCount = $this->orderRepository->getOrdersCount();
        $totalPages = round($ordersCount/$data['limit']);

        $payload = json_encode([
            'orders' => $orders,
            'total_count' => $ordersCount,
            'total_pages' => $totalPages
        ]);

        $response->getBody()->write($payload);

        return $response;
    }

    public function show(Request $request, Response $response, string $id): Response
    {
        $data = $this->orderRepository->getByOrderId((int) $id); 

        if ($data === false) {
            $this->error('Order not found', null, 404);
        }

        $body = json_encode($data);

        $response->getBody()->write($body);

        return $response;
    }

    public function store(Request $request, Response $response): Response
    {
        $validated = $this->storeOrderValidator->validate($request->getParsedBody());
 
        if (!$validated) {
            return $this->error('There was a problem with your submission.', $this->storeOrderValidator->errorBag(), 422);
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

}
