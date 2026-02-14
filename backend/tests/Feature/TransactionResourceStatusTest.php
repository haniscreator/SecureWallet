<?php

namespace Tests\Feature;

use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Models\TransactionStatus;
use App\Domain\Transaction\Resources\TransactionResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionResourceStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_resource_includes_status_and_id()
    {
        $status = TransactionStatus::firstOrCreate(['code' => 'pending', 'name' => 'Pending']);
        $transaction = Transaction::factory()->create([
            'transaction_status_id' => $status->id,
            'amount' => 100,
        ]);

        $resource = new TransactionResource($transaction);
        $json = $resource->response()->getData(true);

        $this->assertEquals($status->id, $json['data']['transaction_status_id']);
        $this->assertEquals('pending', $json['data']['status']['code']);
        $this->assertEquals('Pending', $json['data']['status']['name']);
    }
}
