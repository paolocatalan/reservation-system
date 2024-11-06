<?php

declare(strict_types=1);

namespace App\RequestValidator;

use App\Enums\TableSetting;
use App\Repositories\RestaurantRepository;
use Valitron\Validator;

class StoreReservTableValidator
{
    private $errors;

    public function __construct(
        protected RestaurantRepository $restaurantRepository
    ) {}

    public function validate(array $data) {
        $validator = new Validator($data);

        $validator->rule(function($field, $value, $params, $fields) use ($data) {
            return $this->isNotAvailable($data['table_setting'], $value); 
        }, 'restaurant_date')->message($data['table_setting'] . ' is fully booked on these dates.');

        $validator->mapFieldsRules([
            'table_setting' => [['subset', array_column(TableSetting::cases(), 'value')]],
            'restaurant_date' => [['requiredWith', 'table_setting'], 'date', ['dateFormat', 'Y-m-d H:i:s']],
            'name' => ['required'],
            'email' => ['required', 'email'],
            'amount' => ['required', 'numeric'],
            'credit_card' => ['required']
        ]);

        if ($validator->validate()) {
            return $data;
        } else {
            $this->errors = $validator->errors();
            return false;
        }

    }

    public function errorBag() {
        return $this->errors;
    }

    public function isNotAvailable(string $tableSetting, string $checkDate) {
        $bookedTables = $this->restaurantRepository->getAvailability($tableSetting, $checkDate);

        $numbersOfTables = match($tableSetting) {
            'Informal Table Setting' => 10,
            'Formal Table Setting' => 10,
            'Five Course Table Setting' => 5 
        };

        if ($bookedTables >= $numbersOfTables) {
            return false;
        }

        return true;
    }
}
