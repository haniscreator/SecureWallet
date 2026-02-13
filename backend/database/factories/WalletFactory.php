<?php

namespace Database\Factories;

use App\Domain\Wallet\Models\Wallet;
use App\Domain\Currency\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Wallet\Models\Wallet>
 */
class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'address' => Str::uuid(),
            'name' => fake()->word() . ' Wallet',
            'status' => true,
            'currency_id' => Currency::factory(),
        ];
    }

}
