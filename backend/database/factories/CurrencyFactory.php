<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Currency\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    protected $model = \App\Domain\Currency\Models\Currency::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->currencyCode(),
            'name' => fake()->currencyCode() . ' Name',
            'symbol' => '$',
        ];
    }
}
