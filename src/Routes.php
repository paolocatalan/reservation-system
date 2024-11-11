<?php

declare(strict_types=1);

use App\Controller\AuthController;
use App\Controller\AuthSessionController;
use Slim\App;
use App\Controller\OrderController;
use App\Controller\RestaurantController;
use App\Middleware\AuthMiddleware;

return function(App $app) {
    $app->get('/api/orders', [OrderController::class, 'index'])->add(AuthMiddleware::class);
    $app->get('/api/orders/{orderId:[0-9]+}', [OrderController::class, 'show'])->add(AuthMiddleware::class);
    $app->post('/api/book', [OrderController::class, 'store']);
    $app->post('/api/restaurant', [RestaurantController::class, 'store']);
    $app->post('/api/register', [AuthController::class, 'store']);
    $app->post('/api/login', [AuthSessionController::class, 'store']);

    $app->get('/api/paginate', [OrderController::class, 'index']);

    $app->get('/seats', [RestaurantController::class, 'pull']);
};
