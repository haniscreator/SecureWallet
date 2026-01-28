<?php

namespace App\Domain\Wallet\Services;

use App\Domain\Wallet\Models\Wallet;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Actions\CreateWalletAction;
use App\Domain\Wallet\Actions\UpdateWalletStatusAction;
use App\Domain\Wallet\Actions\AssignWalletAction;

class WalletService
{
    public function listWallets(User $user)
    {
        if ($user->role === 'admin') {
            return Wallet::all();
        }

        // Return only assigned wallets for users
        return $user->wallets;
    }

    public function getWallet(Wallet $wallet)
    {
        // Add relationships if needed, e.g. users
        return $wallet->load('users');
    }

    public function create(\App\Domain\Wallet\DataTransferObjects\WalletData $data): Wallet
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $wallet = Wallet::create([
                'name' => $data->name,
                'currency_id' => $data->currency_id,
                'status' => $data->status ?? true,
            ]);

            // Handle Initial Balance by creating a 'credit' transaction
            if ($data->initial_balance && $data->initial_balance > 0) {
                \App\Domain\Wallet\Models\Transaction::create([
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

    public function update(Wallet $wallet, \App\Domain\Wallet\DataTransferObjects\WalletData $data): Wallet
    {
        $wallet->update($data->toArray());
        return $wallet;
    }
}
