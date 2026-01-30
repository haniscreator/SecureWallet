<?php

namespace App\Domain\Wallet\Services;

use App\Domain\Wallet\Models\Transaction;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\User\Models\User;

class TransactionService
{
    public function listTransactions(Wallet $wallet, array $filters = [])
    {
        $query = Transaction::where(function ($q) use ($wallet) {
            $q->where('from_wallet_id', $wallet->id)
                ->orWhere('to_wallet_id', $wallet->id);
        });

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        if (!empty($filters['reference'])) {
            $query->where('reference', 'like', '%' . $filters['reference'] . '%');
        }

        return $query->latest()->paginate(15);
    }
    public function listAllTransactions(User $user, array $filters = [])
    {
        $query = Transaction::query();

        // If not admin, restrict to user's wallets
        if ($user->role !== 'admin') {
            $query->where(function ($q) use ($user) {
                $q->whereHas('fromWallet', function ($w) use ($user) {
                    $w->whereHas('users', function ($u) use ($user) {
                        $u->where('users.id', $user->id);
                    });
                })->orWhereHas('toWallet', function ($w) use ($user) {
                    $w->whereHas('users', function ($u) use ($user) {
                        $u->where('users.id', $user->id);
                    });
                });
            });
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        if (!empty($filters['reference'])) {
            $query->where('reference', 'like', '%' . $filters['reference'] . '%');
        }

        return $query->latest()->paginate(15);
    }
}
