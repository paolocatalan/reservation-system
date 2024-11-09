<?php

declare(strict_types=1);

namespace App\RequestValidator;

use App\Repositories\UserRepository;
use Valitron\Validator;

class UserRegistrationValidator
{
    protected $errors = [];

    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function validate(array $data): array|bool
    {
        $validator = new Validator($data);

        $validator->mapFieldsRules([
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $validator->rule(function($field, $value, $params, $fields) {
            return ($this->userRepository->getByEmail($value)) ? false : true;
        }, 'email')->message('Email address already exist.');

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

}
