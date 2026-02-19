<?php

namespace App\Domain\Transaction\Actions;

use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Services\TransferService;
use App\Domain\User\Models\User;

class RejectTransferAction
{
    public function __construct(
        protected TransferService $transferService
    ) {
    }

    public function execute(Transaction $transaction, User $rejector, string $reason): Transaction
    {
        return $this->transferService->rejectTransfer($transaction, $rejector, $reason);
    }
}
