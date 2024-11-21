<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

class RoomRepository extends BaseRepository
{
    public function getAll(): array
    {
        $stmt = $this->database->query('SELECT * FROM room');

        return $stmt->fetchAll();
    } 

    public function getByOrderId(int $id): array|bool
    {
        $stmt = $this->database->prepare('
            SELECT order.id, name, amount, room_type, checkin_date, checkout_date
            FROM room
            INNER JOIN `order`
            ON room.order_id = order.id
            WHERE order_id = :id
            ');

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

    public function searchByName(string $searchName): array|bool
    {
        $stmt = $this->database->prepare("
            SELECT order_id, invoice_id, name, room_type, checkin_date, checkout_date
            FROM room
            LEFT JOIN `order`
            ON room.order_id = order.id 
            WHERE order.name LIKE :search_name
            ORDER BY checkin_date DESC
            ");

        $stmt->bindValue(':search_name', "%$searchName%");

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getAvailability(string $roomType, string $checkDate): int
    {
        // docs recommends to use count star
        $stmt = $this->database->prepare('
            SELECT COUNT(*) as room_type_count
            FROM room
            WHERE room_type = :room_type
            AND :check_availability_date BETWEEN checkin_date AND checkout_date
            ');

        $stmt->bindValue(':room_type', $roomType);
        $stmt->bindValue(':check_availability_date', $checkDate);

        $stmt->execute();

        $result = $stmt->fetch();

        return $result ? $result['room_type_count'] : 0;
    }

    public function getByRoomType(string $type, int $pageSize = 10, int $page = 1): array
    {
        $offset = $pageSize * ($page - 1);

        $stmt = $this->database->prepare('SELECT * FROM room WHERE room_type = :room_type ORDER BY checkin_date DESC LIMIT :limit OFFSET :offset'); 

        $stmt->bindValue(':room_type', $type, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    } 

    public function getRoomReservationCount()
    {
        $stmt = $this->database->query('SELECT COUNT(*) AS total_room_reservation FROM room');

        return $stmt->fetch()['total_room_reservation'];
    }
}
