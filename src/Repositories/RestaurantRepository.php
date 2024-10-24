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
        $pdo = $this->database->getConnection();

        $stmt = $pdo->query('SELECT * FROM restaurant'); 

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 

    public function getById(int $id): array|bool
    {
        $sql = 'SELECT *
                FROM restaurant
                WHERE id = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


}
