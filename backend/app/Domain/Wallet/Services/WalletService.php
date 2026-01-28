<?php

namespace App\Domain\Wallet\Services;

use App\Domain\Wallet\Models\Wallet;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Actions\CreateWalletAction;
use App\Domain\Wallet\Actions\UpdateWalletStatusAction;
use App\Domain\Wallet\Actions\AssignWalletAction;

class WalletService
{
    public function __construct(
        protected CreateWalletAction $createWalletAction,
        protected UpdateWalletStatusAction $updateWalletStatusAction,
        protected AssignWalletAction $assignWalletAction
    ) {
    }

    public function createWallet(array $data)
    {
        return $this->createWalletAction->execute($data);
    }

    public function updateWalletStatus(Wallet $wallet, string $status)
    {
        return $this->updateWalletStatusAction->execute($wallet, $status);
    }

    public function assignWalletToUser(Wallet $wallet, array $userIds)
    {
        $this->assignWalletAction->execute($wallet, $userIds);
    }

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
}
