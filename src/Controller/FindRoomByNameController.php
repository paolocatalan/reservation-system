<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\RoomRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class FindRoomByNameController
{
    public function __construct(
        private RoomRepository $roomRepository
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $results = $this->roomRepository->searchByName($data['search']);

        $payload = json_encode($results);

        $response->getBody()->write($payload);

        return $response;
    }
}
