<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

class RestaurantRepository
{
    public function __construct(
        private Database $database
    ) { } 

    public function getAll(): array
    {
        $stmt = $this->database->query('SELECT * FROM restaurant'); 

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 

    public function getById(int $id): array|bool
    {
        $sql = 'SELECT *
                FROM restaurant
                WHERE id = :id';

        $stmt = $this->database->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function reserveTable(array $order) {
        $query = 'INSERT INTO restaurant (order_id, table_setting, reservation_date, created_at, updated_at) VALUES (:order_id, :table_setting, :reservation_date, NOW(), NOW())';

        $stmt = $this->database->prepare($query);

        $stmt->bindValue(':order_id', $order['order_id']);
        $stmt->bindValue(':table_setting', $order['table_setting']);
        $stmt->bindValue(':reservation_date', $order['restaurant_date']);

        $stmt->execute();

        return (int) $this->database->lastInsertId();

    }


}
