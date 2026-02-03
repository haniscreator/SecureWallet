<?php

namespace App\Domain\Wallet\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Domain\Wallet\Models\Wallet;

class UpdateWalletStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $walletId = $this->route('id');
        $wallet = Wallet::findOrFail($walletId);

        return $this->user()->can('freeze', $wallet);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|boolean',
        ];
    }
}
