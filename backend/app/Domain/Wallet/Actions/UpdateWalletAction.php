<?php

namespace App\Domain\Wallet\Actions;

use App\Domain\Wallet\Models\Wallet;
use App\Domain\Wallet\Services\WalletService;
use App\Domain\Wallet\DataTransferObjects\WalletData;

class UpdateWalletAction
{
    public function __construct(
        protected WalletService $walletService
    ) {
    }

    public function execute(Wallet $wallet, WalletData $data): Wallet
    {
        return $this->walletService->update($wallet, $data);
    }
}
