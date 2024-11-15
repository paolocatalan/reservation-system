<?php

declare(strict_types=1);

namespace App\Auth;

use App\Repositories\UserRepository;
use Firebase\JWT\JWT;

class Authorization
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function token(int $userId): string
    {
        $key = $_ENV['JWT_SECRET_KEY'];

        $user = $this->userRepository->getById($userId);

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

        return JWT::encode($payload, $key, 'HS256');
    }
}
