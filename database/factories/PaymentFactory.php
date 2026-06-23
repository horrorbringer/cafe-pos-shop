<?php

namespace Database\Factories;

use App\Domain\Ordering\Models\Order;
use App\Domain\Ordering\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'provider_code' => 'cash',
            'method' => 'cash',
            'amount' => $this->faker->randomFloat(2, 5, 100),
            'currency' => 'USD',
            'status' => 'paid',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => 'pending',
            'provider_reference' => $this->faker->uuid(),
        ]);
    }

    public function refunded(): static
    {
        return $this->state(fn () => [
            'status' => 'refunded',
        ]);
    }
}
