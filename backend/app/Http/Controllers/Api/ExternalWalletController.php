<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Wallet\Models\ExternalWallet;
use Illuminate\Http\Request;

class ExternalWalletController extends Controller
{
    public function index()
    {
        // Return active external wallets
        return ExternalWallet::where('status', true)->get();
    }
}
