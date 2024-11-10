<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\UserRepository;
use App\RequestValidator\UserLoginValidator;

class AuthSessionController
{
    public function __construct(
        protected UserRepository $userRepository,
        protected UserLoginValidator $userLoginValidator
    ) {}

    public function store(Request $request, Response $response): Response 
    { 
        $data = (array) $request->getParsedBody();

        $validated = $this->userLoginValidator->validate($data);

        if (!$validated) {
            $body = json_encode($this->userLoginValidator->errorBag());
            $response->getBody()->write($body);
            return $response->withStatus(422);
        }

        $user = $this->userRepository->getByEmail($validated['email']);

        $key = $_ENV['JWT_SECRET_KEY'];

        $payload = [
            'iss' => 'localhost',
            'aud' => 'localhost',
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + (60 * 60),
            'data' => [
                'username' => $user['name'],
                'role' => 'Administrator',
            ]
        ];

        $jwt = \Firebase\JWT\JWT::encode($payload, $key, 'HS256');

        $auth = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' =>  $user['email'],
            'created_at' => $user['created_at'],
            'updated_at' => $user['updated_at'],
            'token' => $jwt
        ];

        $payload = json_encode($auth);

        $response->getBody()->write($payload);

        return $response;
    }

    public function index(Request $request, Response $response): Response
    {
        $data = $request->getAttribute('auth');

        $payload = json_encode($data);

        $response->getBody()->write($payload);

        return $response;

    }
}
