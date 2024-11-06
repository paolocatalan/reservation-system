<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\ProductRepository;
use App\Repositories\RestaurantRepository;
use Slim\Exception\HttpNotFoundException;

class ProductController
{
    public function __construct(
        private RestaurantRepository $restaurantRepository,
        private ProductRepository $repository
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
            throw new HttpNotFoundException($request, message: 'product not found');
        }

        $body = json_encode($data);

        $response->getBody()->write($body);

        return $response;

    }

    public function findOrder(Request $request, Response $response): Response
    {
        // $id = 2;

        // $data = $this->restaurantRepository->getByOrderId($id);
        // $data = $this->roomRepository->getAvailability('Villa', '2024-11-09 12:00:00');
        $data = $this->restaurantRepository->getAllReservSeats('2024-11-11 18:00:00', '2024-11-12 23:00:00');

        $body = json_encode($data);

        $response->getBody()->write($body);

        return $response;
    }
}
