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
            'contract_id' => 'nullable|exists:contracts,id',
            'customer_id' => 'required',
            'user_id' => 'required',
            'date' => 'required',
            'policy_number' => 'required|string',
            'customs_declaration' => 'required|string',
            'customs_declaration_date' => 'required|date',
        ];
    }
}
