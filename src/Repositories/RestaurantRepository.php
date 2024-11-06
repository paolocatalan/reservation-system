<?php

declare(strict_types=1);

namespace App\Repositories;

class RestaurantRepository extends BaseRepository
{
    public function getAll(): array
    {
        $stmt = $this->database->query('SELECT * FROM restaurant'); 

        return $stmt->fetchAll();
    } 

    public function getById(int $id): array|bool
    {
        $query = 'SELECT * FROM restaurant WHERE id = :id';

        $stmt = $this->database->prepare($query);

        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch();
    }

    public function create(array $order, int $orderId): int
    {
        $query = 'INSERT INTO restaurant (order_id, seats, table_setting, reservation_date, created_at, updated_at) VALUES (:order_id, :seats, :table_setting, :reservation_date, NOW(), NOW())';

        $stmt = $this->database->prepare($query);

        $stmt->bindValue(':order_id', $orderId);
        $stmt->bindValue(':seats', $order['seats']);
        $stmt->bindValue(':table_setting', $order['table_setting']);
        $stmt->bindValue(':reservation_date', $order['restaurant_date']);

        $stmt->execute();

        return (int) $this->database->lastInsertId();
    }

    public function getByOrderId(int $id): array|bool
    {
        $stmt = $this->database->prepare('
            SELECT order.id, name, amount, seats, table_setting, reservation_date
            FROM restaurant
            JOIN `order`
            ON restaurant.order_id = order.id
            WHERE order_id = :id
            ');

        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch();
    }

    public function getAllReservation(): array
    {
        $stmt = $this->database->query('
            SELECT order.id, invoice_id, name, amount, seats, table_setting, reservation_date
            FROM `order`
            RIGHT JOIN restaurant
            ON order.id = restaurant.order_id
            WHERE reservation_date > NOW() 
            ');

        return $stmt->fetchAll();
    }

    public function getAllReservSeats(string $startTime, string $endTime): array
    {
        $stmt = $this->database->prepare('
            SELECT seats
            FROM restaurant
            WHERE reservation_date BETWEEN :start_time AND :end_time 
            ');

        $stmt->bindValue(':start_time', $startTime);
        $stmt->bindValue(':end_time', $endTime);

        $stmt->execute();

        return $stmt->fetchAll();
    }


}
