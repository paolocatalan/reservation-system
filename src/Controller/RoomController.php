<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\RoomRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class RoomController
{
    public function __construct(
        private RoomRepository $repository
    ) { }

    public function index(Request $request, Response $response): Response
    {
        $data = $this->repository->getAll();
        $body = json_encode($data);
        $response->getBody()->write($body);

        return $response;
    }

}
