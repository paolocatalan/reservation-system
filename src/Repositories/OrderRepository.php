<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

class OrderRepository extends BaseRepository
{
    public function getAll(int $resultsPerPage = 10, int $page = 1): array
    {
        //validate and sanitize input
       $resultsPerPage = filter_var($resultsPerPage, FILTER_VALIDATE_INT, ['options' => ['default' => 10, 'min_range' => 1]]);
        $page = filter_var($page, FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
 
        $offset = ($page - 1) * $resultsPerPage;

        $stmt = $this->database->prepare("SELECT * FROM `order` LIMIT :limit OFFSET :offset");

        $stmt->bindValue(':limit', $resultsPerPage, PDO::PARAM_INT);
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
 
    public function getOrderByDates($startDate, $endDate): array
    {
        $stmt = $this->database->prepare('
            SELECT order.id, invoice_id, name, amount, room_type, checkin_date, checkout_date
            FROM `order`
            LEFT JOIN room
            ON order.id = room.order_id
            WHERE checkin_date BETWEEN :start_date AND :end_date
            ');

        $stmt->bindValue(':start_date', $startDate);
        $stmt->bindValue(':end_date', $endDate);

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
}
