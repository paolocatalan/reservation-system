<?php

declare(strict_types=1);

namespace App;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class Monologger 
{
    public $logger;

    public function __construct()
    {
        $this->logger = new Logger('Reservation');
        $this->logger->pushHandler(new StreamHandler(dirname(__DIR__) . '/storage/logs/app.log'), Level::Info);
    }

    public function __get(string $property): Logger
    {
        if ($this->logger->{$property}) {
            return $this->logger->{$property};
        }
    }

}