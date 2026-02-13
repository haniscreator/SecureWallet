<?php

namespace Database\Seeders;

use App\Domain\Transaction\Models\TransactionStatus;
use Illuminate\Database\Seeder;

class TransactionStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Pending', 'code' => 'pending'],
            ['name' => 'Completed', 'code' => 'completed'],
            ['name' => 'Rejected', 'code' => 'rejected'],
            ['name' => 'Cancelled', 'code' => 'cancelled'],
        ];

        foreach ($statuses as $status) {
            TransactionStatus::create($status);
        }
    }
}
