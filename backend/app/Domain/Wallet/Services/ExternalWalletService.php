<?php

namespace App\Domain\Wallet\Services;

use App\Domain\Wallet\Models\ExternalWallet;
use Illuminate\Database\Eloquent\Collection;

class ExternalWalletService
{
    public function listActiveExternalWallets(): Collection
    {
        return ExternalWallet::where('status', true)->get();
    }
}
