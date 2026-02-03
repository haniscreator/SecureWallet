<?php

namespace App\Domain\Wallet\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Domain\Wallet\Models\Wallet;

class UpdateWalletRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // For 'update' endpoint, we need the wallet ID which is in the route
        $walletId = $this->route('id');
        $wallet = Wallet::findOrFail($walletId);

        return $this->user()->can('update', $wallet);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'status' => 'sometimes|boolean',
        ];
    }
}
