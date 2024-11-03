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

    public function reserveTable(array $order, int $orderId): int
    {
        $query = 'INSERT INTO restaurant (order_id, table_setting, reservation_date, created_at, updated_at) VALUES (:order_id, :table_setting, :reservation_date, NOW(), NOW())';

        $stmt = $this->database->prepare($query);

        $stmt->bindValue(':order_id', $orderId);
        $stmt->bindValue(':table_setting', $order['table_setting']);
        $stmt->bindValue(':reservation_date', $order['restaurant_date']);

        $stmt->execute();

        return (int) $this->database->lastInsertId();
    }

    public function getAllReservation(): array
    {
        $stmt = $this->database->query('
            SELECT order.id, invoice_id, name, email, amount, table_setting, reservation_date
            FROM `order`
            INNER JOIN restaurant
            ON order.id = restaurant.order_id
            WHERE reservation_date > NOW() 
            ');

        return $stmt->fetchAll();
    }

    public function findReservation(int $id): array|bool
    {
        $query = 'SELECT order_id, table_settings, reservation_date FROM restaurant WHERE order_id = :id';

        $stmt = $this->database->prepare($query);

        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch();
    }
}
