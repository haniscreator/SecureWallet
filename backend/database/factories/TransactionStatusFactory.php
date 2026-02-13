<?php

namespace Database\Factories;

use App\Domain\Transaction\Models\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionStatusFactory extends Factory
{
    protected $model = TransactionStatus::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word,
            'code' => $this->faker->unique()->slug,
        ];
    }
}
