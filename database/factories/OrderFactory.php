<?php

namespace Database\Factories;

use App\Domain\Ordering\Models\Order;
use App\Domain\Shared\Enums\OrderStatus;
use App\Domain\Shared\Enums\OrderType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'order_number' => Order::generateOrderNumber(),
            'user_id' => User::factory(),
            'order_type' => OrderType::DineIn,
            'subtotal' => 0,
            'discount' => 0,
            'tax' => 0,
            'total' => 0,
            'status' => OrderStatus::Draft,
            'amount_paid' => 0,
            'change_amount' => 0,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => OrderStatus::Draft,
            'subtotal' => 0,
            'total' => 0,
            'amount_paid' => 0,
            'change_amount' => 0,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => OrderStatus::Pending,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'status' => OrderStatus::Paid,
            'amount_paid' => 50,
            'total' => 45,
            'change_amount' => 5,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => OrderStatus::Completed,
            'amount_paid' => 50,
            'total' => 45,
            'change_amount' => 5,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => OrderStatus::Cancelled,
        ]);
    }
}
