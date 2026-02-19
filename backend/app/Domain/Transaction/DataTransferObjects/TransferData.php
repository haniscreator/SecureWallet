<?php

namespace App\Domain\Transaction\DataTransferObjects;

class TransferData
{
    public function __construct(
        public readonly int $source_wallet_id,
        public readonly string $type, // internal, external
        public readonly ?int $to_wallet_id,
        public readonly ?string $to_address,
        public readonly float $amount,
        public readonly ?string $description,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            source_wallet_id: (int) $data['source_wallet_id'],
            type: $data['type'],
            to_wallet_id: isset($data['to_wallet_id']) ? (int) $data['to_wallet_id'] : null,
            to_address: $data['to_address'] ?? null,
            amount: (float) $data['amount'],
            description: $data['description'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'source_wallet_id' => $this->source_wallet_id,
            'type' => $this->type,
            'to_wallet_id' => $this->to_wallet_id,
            'to_address' => $this->to_address,
            'amount' => $this->amount,
            'description' => $this->description,
        ];
    }
}
