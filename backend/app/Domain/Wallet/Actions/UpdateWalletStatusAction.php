<?php

namespace App\Domain\Wallet\Actions;

use App\Domain\Wallet\Models\Wallet;
use App\Domain\Wallet\Services\WalletService;

class UpdateWalletStatusAction
{
    public function __construct(
        protected WalletService $walletService
    ) {
    }

    public function execute(Wallet $wallet, string|int|bool $status): Wallet
    {
        return $this->walletService->updateStatus($wallet, $status);
    }
}
