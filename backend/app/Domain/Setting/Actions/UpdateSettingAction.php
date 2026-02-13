<?php

namespace App\Domain\Setting\Actions;

use App\Domain\Setting\Services\SettingService;
use App\Domain\Setting\DataTransferObjects\SettingData;

class UpdateSettingAction
{
    public function __construct(
        protected SettingService $settingService
    ) {
    }

    public function execute(SettingData $data): void
    {
        $this->settingService->updateSettings($data);
    }
}
