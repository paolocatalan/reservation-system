<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\OrderRepository;
use App\Traits\HttpResponses;
use DateTime;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class GetOrderByDateController
{
    use HttpResponses;

    public function __construct(
        private OrderRepository $orderRepository
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $data = (array) $request->getQueryParams();

        $dateAfter = $this->validateDate($data['after']);
        $dateBefore = $this->validateDate($data['before']);

        if (!$dateAfter || !$dateBefore) {
            return $this->error('Invalid date format', null, 422);
        }
 
        $results = $this->orderRepository->getOrderByDates($dateAfter, $dateBefore);

        if (empty($results)) {
           return $this->success('No results found.', null, 200);
        }

        $payload = json_encode($results);

        $response->getBody()->write($payload);

        return $response;
    }

    private function validateDate(string $dateInput): string|bool
    {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $dateInput);

        if ($date && $date->format('Y-m-d H:i:s') == $dateInput) {
            return $dateInput; 
        }

        return false;
    }
}
