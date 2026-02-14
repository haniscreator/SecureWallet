<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Currency\Models\Currency;

class WalletValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $currency;
    protected $wallet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->currency = Currency::firstOrCreate(
            ['code' => 'USD'],
            ['name' => 'US Dollar', 'symbol' => '$']
        );

        // Create a wallet with a specific address for testing
        $this->wallet = Wallet::factory()->create([
            'currency_id' => $this->currency->id,
            'address' => '0xValidAddress123',
            'status' => true,
        ]);
        $this->wallet->users()->attach($this->user);
    }

    public function test_validate_address_valid()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/wallets/validate-address?address=0xValidAddress123&currency_id=' . $this->currency->id);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'exists' => true,
                    'valid' => true,
                    'message' => "Verified Internal Wallet: {$this->wallet->name}",
                ]
            ]);
    }

    public function test_validate_address_invalid_external()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/wallets/validate-address?address=0xUnknownAddress');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'exists' => false,
                    'valid' => false,
                    'message' => 'Invalid External Wallet Address',
                ]
            ]);
    }

    public function test_validate_address_inactive()
    {
        $inactiveWallet = Wallet::factory()->create([
            'address' => '0xInactiveAddress',
            'status' => false,
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/wallets/validate-address?address=0xInactiveAddress');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'exists' => true,
                    'valid' => false,
                    'message' => 'Wallet is inactive/frozen.',
                ]
            ]);
    }

    public function test_validate_address_currency_mismatch()
    {
        $eur = Currency::firstOrCreate(
            ['code' => 'EUR'],
            ['name' => 'Euro', 'symbol' => '€']
        );

        $eurWallet = Wallet::factory()->create([
            'address' => '0xEurAddress',
            'currency_id' => $eur->id,
            'status' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/wallets/validate-address?address=0xEurAddress&currency_id=' . $this->currency->id);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'exists' => true,
                    'valid' => false,
                    'message' => "Currency mismatch. Wallet is EUR.",
                ]
            ]);
    }
}
