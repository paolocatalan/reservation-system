<?php

declare(strict_types=1);

use App\Database;
use App\Message\AddReservation;
use App\MessageHandler\AddReservationHandler;
use App\Repositories\RoomRepository;
use App\Services\PaymentGateway\Visa;
use App\Services\PaymentGatewayService;
use App\Services\ReservationService;
use Doctrine\DBAL\Connection;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineTransport;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Messenger\Transport\TransportInterface;

return [
    Database::class => function() {
        return new Database(
            host: $_ENV['DB_HOST'], 
            name: $_ENV['DB_NAME'], 
            user: $_ENV['DB_USER'], 
            password: $_ENV['DB_PASS'] 
        );
    },
    // RoomRepository::class => DI\autowire(RoomRepository::class),
    // RoomRepository::class => function(ContainerInterface $container) {
    //     return new RoomRepository($container->get(Database::class));
    // }, 
    PaymentGatewayService::class => fn(ContainerInterface $container) => $container->get(Visa::class),
    // MessageBusInterface::class => function (ContainerInterface $container) {
    //     $reservation = $container->get(ReservationService::class);

    //     $handlers = [
    //         AddReservation::class => [new AddReservationHandler($reservation)],
    //     ];

    //     $handlersLocator = new HandlersLocator($handlers);
    //     $middleware = [new HandleMessageMiddleware($handlersLocator)];

    //     return new MessageBus($middleware);
    // },
    // TransportInterface::class => function (ContainerInterface $container)
    // {
    //     $connection = $container->get(Connection::class);
    //     $serialize = $container->get(Serializer::class);
    //     return new DoctrineTransport($connection, $serialize);
    // }

];
