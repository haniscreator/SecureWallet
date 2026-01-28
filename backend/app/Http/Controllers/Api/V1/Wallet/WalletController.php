<?php

namespace App\Http\Controllers\Api\V1\Wallet;

use App\Http\Controllers\Controller;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Wallet\Services\WalletService;
use App\Domain\Wallet\Actions\CreateWalletAction;
use App\Domain\Wallet\Actions\UpdateWalletStatusAction;
use App\Domain\Wallet\Actions\AssignWalletAction;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        protected WalletService $walletService,
        protected CreateWalletAction $createWalletAction,
        protected UpdateWalletStatusAction $updateWalletStatusAction,
        protected AssignWalletAction $assignWalletAction,
        protected \App\Domain\Wallet\Actions\UpdateWalletAction $updateWalletAction
    ) {
    }

    public function index(Request $request)
    {
        if ($request->user()->cannot('viewAny', Wallet::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($this->walletService->listWallets($request->user()));
    }

    public function show(Request $request, $id)
    {
        $wallet = Wallet::findOrFail($id);

        if ($request->user()->cannot('view', $wallet)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($this->walletService->getWallet($wallet)->append('balance'));
    }

    public function store(Request $request)
    {
        if ($request->user()->cannot('create', Wallet::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'currency_id' => 'required|exists:currencies,id',
            'initial_balance' => 'sometimes|numeric|min:0',
        ]);

        $wallet = $this->createWalletAction->execute($validated);

        return response()->json(['message' => 'Wallet created', 'wallet' => $wallet->append('balance')]);
    }

    public function update(Request $request, $id)
    {
        $wallet = Wallet::findOrFail($id);

        if ($request->user()->cannot('update', $wallet)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $wallet = $this->updateWalletAction->execute($wallet, $validated);

        return response()->json(['message' => 'Wallet updated', 'wallet' => $wallet]);
    }

    public function updateStatus(Request $request, $id)
    {
        $wallet = Wallet::findOrFail($id);

        // Custom policy method 'freeze'? Or update?
        if ($request->user()->cannot('freeze', $wallet)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|boolean', // true/1 or false/0
        ]);

        $wallet = $this->updateWalletStatusAction->execute($wallet, $validated['status']);

        return response()->json(['message' => 'Wallet status updated', 'wallet' => $wallet]);
    }

    public function assignUser(Request $request, $id)
    {
        $wallet = Wallet::findOrFail($id);

        if ($request->user()->cannot('assignMember', $wallet)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $this->assignWalletAction->execute($wallet, $validated['user_ids']);

        return response()->json(['message' => 'Users assigned to wallet']);
    }
}
