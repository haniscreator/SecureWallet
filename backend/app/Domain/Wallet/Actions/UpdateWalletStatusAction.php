<?php

namespace App\Domain\Wallet\Actions;

use App\Domain\Wallet\Models\Wallet;
use InvalidArgumentException;

class UpdateWalletStatusAction
{
    public function execute(Wallet $wallet, string $status): Wallet
    {
        if (!in_array($status, ['active', 'frozen'])) {
            throw new InvalidArgumentException("Invalid status: {$status}");
        }

        $wallet->update(['status' => $status]);

        return $wallet;
    }
}
