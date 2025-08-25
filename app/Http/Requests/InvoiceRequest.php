<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'policy_id' => 'required',
            'customer_id' => 'required',
            'made_by' => 'required',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'payment_method' => 'required'
        ];
    }
}
