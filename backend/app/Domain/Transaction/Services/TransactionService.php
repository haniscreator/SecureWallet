<?php

namespace App\Domain\Transaction\Services;

use App\Domain\Transaction\DataTransferObjects\TransactionFilterData;
use App\Domain\Transaction\Models\Transaction;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\Wallet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class TransactionService
{
    public function listTransactions(Wallet $wallet, TransactionFilterData $filters): LengthAwarePaginator
    {
        $query = Transaction::where(function ($q) use ($wallet) {
            $q->where('from_wallet_id', $wallet->id)
                ->orWhere('to_wallet_id', $wallet->id);
        });

        if (!empty($filters->type)) {
            $query->where('type', $filters->type);
        }

        if (!empty($filters->from_date)) {
            $start = Carbon::parse($filters->from_date, $filters->timezone)->startOfDay()->setTimezone('UTC');
            $query->where('created_at', '>=', $start);
        }

        if (!empty($filters->to_date)) {
            $end = Carbon::parse($filters->to_date, $filters->timezone)->endOfDay()->setTimezone('UTC');
            $query->where('created_at', '<=', $end);
        }

        if (!empty($filters->reference)) {
            $query->where('reference', 'like', '%' . $filters->reference . '%');
        }

        return $query->orderBy($filters->sort_by ?? 'created_at', $filters->sort_dir ?? 'desc')
            ->paginate($filters->per_page ?? 15);
    }

    public function listAllTransactions(User $user, TransactionFilterData $filters): LengthAwarePaginator
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

        if (!empty($filters->type)) {
            $query->where('type', $filters->type);
        }

        if (!empty($filters->from_date)) {
            $start = Carbon::parse($filters->from_date, $filters->timezone)->startOfDay()->setTimezone('UTC');
            $query->where('created_at', '>=', $start);
        }

        if (!empty($filters->to_date)) {
            $end = Carbon::parse($filters->to_date, $filters->timezone)->endOfDay()->setTimezone('UTC');
            $query->where('created_at', '<=', $end);
        }

        if (!empty($filters->reference)) {
            $query->where('reference', 'like', '%' . $filters->reference . '%');
        }

        if (!empty($filters->status_id)) {
            $query->where('transaction_status_id', $filters->status_id);
        }

        return $query->orderBy($filters->sort_by ?? 'created_at', $filters->sort_dir ?? 'desc')
            ->paginate($filters->per_page ?? 15);
    }

    public function getAggregatedBalances(User $user)
    {
        // 1. Fetch user's wallets
        // Allow admin to see all wallets
        $query = ($user->role === 'admin')
            ? Wallet::query()
            : $user->wallets();

        $wallets = $query->with('currency')->get();

        $totals = [];

        $wallets->each(function ($wallet) use (&$totals) {
            // Calculate Balance using Model Logic (Status = Completed only)
            $balance = $wallet->balance;

            $currencyCode = $wallet->currency?->code ?? ($wallet->currency_id === 2 ? 'EUR' : 'USD');
            $currencySymbol = $wallet->currency?->symbol ?? '$';

            if (!isset($totals[$currencyCode])) {
                $totals[$currencyCode] = [
                    'amount' => 0,
                    'symbol' => $currencySymbol
                ];
            }

            // If symbol was fallback, update it
            if ($totals[$currencyCode]['symbol'] === '$' && $currencySymbol !== '$') {
                $totals[$currencyCode]['symbol'] = $currencySymbol;
            }

            $totals[$currencyCode]['amount'] += $balance;
        });

        return $totals;
    }
}
