<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\RestaurantRepository;
use App\Traits\HttpResponses;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class FindTableByNameController
{
    use HttpResponses;

    public function __construct(
        private RestaurantRepository $restaurantRepository
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $data = (array) $request->getParsedBody();

        $results = $this->restaurantRepository->searchByName(htmlspecialchars($data['name']));

        if (empty($results)) {
           return $this->success('No results found.', null, 200);
        }

        $payload = json_encode($results);

        $response->getBody()->write($payload);

        return $response;
    }
}
