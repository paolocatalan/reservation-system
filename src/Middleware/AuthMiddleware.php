<?php

declare(strict_types = 1);

namespace App\Middleware;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UnexpectedValueException;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authHeader = $request->getHeader('Authorization');

        if (!$authHeader) {
            $response = new \Slim\Psr7\Response();
            $payload = json_encode([
                'message' => 'Unauthorized.'
            ]);

            $response->getBody()->write($payload);
            return $response
                ->withStatus(401);
        }

        $token = preg_replace('/^Bearer\s*/', '', $authHeader[0]);
        $key = $_ENV['JWT_SECRET_KEY'];

        try {
            $decode = \Firebase\JWT\JWT::decode($token, new Key($key, 'HS256'));
        } catch (UnexpectedValueException | ExpiredException $e) {
            $response = new \Slim\Psr7\Response();
            $payload = json_encode([
                'message' => $e->getMessage()
            ]);

            $response->getBody()->write($payload);
            return $response
                ->withStatus(401);

        }

        $request = $request->withAttribute('auth', $decode);

        return $handler->handle($request);
    }

}
