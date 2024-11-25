<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\OrderRepository;
use App\Traits\HttpResponses;
use DateTime;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class GetOrderByDateController
{
    use HttpResponses;

    public function __construct(
        private OrderRepository $orderRepository,
        private FilesystemAdapter $cache
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
            $dateAfter = date('Y-m-d'.' 12:00:00');
            $dateBefore = date('Y-m-d'.' 12:00:00', strtotime('+30 days'));
        }

        $pageSize = filter_var($data['limit'], FILTER_VALIDATE_INT, ['options' => ['default' => 10, 'min_range' => 1]]);
        $page = filter_var($data['offset'], FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);

        $records = $this->cache->get('orders_' . strtotime($dateAfter) . strtotime($dateBefore) . $pageSize . $page, function (ItemInterface $item) use ($dateAfter, $dateBefore, $pageSize, $page): array {
            $item->expiresAfter(3600);
            $value = $this->orderRepository->getOrderByDates($dateAfter, $dateBefore, $pageSize, $page);

            return $value;
        });

        if (empty($records)) {
            return $this->success('No results found.', null, 200);
        }

        $totalRecords = $this->cache->get('order_count', function (ItemInterface $item): int {
            $item->expiresAfter(3600);
            $value = $this->orderRepository->getOrdersCount();

            return $value;
        });

        $totalPages = ceil($totalRecords / $pageSize);
        $response->getBody()->write(json_encode([
            'data' => $records,
            'pagination' => [
                'total_records' => $totalRecords,
                'total_pages' => $totalPages,
                'current_page' => $page,
            ],
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
