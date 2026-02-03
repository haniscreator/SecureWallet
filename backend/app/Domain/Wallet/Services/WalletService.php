<?php

namespace App\Domain\Wallet\Services;

use App\Domain\Wallet\Models\Wallet;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Actions\CreateWalletAction;
use App\Domain\Wallet\Actions\UpdateWalletStatusAction;
use App\Domain\Wallet\Actions\AssignWalletAction;
use App\Domain\Wallet\DataTransferObjects\WalletData;
use Illuminate\Support\Facades\DB;

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

        return $query->with('currency')->withCount('users')->with('users')->get();
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
            ]);

            // Handle Initial Balance by creating a 'credit' transaction
            if ($data->initial_balance && $data->initial_balance > 0) {
                \App\Domain\Transaction\Models\Transaction::create([
                    'to_wallet_id' => $wallet->id,
                    'type' => 'credit',
                    'amount' => $data->initial_balance,
                    'reference' => 'Initial Balance',
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
}
