<?php

namespace App\Http\Requests\User;

use App\Domain\User\Models\User;
use Illuminate\Foundation\Http\FormRequest;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'role' => 'nullable|string|in:user,admin',
            // Email/Password updates usually handled separately or here with extra checks, 
            // but keeping minimal based on current controller logic.
        ];
    }
}
