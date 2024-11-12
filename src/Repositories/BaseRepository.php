<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;

abstract class BaseRepository
{
    public function __construct(
        protected Database $database
    ) {}
}

