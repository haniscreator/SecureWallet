<?php

namespace App\Domain\Wallet\Requests;

use App\Domain\Wallet\Models\Wallet;
use Illuminate\Foundation\Http\FormRequest;

class StoreWalletRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user can create wallets via Policy
        return $this->user()->can('create', Wallet::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'currency_id' => 'required|exists:currencies,id',
            'initial_balance' => 'sometimes|numeric|min:0',
        ];
    }
}
