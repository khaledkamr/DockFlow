<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required',
            'date' => 'required|date',
            'credit_account_id' => 'required',
            'debit_account_id' => 'required',
            'amount' => 'required',
            'hatching' => 'required|string',
            'description' => 'nullable|string',
            'user_id' => 'required',
        ];
    }
}
