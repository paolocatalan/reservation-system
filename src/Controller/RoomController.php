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

    public function index(Request $request, Response $response, string $type): Response
    {
        $data = $this->repository->getByRoomType(strtolower($type));

        $payload = json_encode($data);

        $response->getBody()->write($payload);

        return $response;
    }

    public function search(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $results = $this->repository->searchByName($data['search']);

        $payload = json_encode($results);

        $response->getBody()->write($payload);

        return $response;
    }

}
