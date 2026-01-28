<?php

namespace App\Domain\Wallet\Actions;

use App\Domain\Wallet\Models\Wallet;
use App\Domain\Wallet\Services\WalletService;

class UpdateWalletAction
{
    public function __construct(
        protected WalletService $walletService
    ) {
    }

    public function execute(Wallet $wallet, array $data): Wallet
    {
        return $this->walletService->update($wallet, $data);
    }
}
