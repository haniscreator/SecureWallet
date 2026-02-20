<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Http\Controllers\Controller;
use App\Domain\Wallet\Actions\ListExternalWalletsAction;
use App\Domain\Wallet\Resources\ExternalWalletResource;
use Illuminate\Http\Request;

class ExternalWalletController extends Controller
{
    public function __construct(
        protected ListExternalWalletsAction $listExternalWalletsAction
    ) {
    }

    public function index()
    {
        return ExternalWalletResource::collection($this->listExternalWalletsAction->execute());
    }
}
