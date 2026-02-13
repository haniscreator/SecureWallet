<?php

namespace App\Domain\Currency\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateCurrencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:10|unique:currencies,code,' . $this->route('currency'),
            'symbol' => 'nullable|string|max:10',
            'status' => 'sometimes|boolean',
        ];
    }
}
