<?php

namespace Database\Factories\Transactions;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transactions\Transaction>
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
            'transaction_type' => fake()->randomElement([TransactionType::Income, TransactionType::Expense]),
            'amount' => fake()->numberBetween(1000, 125000),
            'date' => fake()->dateTimeBetween(now()->startOfYear(), now()->endOfYear()),
            'finished' => fake()->boolean(),
            'description' => fake()->realText(26),
        ];
    }
}
