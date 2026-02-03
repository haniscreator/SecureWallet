<?php

namespace App\Domain\Wallet\Actions;

use App\Domain\Wallet\Models\Wallet;
use App\Domain\Wallet\Services\WalletService;
use App\Domain\Wallet\DataTransferObjects\WalletData;

class CreateWalletAction
{
    public function __construct(
        protected WalletService $walletService
    ) {
    }

    public function execute(WalletData $data): Wallet
    {
        return $this->walletService->create($data);
    }
}
