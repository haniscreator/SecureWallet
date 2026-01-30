<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Wallet\Models\Wallet>
 */
class WalletFactory extends Factory
{
    protected $model = \App\Domain\Wallet\Models\Wallet::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word() . ' Wallet',
            'status' => true,
            'currency_id' => \App\Domain\Currency\Models\Currency::factory(),
        ];
    }
}
