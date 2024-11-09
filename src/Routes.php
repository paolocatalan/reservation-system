<?php

declare(strict_types=1);

use App\Controller\AuthController;
use Slim\App;
use App\Controller\OrderController;
use App\Controller\RestaurantController;

return function(App $app) {
    $app->get('/api/orders', [OrderController::class, 'index']);
    $app->get('/api/order/{orderId:[0-9]+}', [OrderController::class, 'show']);
    $app->post('/api/book', [OrderController::class, 'store']);
    $app->post('/api/restaurant', [RestaurantController::class, 'store']);
    $app->post('/api/register', [AuthController::class, 'store']);
};
