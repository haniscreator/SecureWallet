<?php

namespace App\Domain\Transaction\DataTransferObjects;

use Illuminate\Http\Request;

readonly class TransactionFilterData
{
    public function __construct(
        public ?string $type,
        public ?string $from_date,
        public ?string $to_date,
        public ?string $reference,
        public ?int $status_id = null,
        public ?int $per_page = 15,
        public ?string $sort_by = 'created_at',
        public ?string $sort_dir = 'desc',
        public ?string $timezone = 'UTC'
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            type: $request->input('type'),
            from_date: $request->input('from_date'),
            to_date: $request->input('to_date'),
            reference: $request->input('reference'),
            status_id: $request->input('status_id') ? (int) $request->input('status_id') : null,
            per_page: $request->input('per_page'),
            sort_by: $request->input('sort_by', 'created_at'),
            sort_dir: $request->input('sort_dir', 'desc'),
            timezone: $request->input('timezone', 'UTC')
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'type' => $this->type,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'reference' => $this->reference,
            'status_id' => $this->status_id,
            'timezone' => $this->timezone,
        ], fn($v) => !is_null($v) && $v !== '');
    }
}
