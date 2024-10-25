<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

class ProductRepository
{
    public function __construct(
        public Database $database 
    ) { }

    public function getAll(): array
    {
        $stmt = $this->database->query('SELECT * FROM product'); 

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 

    public function getById(int $id): array|bool
    {
        $sql = 'SELECT *
                FROM product
                WHERE id = :id';

        $stmt = $this->database->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function reserveProduct(int $id, string $date): bool
    {
        $stmt = $this->database->prepare('
            UPDATE product
            SET reserved_at = :date, updated_at = NOW()
            WHERE id = :id
        ');

        $stmt->bindValue(':date', $date);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
