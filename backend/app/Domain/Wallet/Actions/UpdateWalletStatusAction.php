<?php

namespace App\Domain\Wallet\Actions;

use App\Domain\Wallet\Models\Wallet;
use InvalidArgumentException;

class UpdateWalletStatusAction
{
    public function execute(Wallet $wallet, string|int|bool $status): Wallet
    {
        $wallet->update(['status' => (bool) $status]);

        return $wallet;
    }
}
