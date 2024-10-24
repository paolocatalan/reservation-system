<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\ScheduleRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ScheduleController
{
    public function __construct(
        private ScheduleRepository $repository
    ) { }

    public function index(Request $request, Response $response): Response
    {
        $data = $this->repository->getAll();
        $body = json_encode($data);
        $response->getBody()->write($body);

        return $response;
    }

    public function create(Request $request, Response $response): Response
    {
        $date = "2024-10-28 09:00:00";
        $product_id = 1;

        $scheduleId = $this->repository->reserveProduct($product_id, $date);
        $data = $this->repository->getById($scheduleId);

        $body = json_encode($data);
        $response->getBody()->write($body);

        return $response;
    }

}
