<?php

namespace App\Domain\Wallet\Services;

use App\Domain\Wallet\Models\Wallet;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Actions\CreateWalletAction;
use App\Domain\Wallet\Actions\UpdateWalletStatusAction;
use App\Domain\Wallet\Actions\AssignWalletAction;
use App\Domain\Wallet\DataTransferObjects\WalletData;
use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Models\TransactionStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletService
{
    public function listWallets(User $user, array $filters = [])
    {
        $query = ($user->role === 'admin')
            ? Wallet::query()
            : $user->wallets()->getQuery();

        $query->when(isset($filters['name']), function ($q) use ($filters) {
            $q->where('name', 'like', '%' . $filters['name'] . '%');
        });

        $query->when(isset($filters['currency_id']), function ($q) use ($filters) {
            $q->where('currency_id', $filters['currency_id']);
        });

        $query->when(isset($filters['status']), function ($q) use ($filters) {
            // Check if status is explicitly not null (to allow 0/false)
            if ($filters['status'] !== null && $filters['status'] !== '') {
                $q->where('status', filter_var($filters['status'], FILTER_VALIDATE_BOOLEAN));
            }
        });

        $wallets = $query->with('currency')->withCount('users')->with('users')->get();

        // Append balance to each wallet
        $wallets->each(function ($wallet) {
            $wallet->append('balance');
        });

        return $wallets;
    }

    public function getWallet(Wallet $wallet)
    {
        // Add relationships if needed, e.g. users
        return $wallet->load('users');
    }

    public function create(WalletData $data): Wallet
    {
        return DB::transaction(function () use ($data) {
            $wallet = Wallet::create([
                'name' => $data->name,
                'currency_id' => $data->currency_id,
                'status' => $data->status ?? true,
                'address' => (string) Str::uuid(),
            ]);

            // Handle Initial Balance by creating a 'credit' transaction
            if ($data->initial_balance && $data->initial_balance > 0) {
                $completedStatus = TransactionStatus::where('code', 'completed')->first();

                Transaction::create([
                    'to_wallet_id' => $wallet->id,
                    'type' => 'credit',
                    'amount' => $data->initial_balance,
                    'reference' => 'Initial Balance',
                    'transaction_status_id' => $completedStatus?->id,
                ]);
            }

            // Reload to get relationship if needed
            $wallet->load('currency');

            return $wallet;
        });
    }

    public function updateStatus(Wallet $wallet, string|int|bool $status): Wallet
    {
        $wallet->update(['status' => (bool) $status]);
        return $wallet;
    }

    public function assignUsers(Wallet $wallet, array $userIds): void
    {
        $wallet->users()->syncWithoutDetaching($userIds);
    }

    public function update(Wallet $wallet, WalletData $data): Wallet
    {
        $wallet->update($data->toArray());
        return $wallet;
    }

    public function getWalletsForDashboard(User $user, int $limit = 3)
    {
        // 1. Fetch user's wallets
        // Fix ambiguous column 'created_at' by specifying table name
        // Allow admin to see all wallets
        $query = ($user->role === 'admin')
            ? Wallet::query()
            : $user->wallets();

        $wallets = $query->withCount('users')->latest('wallets.created_at')->take($limit)->get();

        $wallets->load('currency');

        $wallets->each(function ($wallet) {
            // Assign to dashboard_balance to match Wallet Model logic
            // We use the model's accessor logic but eager load it here to avoid N+1 if we were iterating differently
            // Actually, since we already have the wallet model, we can just use its relation methods if we eager load them properly
            // But here we are fetching raw transactions to calculate.

            // Let's use the relation logic with filtering
            $credits = $wallet->incomingTransactions()
                ->whereHas('status', function ($q) {
                    $q->where('code', 'completed');
                })
                ->sum('amount');

            $debits = $wallet->outgoingTransactions()
                ->whereHas('status', function ($q) {
                    $q->where('code', 'completed');
                })
                ->sum('amount');

            $balance = $credits - $debits;

            $wallet->setAttribute('dashboard_balance', $balance);
        });

        return $wallets;
    }
}
