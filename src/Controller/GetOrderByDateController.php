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

        if ($data['after'] && $data['before']) {
            $dateAfter = $this->validateDate($data['after']);
            $dateBefore = $this->validateDate($data['before']);

            if (!$dateAfter || !$dateBefore) {
                return $this->error('Invalid date format', null, 422);
            }
        } else {
            $dateAfter = date('Y-m-d' . ' 12:00:00');
            $dateBefore = date('Y-m-d' . ' 12:00:00', strtotime('+30 days'));
        }

        $pageSize = filter_var($data['limit'], FILTER_VALIDATE_INT, ['options' => ['default' => 10, 'min_range' => 1]]);
        $page = filter_var($data['offset'], FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
 
        $records = $this->orderRepository->getOrderByDates($dateAfter, $dateBefore, $pageSize, $page);
        $totalRecords = $this->orderRepository->getOrdersCount();
        $totalPages = ceil($totalRecords/$pageSize);

        if (empty($records)) {
           return $this->success('No results found.', null, 200);
        }

        $response->getBody()->write(json_encode([
            'data' => $records,
            'pagination' => [
                'total_records' => $totalRecords,
                'total_pages' => $totalPages,
                'current_page' => $page
            ]
        ]));

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
