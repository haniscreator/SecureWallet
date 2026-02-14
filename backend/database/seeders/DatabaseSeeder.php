<?php

namespace Database\Seeders;

use App\Domain\User\Models\User;
use App\Domain\User\Models\UserRole;
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
        // Seed Roles
        $adminRole = UserRole::create(['name' => 'admin', 'label' => 'Administrator']);
        $userRole = UserRole::create(['name' => 'user', 'label' => 'User']);
        $managerRole = UserRole::create(['name' => 'manager', 'label' => 'Manager']);

        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => '12345678',
            'role_id' => $adminRole->id,
        ]);

        $manager = User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager1@gmail.com',
            'password' => '12345678', // Explicitly requested by user
            'role_id' => $managerRole->id,
        ]);

        $wManager1 = Wallet::factory()->create([
            'name' => 'Manager Wallet-1',
            'currency_id' => $usd->id,
        ]);
        $wManager1->users()->attach($manager->id);

        $wManager2 = Wallet::factory()->create([
            'name' => 'Manager Wallet-2',
            'currency_id' => $eur->id,
        ]);
        $wManager2->users()->attach($manager->id);

        $sgd = Currency::where('code', 'SGD')->first();
        $wManager3 = Wallet::factory()->create([
            'name' => 'Manager Wallet-3',
            'currency_id' => $sgd->id,
        ]);
        $wManager3->users()->attach($manager->id);

        $w1 = Wallet::factory()->create([
            'name' => 'Admin Wallet',
            'currency_id' => $usd->id,
        ]);
        $w1->users()->attach($admin->id);

        $user1 = User::factory()->create([
            'name' => 'User One',
            'email' => 'user1@gmail.com',
            'password' => '$2y$12$.AjnzZci3HFiXu0CJVdhjuuiFc.BkxINYgUwmR0v62Jy35LmNEmj2', // Explicitly requested hash
            'role_id' => $userRole->id,
        ]);

        $w2 = Wallet::factory()->create([
            'name' => 'Wallet-1',
            'currency_id' => $usd->id,
        ]);
        $w2->users()->attach($user1->id);

        $user2 = User::factory()->create([
            'name' => 'User Two',
            'email' => 'user2@gmail.com',
            'password' => '$2y$12$.AjnzZci3HFiXu0CJVdhjuuiFc.BkxINYgUwmR0v62Jy35LmNEmj2', // Explicitly requested hash
            'role_id' => $userRole->id,
        ]);

        // Wallet-2 for User 2 with SGD
        $sgd = Currency::where('code', 'SGD')->first();
        $w3 = Wallet::factory()->create([
            'name' => 'Wallet-2',
            'currency_id' => $sgd->id,
        ]);
        $w3->users()->attach($user2->id);

        // 3. Call Other Seeders
        $this->call([
            ExternalWalletSeeder::class,
            SettingSeeder::class,
            TransactionStatusSeeder::class,
            ExtraTransactionSeeder::class,
            Wallet11Seeder::class,
        ]);
    }
}



