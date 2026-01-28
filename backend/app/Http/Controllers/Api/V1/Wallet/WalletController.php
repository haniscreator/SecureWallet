<?php

namespace App\Http\Controllers\Api\V1\Wallet;

use App\Http\Controllers\Controller;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Wallet\Services\WalletService;
use App\Domain\Wallet\Actions\CreateWalletAction;
use App\Domain\Wallet\Actions\UpdateWalletStatusAction;
use App\Domain\Wallet\Actions\AssignWalletAction;
use Illuminate\Http\Request;
use App\Http\Requests\Wallet\StoreWalletRequest;
use App\Http\Requests\Wallet\UpdateWalletRequest;
use App\Http\Requests\Wallet\UpdateWalletStatusRequest;
use App\Http\Requests\Wallet\AssignUserToWalletRequest;
use App\Domain\Wallet\Actions\ListWalletsAction;
use App\Domain\Wallet\Actions\GetWalletAction;
use App\Http\Resources\WalletResource;

class WalletController extends Controller
{
    public function __construct(
        protected CreateWalletAction $createWalletAction,
        protected UpdateWalletStatusAction $updateWalletStatusAction,
        protected AssignWalletAction $assignWalletAction,
        protected \App\Domain\Wallet\Actions\UpdateWalletAction $updateWalletAction,
        protected ListWalletsAction $listWalletsAction,
        protected GetWalletAction $getWalletAction
    ) {
    }

    public function index(Request $request)
    {
        if ($request->user()->cannot('viewAny', Wallet::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return WalletResource::collection($this->listWalletsAction->execute($request->user()));
    }

    public function show(Request $request, $id)
    {
        $wallet = Wallet::findOrFail($id);

        if ($request->user()->cannot('view', $wallet)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return new WalletResource($this->getWalletAction->execute($wallet)->append('balance'));
    }

    public function store(StoreWalletRequest $request)
    {
        $wallet = $this->createWalletAction->execute(\App\Domain\Wallet\DataTransferObjects\WalletData::fromRequest($request->validated()));

        return response()->json(['message' => 'Wallet created', 'wallet' => new WalletResource($wallet->append('balance'))]);
    }

    public function update(UpdateWalletRequest $request, $id)
    {
        $wallet = Wallet::findOrFail($id);

        // Authorization handled in Form Request, but we need the model here to pass to action
        // Technically FormRequest loaded it too, but fetching again is cheap and safe or use route binding.

        $wallet = $this->updateWalletAction->execute($wallet, \App\Domain\Wallet\DataTransferObjects\WalletData::fromRequest($request->validated()));

        return response()->json(['message' => 'Wallet updated', 'wallet' => new WalletResource($wallet)]);
    }

    public function updateStatus(UpdateWalletStatusRequest $request, $id)
    {
        $wallet = Wallet::findOrFail($id);

        $wallet = $this->updateWalletStatusAction->execute($wallet, $request->validated()['status']);

        return response()->json(['message' => 'Wallet status updated', 'wallet' => new WalletResource($wallet)]);
    }

    public function assignUser(AssignUserToWalletRequest $request, $id)
    {
        $wallet = Wallet::findOrFail($id);

        $this->assignWalletAction->execute($wallet, $request->validated()['user_ids']);

        return response()->json(['message' => 'Users assigned to wallet']);
    }
}
