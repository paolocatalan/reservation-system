<?php

declare(strict_types=1);

use App\Database;
use App\Services\PaymentGateway\Visa;
use App\Services\PaymentGatewayService;
use Psr\Container\ContainerInterface;

return [
    Database::class => function() {
        return new Database(
            host: $_ENV['DB_HOST'], 
            name: $_ENV['DB_NAME'], 
            user: $_ENV['DB_USER'], 
            password: $_ENV['DB_PASS'] 
        );
    },
    PaymentGatewayService::class => fn(ContainerInterface $container) => $container->get(Visa::class)

];
