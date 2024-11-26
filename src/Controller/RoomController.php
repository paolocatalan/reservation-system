<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enums\RoomType;
use App\Repositories\RoomRepository;
use App\Traits\HttpResponses;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class RoomController
{
    use HttpResponses;

    public function __construct(
        private RoomRepository $roomRepository
    ) {}

    public function index(Request $request, Response $response): Response
    {
        $data = (array) $request->getQueryParams();

        if (in_array($data['room_type'], array_column(RoomType::cases(), 'value'))) {
            $roomType = $data['room_type'];
        } else {
            return $this->error('Invalid room type', null, 422);
        }

        $pageSize = filter_var($data['limit'], FILTER_VALIDATE_INT, ['options' => ['default' => 10, 'min_range' => 1]]);
        $page = filter_var($data['offset'], FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);

        $records = $this->roomRepository->getByRoomType($roomType, $pageSize, $page);
        $totalPages = ceil(count($records)/$pageSize);

        if (empty($records)) {
            return $this->success('No results found.', null, 200);
        }

        $response->getBody()->write(json_encode([
            'data' => $records,
            'pagination' => [
                'total_records' => count($records),
                'total_pages' => $totalPages,
                'current_page' => $page
            ]
        ]));

        return $response;
    }
}
