<?php

namespace App\Domain\Transaction\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user->hasRole('manager') || $user->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'reason' => 'required|string|max:255',
        ];
    }
}
