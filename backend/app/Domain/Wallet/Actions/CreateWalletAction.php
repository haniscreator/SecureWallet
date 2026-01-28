<?php

namespace App\Domain\Wallet\Actions;

use App\Domain\Wallet\Models\Wallet;
use App\Domain\Wallet\Services\WalletService;

class CreateWalletAction
{
    public function __construct(
        protected WalletService $walletService
    ) {
    }

    public function execute(array $data): Wallet
    {
        return $this->walletService->create($data);
    }
}
