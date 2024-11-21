<?php

declare(strict_types=1);

namespace App\RequestValidator;

use App\Enums\RoomType;
use App\Enums\TableSetting;
use App\Repositories\RestaurantRepository;
use App\Repositories\RoomRepository;
use Valitron\Validator;

class StoreOrderValidator
{
    protected $errors = [];

    public function __construct(
        protected RoomRepository $roomRepository,
        protected RestaurantRepository $restaurantRepository
    ) {}

    public function validate(array $data): array|bool
    {
        $validator = new Validator($data);

        $validator->mapFieldsRules([
            'room_type' => ['required', ['subset', array_column(RoomType::cases(), 'value')]],
            'checkin_date' => ['required', 'date', ['dateFormat', 'Y-m-d H:i:s'], ['dateAfter', date('Y-m-d H:i:s')]],
            'checkout_date' => ['required', 'date', ['dateFormat', 'Y-m-d H:i:s'], ['dateAfter', date('Y-m-d H:i:s')]],
            'seats' => ['numeric'], // add a validation for max and min, currently its returning string
            'table_setting' => [['requiredWith', 'seats'], ['subset', array_column(TableSetting::cases(), 'value')]],
            'restaurant_date' => [['requiredWith', 'seats'], 'date', ['dateFormat', 'Y-m-d H:i:s'], ['dateAfter', $data['checkin_date']], ['dateBefore', $data['checkout_date']]],
            'name' => ['required'],
            'email' => ['required', 'email'],
            'amount' => ['required', 'numeric'],
            'credit_card' => ['required']
        ]);

        if ($data['room_type']) {
            $validator->rule(function($field, $value, $params, $fields) use ($data) {
                return $this->isFullyBooked($data['room_type'], $value); 
            }, 'checkin_date')->message($data['room_type'] . ' is fully booked on these dates.');
        }

        if ($data['restaurant_date']) {
            $validator->rule(function($field, $value, $params, $fields) use ($data) {
                return $this->isNotAvailable($value, (int) $data['seats']); 
            }, 'restaurant_date')->message('No available seat for your date.');
        }

        if ($validator->validate()) {
            // we should return the validated inputs not the data from the agruments
            return $data;
        } else {
            $this->errors = $validator->errors();
            return false;
        }
    }

    public function errorBag(): array {
        return $this->errors;
    }

    private function isFullyBooked(string $roomType, string $date): bool
    {
        $bookedRooms = $this->roomRepository->getAvailability($roomType, $date);

        $numbersOfRoom = match($roomType) {
            'Cabana' => 10,
            'Villa' => 5,
            'Penthouse' => 2 
        };

        if ($bookedRooms >= $numbersOfRoom) {
            return false;
        }

        return true;
    }

    private function isNotAvailable(string $startTime, int $seats): bool {
        $endTime = date('Y-m-d H:i:s', strtotime('+8 hours', strtotime($startTime)));

        $bookedSeats = $this->restaurantRepository->getReseverdSeats($startTime, $endTime);

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