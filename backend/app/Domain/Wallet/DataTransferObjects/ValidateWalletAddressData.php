<?php

namespace App\Domain\Wallet\DataTransferObjects;

use Spatie\LaravelData\Data;
use Illuminate\Http\Request;
use App\Domain\Wallet\Requests\ValidateWalletAddressRequest;

class ValidateWalletAddressData
{
    public function __construct(
        public readonly string $address,
        public readonly ?int $currency_id,
    ) {
    }

    public static function fromRequest(ValidateWalletAddressRequest $request): self
    {
        return new self(
            address: $request->validated('address'),
            currency_id: $request->validated('currency_id')
        );
    }
}
