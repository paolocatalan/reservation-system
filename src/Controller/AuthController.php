<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\UserRepository;
use App\RequestValidator\UserRegistrationValidator;
use App\Traits\HttpResponses;

class AuthController
{
    use HttpResponses;

    public function __construct(
        protected UserRepository $userRepository,
        protected UserRegistrationValidator $userRegistrationValidator
    ) {}

    public function store(Request $request, Response $response): Response
    { 
        $data = (array) $request->getParsedBody();

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);

        $validated = $this->userRegistrationValidator->validate($data);

        if (!$validated) {
           return $this->error(null, $this->userRegistrationValidator->errorBag(), 422);
        }

        $userId = $this->userRepository->create($data);

        $payload = json_encode($userId);

        $response->getBody()->write($payload);

        return $response;
    }
}
