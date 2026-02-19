<?php

namespace App\Domain\Transaction\Actions;

use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Services\TransferService;
use App\Domain\Transaction\DataTransferObjects\TransferData;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\User\Models\User;

class InitiateTransferAction
{
    public function __construct(
        protected TransferService $transferService
    ) {
    }

    public function execute(TransferData $data, User $initiator): Transaction
    {
        $target = $data->type === 'internal' ? $data->to_wallet_id : $data->to_address;
        $sourceWallet = Wallet::findOrFail($data->source_wallet_id);

        return $this->transferService->initiateTransfer(
            $sourceWallet,
            $data->type,
            $target,
            $data->amount,
            $initiator,
            $data->description
        );
    }
}
