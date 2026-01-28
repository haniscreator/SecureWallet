<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'currency_id' => $this->currency_id,
            'currency' => new CurrencyResource($this->whenLoaded('currency')),
            'status' => $this->status,
            'balance' => $this->whenAppended('balance'),
            'users' => UserResource::collection($this->whenLoaded('users')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
