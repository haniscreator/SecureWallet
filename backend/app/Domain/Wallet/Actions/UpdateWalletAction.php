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

    public function execute(Wallet $wallet, \App\Domain\Wallet\DataTransferObjects\WalletData $data): Wallet
    {
        return $this->walletService->update($wallet, $data);
    }
}
