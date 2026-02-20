<?php

namespace App\Domain\Setting\Requests;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
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
            'settings' => 'required|array',
            'settings.*.key' => 'required|string|exists:settings,key',
            'settings.*.value' => [
                'required',
                'string',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $index = explode('.', $attribute)[1] ?? null;
                    $key = $index !== null ? $this->input("settings.{$index}.key") : null;

                    if (is_string($key) && str_contains($key, 'limit') && filter_var($value, FILTER_VALIDATE_INT) === false) {
                        $fail("The {$key} value must be an integer.");
                    }
                },
            ],
        ];
    }
}
