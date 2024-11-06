<?php

declare(strict_types=1);

namespace App\RequestValidator;

use App\Enums\RoomType;
use App\Enums\TableSetting;
use App\Repositories\RoomRepository;
use Valitron\Validator;

class CreateOrderValidator
{
    protected $roomRepository;
    protected $validator;
    protected $data;
    protected $errors = [];

    public function __construct(RoomRepository $roomRepository, array $data)
    {
        $this->roomRepository = $roomRepository;
        $this->data = $data;
        $this->validator = new Validator($data);
    }

    public function validate(): array|bool
    {
        $this->validator->rule(function($field, $value, $params, $fields) {
            return $this->isFullyBooked($value); 
        }, 'checkin_date')->message($this->data['room_type'] . ' is fully booked on these dates.');

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
            // we should return the validated inputs not the data from the agruments
            return $this->data;
        } else {
            $this->errors = $this->validator->errors();
            return false;
        }
    }

    public function errorBag() {
        return $this->errors;
    }

    public function isFullyBooked(string $date): bool {
        $bookedRooms = $this->roomRepository->getAvailability($this->data['room_type'], $date);

        $numbersOfRoom = match($this->data['room_type']) {
            'Cabana' => 10,
            'Villa' => 5,
            'Penthouse' => 2 
        };

        if ($bookedRooms >= $numbersOfRoom) {
            return false;
        }

        return true;
    }
}
