<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\RoomRepository;
use App\Traits\HttpResponses;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class RoomController
{
    use HttpResponses;

    public function __construct(
        private RoomRepository $repository
    ) {}

    public function index(Request $request, Response $response): Response
    {
        $data = $this->repository->getAll();

        $body = json_encode($data);

        $response->getBody()->write($body);

        return $response;
    }

    public function show(Request $request, Response $response, string $id): Response
    {
        $data = $this->repository->getById((int) $id); 

        if ($data === false) {
            return $this->error('Booking not found', null, 404);
        }

        $body = json_encode($data);

        $response->getBody()->write($body);

        return $response;

    }

}
