<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\InvoiceService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Services\RestaurantService;

class RestaurantController
{
    public function __construct(
        private InvoiceService $invoiceService,
        private RestaurantService $restaurantService,
    ) {}

    public function store(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();

        $invoice = $this->invoiceService->process($body);
        
        $data = $this->restaurantService->add($body, $invoice['id']);

        $content = json_encode([
            'message' => 'Your table is reserved.',
            'reservation_details' => $data
        ]);

        $response->getBody()->write($content);

        return $response->withStatus(201);
    }
}
