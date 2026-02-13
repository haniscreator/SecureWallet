<?php

namespace App\Domain\Transaction\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InitiateTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source_wallet_id' => [
                'required',
                'exists:wallets,id',
                function ($attribute, $value, $fail) {
                    if (!$this->user()->wallets()->where('wallets.id', $value)->exists()) {
                        $fail('You do not have access to this wallet.');
                    }
                },
            ],
            'recipient_email' => 'required|email|exists:users,email',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ];
    }
}
