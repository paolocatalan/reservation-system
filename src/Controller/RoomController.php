<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\RoomRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

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

    public function show(Request $request, Response $response, string $id): Response
    {
        $data = $this->repository->getById((int) $id); 

        if ($data === false) {
            throw new HttpNotFoundException($request, message: 'product not found');
        }

        $body = json_encode($data);

        $response->getBody()->write($body);

        return $response;

    }

}
