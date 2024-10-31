<?php

declare(strict_types=1);

namespace App\RequestValidator;

use App\Enums\RoomType;
use App\Enums\TableSetting;
use Valitron\Validator;

class CreateOrderValidator
{
    protected $data;
    protected $errors = [];

    public function __construct(array $data)
    {
       $this->data = $data; 
    }

    public function validate(): bool
    {

        $validator = new Validator($this->data);

        $validator->rule('required', ['room_type', 'checkin_date', 'checkout_date', 'name', 'email']);

        $currentDate = date('Y-m-d H:i:s');

        $validator->mapFieldsRules([
            'room_type' => ['required', ['subset', array_column(RoomType::cases(), 'value')]],
            'checkin_date' => ['required', 'date', ['dateFormat', 'Y-m-d H:i:s'], ['dateAfter', $currentDate]],
            'checkout_date' => ['required', 'date', ['dateFormat', 'Y-m-d H:i:s'], ['dateAfter', $currentDate]],
            'table_setting' => [['subset', array_column(TableSetting::cases(), 'value')]],
            'restaurant_date' => [['requiredWith', 'table_setting'], 'date', ['dateFormat', 'Y-m-d H:i:s'], ['dateAfter', $this->data['checkin_date']], ['dateBefore', $this->data['checkout_date']]],
            'name' => ['required', 'alpha'],
            'email' => ['required', 'email']
        ]);

        if ($validator->validate()) {
            return true;
        } else {
            $this->errors = $validator->errors();
            return false;
        }
    }

    public function errorBag() {
        return $this->errors;
    }
}
