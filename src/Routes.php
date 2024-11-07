<?php

declare(strict_types=1);

use Slim\App;
use App\Controller\OrderController;
use App\Controller\RestaurantController;

return function(App $app) {
    $app->post('/api/book', [OrderController::class, 'store']);
    $app->post('/api/restaurant', [RestaurantController::class, 'store']);

};
