<?php

declare(strict_types=1);

namespace App\Repositories;

class RoomRepository extends BaseRepository
{
    public function getAll(): array
    {
        $stmt = $this->database->query('SELECT * FROM Room'); 

        return $stmt->fetchAll();
    } 

    public function getById(int $id): array|bool
    {
        $query = 'SELECT * FROM room WHERE id = :id';

        $stmt = $this->database->prepare($query);

        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch();
    }

    public function create(array $order, int $orderId): int 
    {
        $query = 'INSERT INTO room (order_id, room_type, checkin_date, checkout_date, updated_at, created_at) VALUES (:order_id, :room_type, :checkin_date, :checkout_date, NOW(), NOW())';

        $stmt = $this->database->prepare($query);

        $stmt->bindValue(':order_id', $orderId);
        $stmt->bindValue(':room_type', $order['room_type']);
        $stmt->bindValue(':checkin_date', $order['checkin_date']);
        $stmt->bindValue(':checkout_date', $order['checkout_date']);

        $stmt->execute();

        return (int) $this->database->lastInsertId();
    }

    public function getByOrderId(int $id): array|bool
    {
        $stmt = $this->database->prepare('
            SELECT order.id, name, amount, room_type, checkin_date, checkout_date
            FROM room
            JOIN `order`
            ON room.order_id = order.id
            WHERE order_id = :id
            ');

        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch();
    }

    public function getAllReservation(): array
    {
        $stmt = $this->database->query('
            SELECT order.id, invoice_id, name, amount, room_type, checkin_date, checkout_date
            FROM `order`
            RIGHT JOIN room
            ON order.id = room.order_id
            WHERE checkin_date > NOW() 
            ');

        return $stmt->fetchAll();
    }

}
