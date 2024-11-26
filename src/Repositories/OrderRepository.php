<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

class OrderRepository extends BaseRepository
{
    public function getAll(int $pageSize = 10, int $page = 1): array
    {
        $offset = $pageSize * ($page - 1);

        $stmt = $this->database->prepare('SELECT * FROM `order` ORDER BY id LIMIT :limit OFFSET :offset');

        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getByOrderId(int $id): array
    {
        $stmt = $this->database->prepare('
            SELECT order.id, name, room_type, checkin_date, checkout_date, seats, table_setting, reservation_date
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

    public function create(array $order, int $invoiceId): int
    {
        $query = 'INSERT INTO `order` (invoice_id, amount, name, email, created_at, updated_at) VALUES (:invoice_id, :amount, :name, :email, NOW(), NOW())';

        $stmt = $this->database->prepare($query);

        $stmt->bindValue(':invoice_id', $invoiceId);
        $stmt->bindValue(':amount', $order['amount']);
        $stmt->bindValue(':name', $order['name']);
        $stmt->bindValue(':email', $order['email']);

        $stmt->execute();

        return (int) $this->database->lastInsertId();
    }

    public function getOrderByDates(string $afterDate, string $beforeDate, int $pageSize = 10, int $page = 1): array
    {
        $offset = $pageSize * ($page - 1);

        $stmt = $this->database->prepare('
            SELECT order.id, invoice_id, name, amount, room_type, checkin_date, checkout_date
            FROM `order`
            LEFT JOIN room
            ON order.id = room.order_id
            WHERE checkin_date BETWEEN :after_date AND :before_date
            ORDER BY id
            LIMIT :limit OFFSET :offset
            ');

        $stmt->bindValue(':after_date', $afterDate);
        $stmt->bindValue(':before_date', $beforeDate);
        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function fetchFutureDates(): array
    {
        $stmt = $this->database->query('
            SELECT order.id, invoice_id, name, amount, room_type, checkin_date, checkout_date, seats, table_setting, reservation_date 
            FROM `order`
            LEFT JOIN room
            ON order.id = room.order_id
            LEFT JOIN restaurant
            ON order.id = restaurant.order_id
            WHERE room.checkin_date > NOW() OR restaurant.reservation_date > NOW()
            ');

        return $stmt->fetchAll();
    }

    public function getOrdersCount()
    {
        $stmt = $this->database->query('SELECT COUNT(*) AS total_orders FROM `order`');

        return $stmt->fetch()['total_orders'];
    }

    public function getInvoiceId(int $invoiceId): array
    {
        $stmt = $this->database->prepare('
            SELECT invoice_id, amount, name, room_type, checkin_date, checkout_date, seats, table_setting, reservation_date
            FROM `order`
            LEFT JOIN room
            ON order.id = room.order_id
            LEFT JOIN restaurant
            ON order.id = restaurant.order_id 
            WHERE order.invoice_id = :invoice_id 
            ');

        $stmt->bindValue(':invoice_id', $invoiceId, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

}
