<?php

declare(strict_types=1);

namespace App\Repositories;

class OrderRepository extends BaseRepository
{
    public function getAll(): array
    {
        $stmt = $this->database->query('SELECT * FROM `order`'); 

        return $stmt->fetchAll();
    } 

    public function getById(int $id): array|bool
    {
        $query = 'SELECT * FROM `order` WHERE id = :id';

        $stmt = $this->database->prepare($query);

        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch();
    }

    public function create(array $order, int $invoiceId): int
    {
        $query = 'INSERT INTO `order` (invoice_id, amount, name, email, created_at, updated_at) VALUES (:invoice_id, :amount, :name, :email, NOW(), NOW())';

        $stmt = $this->database->prepare($query);

        $stmt->bindValue(':invoice_id', $invoiceId);
        $stmt->bindValue(':amount', $order['amount']);
        $stmt->bindValue(':name', $order['name']);
        $stmt->bindValue(':email', $order['email']);

        $stmt->execute();

        return (int) $this->database->lastInsertId();
    }
    
    public function find(int $id): array
    {
        $stmt = $this->database->prepare('
            SELECT order.id, name, email, room_type, checkin_date, checkout_date, table_setting, reservation_date
            FROM `order`
            INNER JOIN room
            ON order.id = room.order_id
            INNER JOIN restaurant
            ON order.id = restaurant.order_id 
            WHERE order.id = ?
            ');

        $stmt->execute([$id]);

        $order = $stmt->fetch();

        return $order ? $order : [];
    }

    public function getAllReservation(): array
    {
        $stmt = $this->database->query('
            SELECT order.id, name, amount, room_type, checkin_date, checkout_date, table_setting, reservation_date 
            FROM `order`
            LEFT JOIN room
            ON order.id = room.order_id
            LEFT JOIN restaurant
            ON order.id = restaurant.order_id
            WHERE room.checkin_date > NOW() OR restaurant.reservation_date > NOW()
        ');

        return $stmt->fetchAll();
    }
}
