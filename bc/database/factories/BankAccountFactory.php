<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BankAccount>
 */
class BankAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' - Conta Corrente',
            'bank' => $this->faker->randomElement(['Itaú', 'Bradesco', 'Santander', 'Banco do Brasil', 'Caixa']),
            'agency' => $this->faker->numerify('####'),
            'account' => $this->faker->numerify('#####-#'),
            'account_number' => $this->faker->numerify('#####-#'),
            'type' => $this->faker->randomElement(['checking', 'savings']),
            'balance' => $this->faker->randomFloat(2, 0, 10000),
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
