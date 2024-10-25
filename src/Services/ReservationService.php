<?php

declare(strict_types=1);

namespace App\Services;

use App\Database;
use App\Repositories\OrderRepository;
use App\Repositories\RestaurantRepository;
use App\Repositories\RoomRepository;

class ReservationService
{
    public function __construct(
        private Database $database,
        private OrderRepository $orderRepository,
        private RestaurantRepository $restaurantRepository,
        private RoomRepository $roomRepository  
    ) { }

    public function processOrder(array $order): array
    {
        try {
            $this->database->beginTransaction();

            $orderId = $this->orderRepository->create($order);

            $roomId = $this->roomRepository->reserveRoom($order, $orderId);

            $restaurantId = $this->restaurantRepository->reserveTable($order, $orderId);

            $this->database->commit();


        } catch (\Throwable $th) {

            if ($this->database->inTransaction()) {
                $this->database->rollBack();
            }

            throw $th;
        }

        return [
            'order_id' => $orderId,
            'room_id' => $roomId,
            'restaurant_id' => $restaurantId 
        ];

    }
}
