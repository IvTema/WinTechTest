<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'balance_id'=> rand(1,15),
            'transaction_type'=> $this->faker->randomElement(['debit', 'credit']),
            'amount'=> rand(1,2000),
            'currency'=> $this->faker->randomElement(['usd', 'rub']),
            'issue'=> $this->faker->randomElement(['refund', 'stock', 'renunciation']),
        ];
    }
}
