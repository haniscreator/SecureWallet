<?php

namespace Tests\Feature\Wallet;

use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletTransferTargetsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_lists_transfer_targets_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a currency to avoid unique constraint violations on random code generation
        $currency = \App\Domain\Currency\Models\Currency::factory()->create();

        // Create active wallets
        Wallet::factory()->count(3)->create([
            'status' => true,
            'currency_id' => $currency->id
        ]);

        // Create inactive wallet
        Wallet::factory()->create([
            'status' => false,
            'currency_id' => $currency->id
        ]);

        $response = $this->getJson('/api/wallets/transfer-targets');

        // Should return all 4 wallets (3 active + 1 inactive)
        $response->assertStatus(200)
            ->assertJsonCount(4, 'data');
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->getJson('/api/wallets/transfer-targets');

        $response->assertStatus(401);
    }
}
