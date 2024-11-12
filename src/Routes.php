<?php

declare(strict_types=1);

use App\Controller\AuthController;
use App\Controller\GetOrderByDateController;
use App\Controller\OrderController;
use App\Controller\RestaurantController;
use App\Controller\RoomController;
use App\Controller\SessionController;
use App\Middleware\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function(App $app) {
    $app->group('/api', function (RouteCollectorProxy $group) {
        $group->get('/orders', [OrderController::class, 'index'])->add(new AuthMiddleware());
        $group->get('/orders/{orderId:[0-9]+}', [OrderController::class, 'show'])->add(new AuthMiddleware());

        $group->post('/orders', [OrderController::class, 'store']);
        $group->post('/orders/dinnings', [RestaurantController::class, 'store']);

        $group->post('/orders/dinnings/names/', [RestaurantController::class, 'search'])->add(new AuthMiddleware());
        $group->post('/orders/rooms/names/', [RoomController::class, 'search'])->add(new AuthMiddleware());
        
        $group->get('/orders/rooms/{type}', [RoomController::class, 'index'])->add(new AuthMiddleware());

        $group->get('/orders/', GetOrderByDateController::class)->add(new AuthMiddleware());
    });

    $app->post('/register', [AuthController::class, 'store']);
    $app->post('/login', [SessionController::class, 'store']);
};
