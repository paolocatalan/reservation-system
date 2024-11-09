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

        $payload = json_encode($user);

        $response->getBody()->write($payload);

        return $response;
    }
}
