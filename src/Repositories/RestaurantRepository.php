<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

class RestaurantRepository
{
    public function __construct(
        public Database $database
    ) {}


    public function getAll(): array
    {
        $stmt = $this->database->query('SELECT * FROM restaurant'); 

        return $stmt->fetchAll();
    } 

    public function getById(int $id): array|bool
    {
        $query = 'SELECT * FROM restaurant WHERE id = :id';

        $stmt = $this->database->prepare($query);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

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

    public function getFutureDates(): array
    {
        $query = 'SELECT id, table_setting, reservation_date FROM restaurant WHERE reservation_date > NOW()';

        $stmt = $this->database->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll();
    }

}
