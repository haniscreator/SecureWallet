<?php

namespace Tests\Feature;

use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Resources\TransactionResource;
use App\Domain\Wallet\Models\ExternalWallet;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_resource_includes_external_wallet_address()
    {
        $currency = \App\Domain\Currency\Models\Currency::factory()->create(['code' => 'TR1']);
        $user = User::factory()->create();
        $sourceWallet = Wallet::factory()->create(['currency_id' => $currency->id]);
        $externalWallet = ExternalWallet::factory()->create([
            'address' => '0x1234567890abcdef1234567890abcdef12345678',
            'name' => 'Test External',
            'currency_id' => $currency->id
        ]);

        $transaction = Transaction::factory()->create([
            'from_wallet_id' => $sourceWallet->id,
            'to_wallet_id' => null,
            'external_wallet_id' => $externalWallet->id,
            'amount' => 100,
            'transaction_status_id' => \App\Domain\Transaction\Models\TransactionStatus::factory()->create()->id
        ]);

        $resource = new TransactionResource($transaction);
        $json = $resource->response()->getData(true);

        $this->assertEquals('Test External', $json['data']['to_wallet']['name']);
        $this->assertEquals($externalWallet->address, $json['data']['to_wallet']['address']);
        $this->assertTrue($json['data']['to_wallet']['is_external']);
    }

    public function test_resource_handles_internal_wallet()
    {
        $currency = \App\Domain\Currency\Models\Currency::factory()->create(['code' => 'TR2']);
        $sourceWallet = Wallet::factory()->create(['currency_id' => $currency->id]);
        $destWallet = Wallet::factory()->create(['name' => 'Internal Dest', 'currency_id' => $currency->id]);

        $transaction = Transaction::factory()->create([
            'from_wallet_id' => $sourceWallet->id,
            'to_wallet_id' => $destWallet->id,
            'external_wallet_id' => null,
            'amount' => 100,
            'transaction_status_id' => \App\Domain\Transaction\Models\TransactionStatus::factory()->create()->id
        ]);

        $resource = new TransactionResource($transaction);
        $json = $resource->response()->getData(true);

        $this->assertEquals('Internal Dest', $json['data']['to_wallet']['name']);
        $this->assertFalse($json['data']['to_wallet']['is_external']);
        $this->assertArrayNotHasKey('address', $json['data']['to_wallet']);
    }
}
