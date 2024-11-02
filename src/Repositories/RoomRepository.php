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

    public function reserveRoom(array $order, int $orderId): int 
    {
        $query = 'INSERT INTO room (order_id, room_type, checkin_date, checkin_out, updated_at, created_at) VALUES (:order_id, :room_type, :checkin_date, :checkin_out, NOW(), NOW())';

        $stmt = $this->database->prepare($query);

        $stmt->bindValue(':order_id', $orderId);
        $stmt->bindValue(':room_type', $order['room_type']);
        $stmt->bindValue(':checkin_date', $order['checkin_date']);
        $stmt->bindValue(':checkin_out', $order['checkout_date']);

        $stmt->execute();

        return (int) $this->database->lastInsertId();
    }

    public function getFutureDates(): array
    {
        $query = 'SELECT id, order_id, room_type, checkin_date, checkin_out FROM room WHERE checkin_date > NOW()';

        $stmt = $this->database->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll();
    }


}
