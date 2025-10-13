<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contract_id' => 'required',
            'customer_id' => 'required',
            'user_id' => 'required',
            'date' => 'required',
            'tax_statement' => 'nullable|string',
            'tax_statement_date' => 'nullable|date',
        ];
    }
}
