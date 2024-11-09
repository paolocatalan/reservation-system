<?php

declare(strict_types=1);

namespace App\RequestValidator;

use App\Repositories\UserRepository;
use Valitron\Validator;

class UserLoginValidator
{
    protected $errors = [];

    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function validate(array $data): array|bool
    {
        $validator = new Validator($data);

        $validator->mapFieldsRules([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $validator->rule(function($field, $value, $params, $fields) use ($data) {
            return ($this->invalidCredentials($data['email'], $value));
        }, 'password')->message('Invalid credentials.');

        if ($validator->validate()) {
            return $data;
        } else {
            $this->errors = $validator->errors();
            return false;
        }
    }

    public function errorBag(): array {
        return $this->errors;
    }

    public function invalidCredentials(string $email, $password): bool {
        $user = $this->userRepository->getByEmail($email);

        if (!$user || password_verify($password, $user['password'])) {
            return true;
        }

        return false;
    }

}
