<?php

namespace App\Domain\Wallet\Actions;

use App\Domain\Wallet\Models\Wallet;
use App\Domain\User\Models\User;

class AssignWalletAction
{
    public function execute(Wallet $wallet, array $userIds): void
    {
        // Sync or Attach? Let's use syncWithoutDetaching to be safe, or sync if we want full replace.
        // "Assign member to wallets" usually implies adding.
        $wallet->users()->syncWithoutDetaching($userIds);
    }
}
