<?php

namespace App\Domain\Wallet\Actions;

use App\Domain\User\Models\User;
use App\Domain\Wallet\Services\WalletService;
use Illuminate\Database\Eloquent\Collection;

class ListWalletsAction
{
    public function __construct(
        protected WalletService $walletService
    ) {
    }

    public function execute(User $user): Collection
    {
        return $this->walletService->listWallets($user);
    }
}
