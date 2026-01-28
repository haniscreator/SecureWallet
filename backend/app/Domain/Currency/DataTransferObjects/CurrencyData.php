<?php

namespace App\Domain\Currency\DataTransferObjects;

class CurrencyData
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly string $symbol,
        public readonly ?bool $status = null,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            code: $data['code'],
            name: $data['name'],
            symbol: $data['symbol'],
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
