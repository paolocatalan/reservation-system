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

        if (empty($results)) {
           return $this->success('No results found.', null, 200);
        }

        $payload = json_encode($data);

        $response->getBody()->write($payload);

        return $response;
    }
}
