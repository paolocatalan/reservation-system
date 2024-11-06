<?php

declare(strict_types=1);

use App\Controller\ProductController;
use App\Middleware\AddJsonResponseHeader;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use Slim\Handlers\Strategies\RequestResponseArgs;

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$containerBuilder = new ContainerBuilder();
$container = $containerBuilder->addDefinitions(dirname(__DIR__) . '/configs/definitions.php')->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$collector = $app->getRouteCollector();

$collector->setDefaultInvocationStrategy(new RequestResponseArgs);

$errorMiddlleware = $app->addErrorMiddleware(true, true, true);

$errorHandler = $errorMiddlleware->getDefaultErrorHandler();

$errorHandler->forceContentType('application/json');

$app->add(new AddJsonResponseHeader);

$app->get('/api/products', [ProductController::class, 'index']);

$app->get('/api/products/{productId:[0-9]+}', [ProductController::class, 'show']);

$app->run();
