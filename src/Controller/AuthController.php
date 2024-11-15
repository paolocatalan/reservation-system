<?php

declare(strict_types=1);

namespace App\Controller;

use App\Auth\Authorization;
use App\Repositories\UserRepository;
use App\RequestValidator\UserRegistrationValidator;
use App\Traits\HttpResponses;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AuthController
{
    use HttpResponses;

    public function __construct(
        protected UserRepository $userRepository,
        protected UserRegistrationValidator $userRegistrationValidator,
        protected Authorization $authorization
    ) {}

    public function store(Request $request, Response $response): Response
    { 
        $data = (array) $request->getParsedBody();

        $validated = $this->userRegistrationValidator->validate($data);

        if (!$validated) {
            return $this->error('There was a problem with your submission.', $this->userRegistrationValidator->errorBag(), 422);
        }

        $userId = $this->userRepository->create($validated);

        $user = $this->userRepository->getById($userId);

        $payload = json_encode([
            'user' => $user['name'],
            'email' => $user['email'],
            'created_at' => $user['created_at'],
            'updated_at' => $user['updated_at'],
            'token' => $this->authorization->token($user['id'])
        ]);

        $response->getBody()->write($payload);

        return $response;
    }
}
