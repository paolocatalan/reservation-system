<?php

declare(strict_types=1);

namespace App\RequestValidator;

use App\Enums\TableSetting;
use App\Repositories\RestaurantRepository;
use Valitron\Validator;

class StoreReservTableValidator
{
    private $errors = [];

    public function __construct(
        protected RestaurantRepository $restaurantRepository
    ) {}

    public function validate(array $data): array|bool
    {
        $validator = new Validator($data);

        $validator->mapFieldsRules([
            'seats' => ['required', 'numeric'], // add a validation for max and min
            'table_setting' => ['required', ['subset', array_column(TableSetting::cases(), 'value')]],
            'restaurant_date' => ['required', 'date', ['dateFormat', 'Y-m-d H:i:s'], ['dateAfter', date('Y-m-d H:i:s')]],
            'name' => ['required'],
            'email' => ['required', 'email'],
            'amount' => ['required', 'numeric'],
            'credit_card' => ['required']
        ]);

        if ($data['restaurant_date']) {
            $validator->rule(function($field, $value, $params, $fields) use ($data) {
                return $this->isNotAvailable($value, (int) $data['seats']); 
            }, 'restaurant_date')->message('No available seat for your date.');
        }

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

    private function isNotAvailable(string $startTime, int $seats): bool {
        $endTime = date('Y-m-d H:i:s', strtotime('+8 hours', strtotime($startTime)));

        $bookedSeats = $this->restaurantRepository->getAllReservSeats($startTime, $endTime);

        $numberOfSeats = 0;
        foreach ($bookedSeats as $item) {
            $numberOfSeats += $item['seats'];
        }

        if ($numberOfSeats + $seats >= 20) {
            return false;
        }

        return true;
    }

}
