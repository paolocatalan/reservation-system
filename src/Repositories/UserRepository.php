<?php

declare(strict_types=1);

namespace App\Repositories;

class UserRepository extends BaseRepository
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

    public function getByEmail(string $email): array|bool
    {
        $stmt = $this->database->prepare('SELECT email FROM user WHERE email = ?');

        $stmt->execute([$email]);

        return $stmt->fetch();
    }

    public function create(array $data): int
    {
        $query = 'INSERT INTO user (name, email, password, created_at, updated_at) VALUES (:name, :email, :password, NOW(), NOW())';

        $stmt = $this->database->prepare($query);

        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':password', $data['password']);

        $stmt->execute();

        return (int) $this->database->lastInsertId();
    }

}
