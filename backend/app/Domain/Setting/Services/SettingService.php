<?php

namespace App\Domain\Setting\Services;

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
        foreach ($data->settings as $item) {
            Setting::where('key', $item['key'])->update(['value' => $item['value']]);
        }
    }
}
