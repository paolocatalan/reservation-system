<?php

declare(strict_types=1);

namespace App\Controller;

use App\Auth\Authorization;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\UserRepository;
use App\RequestValidator\UserLoginValidator;
use App\Traits\HttpResponses;

class SessionController
{
    use HttpResponses;

    public function __construct(
        protected UserRepository $userRepository,
        protected UserLoginValidator $userLoginValidator,
        protected Authorization $authorization
    ) {}

    public function store(Request $request, Response $response): Response 
    { 
        $data = (array) $request->getParsedBody();

        $validated = $this->userLoginValidator->validate($data);

        if (!$validated) {
            return $this->error('There was a problem with your submission.', $this->userLoginValidator->errorBag(), 422);
        }

        $user = $this->userRepository->getByEmail($validated['email']);

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
