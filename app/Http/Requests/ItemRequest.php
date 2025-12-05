<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'number' => 'required|integer|min:1',
            'transaction_id' => 'required|exists:transactions,id',
            'credit_account_id' => 'nullable|exists:accounts,id',
            'debit_account_id' => 'nullable|exists:accounts,id',
            'description' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'amount' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ];
    }
}
