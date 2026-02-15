<?php

namespace App\Domain\Setting\Services;

use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Models\TransactionStatus;
use App\Domain\Setting\Models\Setting;
use App\Domain\Setting\DataTransferObjects\SettingData;

class SettingService
{
    public function listSettings()
    {
        return Setting::all();
    }

    public function updateSettings(SettingData $data): void
    {
        // Check for pending transactions
        $hasPending = Transaction::where('transaction_status_id', TransactionStatus::getId(TransactionStatus::CODE_PENDING))
            ->exists();

        if ($hasPending) {
            abort(400, 'Sorry, it is not able to update due to pending approval records.');
        }

        foreach ($data->settings as $item) {
            Setting::where('key', $item['key'])->update(['value' => $item['value']]);
        }
    }
}
