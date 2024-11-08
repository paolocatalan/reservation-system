<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$containerBuilder = new ContainerBuilder();
$container = $containerBuilder->addDefinitions(dirname(__DIR__) . '/configs/definitions.php')->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

$middleware = require dirname(__DIR__)  . '/src/Middleware.php';
$middleware($app, $container);

$router = require dirname(__DIR__) . '/src/Routes.php';
$router($app);

$app->run();
