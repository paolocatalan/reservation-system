<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Traits\HttpResponses;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UnexpectedValueException;

class AuthMiddleware implements MiddlewareInterface
{
    use HttpResponses;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authHeader = $request->getHeader('Authorization');

        if (!$authHeader) {
            return $this->error('Unauthorized.', null, 403);
        }

        $token = preg_replace('/^Bearer\s*/', '', $authHeader[0]);
        $key = $_ENV['JWT_SECRET_KEY'];

        try {
            $decode = \Firebase\JWT\JWT::decode($token, new Key($key, 'HS256'));
        } catch (UnexpectedValueException | ExpiredException $e) {
            return $this->error($e->getMessage(), null, 401);
        }

        $request = $request->withAttribute('auth', $decode);

        return $handler->handle($request);
    }

}
