<?php

declare(strict_types=1);

namespace App\Services;

use App\Database;
use App\Repositories\OrderRepository;
use App\Repositories\RestaurantRepository;

class RestaurantService
{
    public function __construct(
        private Database $database,
        private OrderRepository $orderRepository,
        private RestaurantRepository $restaurantRepository,
    ) {}

    public function add(array $order, int $invoiceId): int 
    {
        try {
            $this->database->beginTransaction();

            $orderId = $this->orderRepository->create($order, $invoiceId);

            $this->restaurantRepository->reserveTable($order, $orderId);

            $this->database->commit();

        } catch (\Throwable $th) {
            if ($this->database->inTransaction()) {
                $this->database->rollBack();
            }

            throw $th;
        }

        return $orderId;

    }
}
