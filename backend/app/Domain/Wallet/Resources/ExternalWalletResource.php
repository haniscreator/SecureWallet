<?php

namespace App\Domain\Wallet\Resources;

use App\Domain\Currency\Resources\CurrencyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExternalWalletResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'name' => $this->name,
            'currency_id' => $this->currency_id,
            'currency' => new CurrencyResource($this->whenLoaded('currency')),
            'status' => $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
