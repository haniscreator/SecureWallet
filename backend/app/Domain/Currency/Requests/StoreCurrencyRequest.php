<?php

namespace App\Domain\Currency\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCurrencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string|unique:currencies,code|max:10',
            'name' => 'required|string|max:255',
            'symbol' => 'nullable|string|max:10',
            'status' => 'sometimes|boolean',
        ];
    }
}
