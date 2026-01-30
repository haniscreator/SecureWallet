<?php

namespace App\Domain\User\DataTransferObjects;

class UserData
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $password = null,
        public readonly ?string $role = null,
        public readonly ?bool $status = null,
        public readonly ?array $wallet_ids = null,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            password: $data['password'] ?? null,
            role: $data['role'] ?? null,
            status: isset($data['status']) ? (bool) $data['status'] : null,
            wallet_ids: $data['wallet_ids'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
            'status' => $this->status,
        ], fn($value) => !is_null($value));
    }
}
