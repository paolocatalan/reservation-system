<?php

declare(strict_types=1);

namespace App\Controller;

use App\RequestValidator\StoreReservTableValidator;
use App\Services\InvoiceService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Services\RestaurantService;
use App\Traits\HttpResponses;

class RestaurantController
{
    use HttpResponses;

    public function __construct(
        private InvoiceService $invoiceService,
        private RestaurantService $restaurantService,
        private StoreReservTableValidator $validator,
    ) {}

    public function store(Request $request, Response $response): Response
    {
        $validated = $this->validator->validate($request->getParsedBody());

        if (!$validated) {
            return $this->error(null, $this->validator->errorBag(), 422);
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
