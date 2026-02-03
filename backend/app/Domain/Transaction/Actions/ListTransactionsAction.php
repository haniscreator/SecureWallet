<?php

namespace App\Domain\Transaction\Actions;

use App\Domain\Transaction\DataTransferObjects\TransactionFilterData;
use App\Domain\Transaction\Services\TransactionService;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\Wallet;
use Illuminate\Pagination\LengthAwarePaginator;

class ListTransactionsAction
{
    public function __construct(
        protected TransactionService $transactionService
    ) {
    }

    public function execute(User $user, TransactionFilterData $filters, ?Wallet $wallet = null): LengthAwarePaginator
    {
        if ($wallet) {
            return $this->transactionService->listTransactions($wallet, $filters);
        }

        return $this->transactionService->listAllTransactions($user, $filters);
    }

    public function service(): TransactionService
    {
        return $this->transactionService;
    }
}
