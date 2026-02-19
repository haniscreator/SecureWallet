<?php

namespace App\Domain\Transaction\Requests;

use App\Domain\Wallet\Models\Wallet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class InitiateTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        $sourceWallet = Wallet::find($this->source_wallet_id);

        if (!$sourceWallet) {
            return false;
        }

        // Authorization: Check if user owns the wallet
        return $sourceWallet->users->contains($this->user()->id);
    }

    public function rules(): array
    {
        return [
            'source_wallet_id' => 'required|exists:wallets,id',
            'type' => 'required|in:internal,external',
            'to_wallet_id' => 'required_if:type,internal|nullable|exists:wallets,id',
            'to_address' => 'required_if:type,external|nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ];
    }
}
