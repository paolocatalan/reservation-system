<?php

declare(strict_types=1);

namespace App\Traits;

trait HttpResponses
{
    protected function error(string $message = null, array $data = null, int $code) {
        $response = new \Slim\Psr7\Response();
        $payload = json_encode([
            'status' => 'Error has occured.',
            'message' => $message,
            'data' => $data
        ]);

        $response->getBody()->write($payload);
        return $response
            ->withStatus($code);
    }
}
