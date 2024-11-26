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
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class OrderController
{
    use HttpResponses;

    public function __construct(
        private OrderRepository $orderRepository,
        private InvoiceService $invoiceService,
        private ReservationService $reservationService,
        private StoreOrderValidator $storeOrderValidator,
        private FilesystemAdapter $cache
    ) {}

    public function index(Request $request, Response $response): Response
    {
        $data = (array) $request->getQueryParams();

        $pageSize = filter_var($data['limit'], FILTER_VALIDATE_INT, ['options' => ['default' => 10, 'min_range' => 1]]);
        $page = filter_var($data['offset'], FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);

        $records = $this->cache->get('orders_' . $pageSize . $page, function (ItemInterface $item) use ($pageSize, $page): array {
            $item->expiresAfter(3600);
            $value = $this->orderRepository->getAll($pageSize, $page);

            return $value;
        });

        if (empty($records)) {
            return $this->success('No results found.', null, 200);
        }

        $totalRecords = $this->cache->get('order_count', function (ItemInterface $item): int {
            $item->expiresAfter(3600);
            $value = $this->orderRepository->getOrdersCount();

            return $value;
        });

        $totalPages = ceil($totalRecords/$pageSize);

        $payload = json_encode([
            'data' => $records,
            'pagination' => [
                'total_records' => $totalRecords,
                'total_pages' => $totalPages,
                'current_page' => $page
            ]
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
