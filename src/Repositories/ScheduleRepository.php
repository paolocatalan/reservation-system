<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

class ScheduleRepository
{
    public function __construct(
        public Database $database 
    ) { }

    public function getAll(): array
    {
        $pdo = $this->database->getConnection();

        $stmt = $pdo->query('SELECT * FROM schedule'); 

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 

    public function getById(int $id): array|bool
    {
        $sql = 'SELECT *
                FROM schedule
                WHERE id = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function reserveProduct(int $id, string $date): int 
    {
        $query = 'INSERT INTO schedule (date, product_id, created_at, updated_at) VALUES (:date, :product_id, NOW(), NOW())';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($query);

        $stmt->bindValue(':date', $date);
        $stmt->bindValue(':product_id', $id);

        $stmt->execute();

        return (int) $pdo->lastInsertId();
    }
}
