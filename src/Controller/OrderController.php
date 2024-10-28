<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enums\RoomType;
use App\Enums\TableSetting;
use App\Repositories\OrderRepository;
use App\Services\ReservationService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;
use Valitron\Validator;

class OrderController
{
    public function __construct(
        private OrderRepository $orderRepository,
        private ReservationService $reservationService,
    ) { }

    public function index(Request $request, Response $response): Response
    {
        $data = $this->orderRepository->getAll();

        $body = json_encode($data);

        $response->getBody()->write($body);

        return $response;
    }

    public function show(Request $request, Response $response, string $id): Response
    {
        $data = $this->orderRepository->getById((int) $id); 

        if ($data === false) {
            throw new HttpNotFoundException($request, message: 'order not found');
        }

        $body = json_encode($data);

        $response->getBody()->write($body);

        return $response;

    }

    public function create(Request $request, Response $response): Response
    {
        $requestData = $request->getParsedBody();

        $validator = new Validator($requestData);

        $validator->rule('required', ['room_type', 'checkin_date', 'checkout_date', 'name', 'email']);

        $currentDate = date('Y-m-d H:i:s');

        $validator->mapFieldsRules([
            'room_type' => ['required', ['subset', array_column(RoomType::cases(), 'value')]],
            'checkin_date' => ['required', 'date', ['dateFormat', 'Y-m-d H:i:s'], ['dateAfter', $currentDate]],
            'checkout_date' => ['required', 'date', ['dateFormat', 'Y-m-d H:i:s'], ['dateAfter', $currentDate]],
            'table_setting' => [['subset', array_column(TableSetting::cases(), 'value')]],
            'restaurant_date' => ['date', ['dateFormat', 'Y-m-d H:i:s'], ['dateAfter', $requestData['checkin_date']], ['dateBefore', $requestData['checkout_date']]],
            'name' => ['required'],
            'email' => ['required', 'email']
        ]);

        if (!$validator->validate()) {
            $response->getBody()->write(json_encode($validator->errors()));
            return $response->withStatus(422);
        }
        
        $orderId = $this->reservationService->processOrder($requestData);

        $body = json_encode([
            'message' => 'Your reservation was successfully created.',
            'id' => $orderId
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(201);
    }

    public function findOrder(Request $request, Response $response): Response
    {
        $id = 14;

        $data = $this->orderRepository->find($id);

        $body = json_encode($data);

        $response->getBody()->write($body);

        return $response;

    }

}
