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

        $auth = $this->authorization->token($userId);

        $payload = json_encode($auth);

        $response->getBody()->write($payload);

        return $response;
    }
}
