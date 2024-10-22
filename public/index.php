<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$containerBuilder = new ContainerBuilder();
$container = $containerBuilder->addDefinitions(dirname(__DIR__) . '/configs/definitions.php')->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response) {
    
    $repository = $this->get(App\Repositories\ProductRepository::class);

    $data = $repository->getAll();

    $body = json_encode($data);

    $response->getBody()->write($body);

    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
