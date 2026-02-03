<?php

namespace App\Domain\User\Requests;

use App\Domain\User\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $userId = $this->route('id');
        $user = User::findOrFail($userId);

        return $this->user()->can('update', $user);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'role' => 'nullable|string|in:user,admin',
            'status' => 'nullable|boolean',
            'wallet_ids' => 'nullable|array',
            'wallet_ids.*' => 'integer|exists:wallets,id',
            // Email/Password updates usually handled separately or here with extra checks, 
            // but keeping minimal based on current controller logic.
        ];
    }
}
