<?php

namespace App\Domain\Auth\DataTransferObjects;

class LoginData
{
    public function __construct(
        public readonly ?string $email = null,
        public readonly ?string $password = null,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            email: $data['email'] ?? null,
            password: $data['password'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'email' => $this->email,
            'password' => $this->password,
        ], fn($value) => !is_null($value));
    }
}
