<?php

declare(strict_types=1);

namespace App\RequestValidator;

use App\Enums\RoomType;
use App\Enums\TableSetting;
use Valitron\Validator;

class CreateOrderValidator
{
    protected $validator;
    protected $data;
    protected $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->validator = new Validator($data);
    }

    public function validate(): array|bool
    {
        $this->validator->rule('required', ['room_type', 'checkin_date', 'checkout_date', 'name', 'email']);

        $this->validator->mapFieldsRules([
            'room_type' => ['required', ['subset', array_column(RoomType::cases(), 'value')]],
            'checkin_date' => ['required', 'date', ['dateFormat', 'Y-m-d H:i:s'], ['dateAfter', date('Y-m-d H:i:s')]],
            'checkout_date' => ['required', 'date', ['dateFormat', 'Y-m-d H:i:s'], ['dateAfter', date('Y-m-d H:i:s')]],
            'table_setting' => [['subset', array_column(TableSetting::cases(), 'value')]],
            'restaurant_date' => [['requiredWith', 'table_setting'], 'date', ['dateFormat', 'Y-m-d H:i:s'], ['dateAfter', $this->data['checkin_date']], ['dateBefore', $this->data['checkout_date']]],
            'name' => ['required'],
            'email' => ['required', 'email'],
            'amount' => ['required', 'numeric'],
            'credit_card' => ['required']
        ]);

        if ($this->validator->validate()) {
            return $this->data;
        } else {
            $this->errors = $this->validator->errors();
            return false;
        }
    }

    public function errorBag() {
        return $this->errors;
    }
}
