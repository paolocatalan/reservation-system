<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;

class Database
{
    private PDO $pdo;

    public function __construct(
        private string $host,
        private string $name,
        private string $user,
        private string $password,
        private string $port
    ) 
    {
        try {
            $this->pdo = new PDO('mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->name, $this->user, $this->password,
                [
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int) $e->getCode());
        }

    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->pdo, $name], $arguments);
    }

}
