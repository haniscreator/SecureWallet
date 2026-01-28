<?php

namespace App\Domain\Wallet\Actions;

use App\Domain\Wallet\Models\Wallet;
use App\Domain\Wallet\Services\WalletService;

class GetWalletAction
{
    public function __construct(
        protected WalletService $walletService
    ) {
    }

    public function execute(Wallet $wallet): Wallet
    {
        return $this->walletService->getWallet($wallet);
    }
}
