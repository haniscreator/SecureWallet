<?php

namespace Database\Seeders;

use App\Domain\User\Models\User;
use App\Domain\Currency\Models\Currency;
use App\Domain\Wallet\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Currencies
        $this->call(CurrencySeeder::class);

        $usd = Currency::where('code', 'USD')->first();
        $eur = Currency::where('code', 'EUR')->first();

        // 2. Create Users & Wallets
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => '12345678',
            'role' => 'admin',
        ]);

        $w1 = Wallet::factory()->create([
            'name' => 'Admin Wallet',
            'currency_id' => $usd->id,
        ]);
        $w1->users()->attach($admin->id);

        $user1 = User::factory()->create([
            'name' => 'User One',
            'email' => 'user1@gmail.com',
            'password' => '12345678',
            'role' => 'user',
        ]);

        $w2 = Wallet::factory()->create([
            'name' => 'User One Wallet',
            'currency_id' => $usd->id,
        ]);
        $w2->users()->attach($user1->id);

        // Other users (optional wallet creation if needed for tests, but we only need 2 wallets for ExtraTransactionSeeder)
        User::factory()->create([
            'name' => 'User Two',
            'email' => 'user2@gmail.com',
            'password' => '12345678',
            'role' => 'user',
        ]);

        // 3. Call Other Seeders
        $this->call([
            ExternalWalletSeeder::class,
            SettingSeeder::class,
            TransactionStatusSeeder::class,
            ExtraTransactionSeeder::class,
        ]);
    }
}



