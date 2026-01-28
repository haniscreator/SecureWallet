<?php

namespace App\Domain\Wallet\DataTransferObjects;

class WalletData
{
    public function __construct(
        public readonly string $name,
        public readonly int $currency_id,
        public readonly ?bool $status = null,
        public readonly ?float $initial_balance = null,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            currency_id: (int) $data['currency_id'],
            status: isset($data['status']) ? (bool) $data['status'] : null,
            initial_balance: isset($data['initial_balance']) ? (float) $data['initial_balance'] : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'currency_id' => $this->currency_id,
            'status' => $this->status,
            'initial_balance' => $this->initial_balance,
        ], fn($value) => !is_null($value));
    }
}
