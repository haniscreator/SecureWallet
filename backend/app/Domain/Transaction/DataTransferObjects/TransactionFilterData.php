<?php

namespace App\Domain\Transaction\DataTransferObjects;

use Illuminate\Http\Request;

readonly class TransactionFilterData
{
    public function __construct(
        public ?string $type,
        public ?string $from_date,
        public ?string $to_date,
        public ?string $reference
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            type: $request->input('type'),
            from_date: $request->input('from_date'),
            to_date: $request->input('to_date'),
            reference: $request->input('reference')
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'type' => $this->type,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'reference' => $this->reference,
        ], fn($v) => !is_null($v) && $v !== '');
    }
}
