<?php

namespace Tests\Feature;

use App\Domain\Currency\Models\Currency;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\ExternalWallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExternalWalletValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_validate_external_wallet_address()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $usd = Currency::firstOrCreate(['code' => 'USD'], ['name' => 'US Dollar', 'symbol' => '$']);
        $address = '0x71C7656EC7ab88b098defB751B7401B5f6d8976F';

        $wallet = ExternalWallet::factory()->create([
            'address' => $address,
            'currency_id' => $usd->id,
            'status' => true,
        ]);

        $response = $this->getJson("/api/wallets/validate-address?address={$address}&currency_id={$usd->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.valid', true);
        $response->assertJsonPath('data.exists', true);
        $response->assertJsonPath('data.message', "Verified External Wallet: {$wallet->name}");
    }

    public function test_can_detect_currency_mismatch_for_external_wallet()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $usd = Currency::firstOrCreate(['code' => 'USD'], ['name' => 'US Dollar', 'symbol' => '$']);
        $eur = Currency::firstOrCreate(['code' => 'EUR'], ['name' => 'Euro', 'symbol' => '€']);

        $wallet = ExternalWallet::factory()->create([
            'currency_id' => $eur->id,
            'status' => true,
        ]);

        $response = $this->getJson("/api/wallets/validate-address?address={$wallet->address}&currency_id={$usd->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.valid', false);
        $response->assertJsonPath('data.exists', true);
        $response->assertJsonPath('data.message', "Currency mismatch. Destination wallet is EUR, Source is USD.");
    }

    public function test_can_detect_inactive_external_wallet()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $usd = Currency::firstOrCreate(['code' => 'USD'], ['name' => 'US Dollar', 'symbol' => '$']);

        $wallet = ExternalWallet::factory()->create([
            'currency_id' => $usd->id,
            'status' => false,
        ]);

        $response = $this->getJson("/api/wallets/validate-address?address={$wallet->address}&currency_id={$usd->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.valid', false);
        $response->assertJsonPath('data.exists', true);
        $response->assertJsonPath('data.message', "Destination wallet is inactive/frozen.");
    }
}
