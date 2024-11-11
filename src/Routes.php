<?php

declare(strict_types=1);

use App\Controller\AuthController;
use App\Controller\FetchOrderController;
use App\Controller\OrderController;
use App\Controller\RestaurantController;
use App\Controller\SessionController;
use App\Middleware\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function(App $app) {
    $app->group('/api', function (RouteCollectorProxy $group) {
        $group->get('/orders', [OrderController::class, 'index'])->add(new AuthMiddleware());
        $group->get('/orders/{orderId:[0-9]+}', [OrderController::class, 'show'])->add(new AuthMiddleware());
        $group->post('/orders', [OrderController::class, 'store']);

        $group->get('/orders/dates', FetchOrderController::class)->add(new AuthMiddleware());
        $group->post('/orders/dinnings', [RestaurantController::class, 'store']);

    });

    $app->post('/register', [AuthController::class, 'store']);
    $app->post('/login', [SessionController::class, 'store']);
};
