<?php

namespace App\Domain\Wallet\Services;

use App\Domain\Wallet\Models\Transaction;
use App\Domain\Wallet\Models\Wallet;

class TransactionService
{
    public function listTransactions(Wallet $wallet, array $filters = [])
    {
        $query = Transaction::where(function ($q) use ($wallet) {
            $q->where('from_wallet_id', $wallet->id)
                ->orWhere('to_wallet_id', $wallet->id);
        });

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        return $query->latest()->paginate(15);
    }
}
