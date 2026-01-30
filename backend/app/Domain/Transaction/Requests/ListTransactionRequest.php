<?php

namespace App\Domain\Transaction\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'nullable|in:credit,debit',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'reference' => 'nullable|string',
        ];
    }
}
