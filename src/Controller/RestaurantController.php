<?php

declare(strict_types=1);

namespace App\Controller;

use App\RequestValidator\StoreReservTableValidator;
use App\Services\InvoiceService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Services\RestaurantService;

class RestaurantController
{
    public function __construct(
        private InvoiceService $invoiceService,
        private RestaurantService $restaurantService,
        private StoreReservTableValidator $validator
    ) {}

    public function store(Request $request, Response $response): Response
    {
        $validated = $this->validator->validate($request->getParsedBody());

        if (!$validated) {
            $body = json_encode($this->validator->errorBag());
            $response->getBody()->write($body);

            return $response->withStatus(422);
        } 

        $invoice = $this->invoiceService->process($validated);
        
        $data = $this->restaurantService->add($validated, $invoice['id']);

        $content = json_encode([
            'message' => 'Your table is reserved.',
            'invoice number' => $invoice['id'],
            'reservation details' => $data
        ]);

        $response->getBody()->write($content);

        return $response->withStatus(201);
    }
}
