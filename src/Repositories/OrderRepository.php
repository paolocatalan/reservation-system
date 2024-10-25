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

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 

    public function getById(int $id): array|bool
    {
        $sql = 'SELECT *
                FROM `order`
                WHERE id = :id';

        $stmt = $this->database->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $order) {
        $query = 'INSERT INTO `order` (name, email, created_at, updated_at) VALUES (:name, :email, NOW(), NOW())';

        $stmt = $this->database->prepare($query);

        $stmt->bindValue(':name', $order['name']);
        $stmt->bindValue(':email', $order['email']);

        $stmt->execute();

        return (int) $this->database->lastInsertId();

    }
}
