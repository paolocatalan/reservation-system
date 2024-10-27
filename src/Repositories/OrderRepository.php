<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

class OrderRepository
{
    public function __construct(
        public Database $database 
    ) { }

    public function getAll(): array
    {
        $stmt = $this->database->query('SELECT * FROM `order`'); 

        return $stmt->fetchAll();
    } 

    public function getById(int $id): array|bool
    {
        $query = 'SELECT * FROM `order` WHERE id = :id';

        $stmt = $this->database->prepare($query);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch();
    }

    public function create(array $order): int
    {
        $query = 'INSERT INTO `order` (name, email, created_at, updated_at) VALUES (:name, :email, NOW(), NOW())';

        $stmt = $this->database->prepare($query);

        $stmt->bindValue(':name', $order['name']);
        $stmt->bindValue(':email', $order['email']);

        $stmt->execute();

        return (int) $this->database->lastInsertId();
    }
    
    public function find(int $id): array
    {
        $stmt = $this->database->prepare('
            SELECT order.id, name, email, room_type, checkin_date, table_setting, reservation_date
            FROM `order`
            LEFT JOIN room
            ON order.id = room.order_id
            LEFT JOIN restaurant
            ON order.id = restaurant.order_id 
            WHERE order.id = ?
            ');

        $stmt->execute([$id]);

        $order = $stmt->fetch();

        return $order ? $order : [];
    }
}
