<?php

namespace App\Domain\Currency\DataTransferObjects;

class CurrencyData
{
    public function __construct(
        public readonly ?string $code = null,
        public readonly ?string $name = null,
        public readonly ?string $symbol = null,
        public readonly ?bool $status = null,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            code: $data['code'] ?? null,
            name: $data['name'] ?? null,
            symbol: $data['symbol'] ?? null,
            status: isset($data['status']) ? (bool) $data['status'] : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'code' => $this->code,
            'name' => $this->name,
            'symbol' => $this->symbol,
            'status' => $this->status,
        ], fn($value) => !is_null($value));
    }
}
