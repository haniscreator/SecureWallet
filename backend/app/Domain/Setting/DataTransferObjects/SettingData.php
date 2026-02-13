<?php

namespace App\Domain\Setting\DataTransferObjects;

use Illuminate\Http\Request;

class SettingData
{
    public function __construct(
        public array $settings
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            settings: $request->input('settings')
        );
    }
}
