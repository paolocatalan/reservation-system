<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\RestaurantRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class FindTableByNameController
{
    public function __construct(
        private RestaurantRepository $restaurantRepository
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $results = $this->restaurantRepository->searchByName($data['search']);

        $payload = json_encode($results);

        $response->getBody()->write($payload);

        return $response;
    }
}
