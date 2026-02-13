<?php

namespace Database\Seeders;

use App\Domain\Setting\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([
            'key' => 'transfer_limit',
            'value' => '1000',
            'description' => 'Global transfer limit requiring approval',
        ]);
    }
}
