<?php

namespace App\Domain\Wallet\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateWalletAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'address' => 'required|string',
            'currency_id' => 'nullable|integer|exists:currencies,id',
        ];
    }
}