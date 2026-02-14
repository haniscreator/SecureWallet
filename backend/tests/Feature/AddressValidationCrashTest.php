<?php

namespace Tests\Feature;

use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Currency\Models\Currency;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddressValidationCrashTest extends TestCase
{
    use RefreshDatabase;

    public function test_validate_address_crash_reproduction()
    {
        $user = User::factory()->create();
        $currency = Currency::factory()->create(['code' => 'ETH']);

        // Authenticate
        // specific address reported by user
        $address = '0x250e76987d838a75310c34bf422ea9f1AC408acc';

        $this->actingAs($user);

        // Set wallet currency to ETH
        $targetWallet = Wallet::factory()->create([
            'address' => $address,
            'currency_id' => $currency->id
        ]);

        // Create another currency (USD)
        $usd = Currency::firstOrCreate(['code' => 'USD'], ['name' => 'US Dollar', 'symbol' => '$']);

        // Validate with USD currency_id (Should Fail)
        $response = $this->getJson("/api/wallets/validate-address?address={$address}&currency_id={$usd->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.valid', false);
        $response->assertJsonPath('data.message', "Currency mismatch. Wallet is ETH.");
    }
}
