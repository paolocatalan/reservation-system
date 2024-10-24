<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;

class Database
{
    public function __construct(
        private string $host,
        private string $name,
        private string $user,
        private string $password
    ) { }

    public function getConnection(): PDO
    {
        try {
            $dsn = "mysql:host=$this->host;dbname=$this->name;charset=utf8";

            return new PDO($dsn, $this->user, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->getConnection(), $name], $arguments);
    }

}
