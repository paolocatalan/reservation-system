<?php

declare(strict_types=1);

namespace App\Services;

use App\Database;
use App\Monologger;
use App\Repositories\OrderRepository;
use App\Repositories\RestaurantRepository;
use App\Repositories\RoomRepository;

class ReservationService
{
    public function __construct(
        private Database $database,
        private OrderRepository $orderRepository,
        private RestaurantRepository $restaurantRepository,
        private RoomRepository $roomRepository,
        private Monologger $monolog
    ) {}

    public function add(array $order, int $invoiceId): int 
    {
        try {
            $this->database->beginTransaction();

            $orderId = $this->orderRepository->create($order, $invoiceId);

            $this->roomRepository->create($order, $orderId);

            if ($order['seats']) {
                $this->restaurantRepository->create($order, $orderId);
            }
            $this->database->commit();

        } catch (\Throwable $th) {
            if ($this->database->inTransaction()) {
                $this->database->rollBack();
            }

            $this->monolog->logger->critical($th->getMessage());

            throw $th;
        }

        // $this->orderRepository->find($orderId)

        return $orderId;

    }
}
