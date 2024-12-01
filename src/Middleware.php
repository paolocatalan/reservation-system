<?php

declare(strict_types=1);

use DI\Container;
use Slim\App;
use App\Middleware\AddJsonResponseHeader;
use Slim\Handlers\Strategies\RequestResponseArgs;

return function(App $app, Container $container) {
    $app->addBodyParsingMiddleware();
    $collector = $app->getRouteCollector();
    $collector->setDefaultInvocationStrategy(new RequestResponseArgs);

    $errorMiddlleware = $app->addErrorMiddleware(true, true, true);
    $errorHandler = $errorMiddlleware->getDefaultErrorHandler();
    $errorHandler->forceContentType('application/json');

    $app->add(new AddJsonResponseHeader);

};
