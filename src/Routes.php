<?php

declare(strict_types=1);

use App\Controller\AuthController;
use App\Controller\FindRoomByNameController;
use App\Controller\FindTableByNameController;
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

        $group->post('/orders/dinnings/names', FindTableByNameController::class)->add(new AuthMiddleware());
        $group->post('/orders/rooms/names', FindRoomByNameController::class)->add(new AuthMiddleware());

        $group->get('/orders/rooms', [RoomController::class, 'index'])->add(new AuthMiddleware());

        $group->get('/orders/dates', GetOrderByDateController::class)->add(new AuthMiddleware());

        $group->post('/register', [AuthController::class, 'store']);
        $group->post('/login', [SessionController::class, 'store']);

    });
};
