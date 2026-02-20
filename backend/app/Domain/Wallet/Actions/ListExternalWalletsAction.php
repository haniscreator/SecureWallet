<?php

namespace App\Domain\Wallet\Actions;

use App\Domain\Wallet\Services\ExternalWalletService;
use Illuminate\Database\Eloquent\Collection;

class ListExternalWalletsAction
{
    public function __construct(
        protected ExternalWalletService $externalWalletService
    ) {
    }

    public function execute(): Collection
    {
        return $this->externalWalletService->listActiveExternalWallets();
    }
}
