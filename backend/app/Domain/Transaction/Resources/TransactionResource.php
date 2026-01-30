<?php

namespace App\Domain\Transaction\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'type' => $this->type,
            'reference' => $this->reference,
            'from_wallet_id' => $this->from_wallet_id,
            'to_wallet_id' => $this->to_wallet_id,
            'from_wallet' => $this->fromWallet ? [
                'name' => $this->fromWallet->name,
                'currency' => [
                    'symbol' => $this->fromWallet->currency->symbol ?? '$',
                ]
            ] : null,
            'to_wallet' => $this->toWallet ? [
                'name' => $this->toWallet->name,
                'currency' => [
                    'symbol' => $this->toWallet->currency->symbol ?? '$',
                ]
            ] : null,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
