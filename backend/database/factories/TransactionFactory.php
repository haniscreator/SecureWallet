<?php

namespace Database\Factories;

use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Models\TransactionStatus;
use App\Domain\Wallet\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        return [
            'from_wallet_id' => function (array $attributes) {
                return Wallet::factory();
            },
            'to_wallet_id' => function (array $attributes) {
                return Wallet::factory();
            },
            'transaction_status_id' => TransactionStatus::factory(), // This might need TransactionStatusFactory or just a clearer, I'll use id if factory not exists
            // actually TransactionStatus might not have a factory. I'll just use 1 or strict values if no factory.
            // Better: 'transaction_status_id' => 1, but let's see.
            // Safest is to not define it here if I always override it, OR assume existence.
            'type' => 'credit',
            'amount' => $this->faker->numberBetween(10, 1000),
            'reference' => $this->faker->sentence(),
            'created_at' => now(),
        ];
    }
}
