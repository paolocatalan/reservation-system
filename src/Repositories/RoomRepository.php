<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

class RoomRepository
{
    public function __construct(
        public Database $database 
    ) {}

    public function getAll(): array
    {
        $stmt = $this->database->query('SELECT * FROM Room'); 

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 

    public function getById(int $id): array|bool
    {
        $sql = 'SELECT *
                FROM Room
                WHERE id = :id';

        $stmt = $this->database->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function reserveRoom(array $order): int 
    {
        $query = 'INSERT INTO room (order_id, room_type, checkin_date, checkin_out, updated_at, created_at) VALUES (:order_id, :room_type, :checkin_date, :checkin_out, NOW(), NOW())';

        $stmt = $this->database->prepare($query);

        $stmt->bindValue(':order_id', $order['order_id']);
        $stmt->bindValue(':room_type', $order['room_type']);
        $stmt->bindValue(':checkin_date', $order['checkin_date']);
        $stmt->bindValue(':checkin_out', $order['checkout_date']);

        $stmt->execute();

        return (int) $this->database->lastInsertId();
    }
}
